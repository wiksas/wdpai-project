<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\TreeRepository;
use App\Repository\UserStateRepository;
use App\Support\PolishDate;
use DateTimeImmutable;

/**
 * Kolekcja drzewek użytkownika: dane bazowe z `TreeRepository` nałożone na zapisany stan
 * (podlania i korekty z bieżącego dnia), filtry, statystyki i rejestrowanie zabiegów.
 */
final class CollectionService
{
    /** Wilgotność bryły po podlaniu. */
    private const WATERED_MOISTURE = 88;

    public function __construct(
        private readonly TreeRepository $trees,
        private readonly UserStateRepository $states,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(int $userId): array
    {
        $state = $this->states->get($userId);
        $now = PolishDate::now();

        return array_map(fn (array $tree): array => $this->applyState($tree, $state, $now), $this->trees->findAll());
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $userId, int $id): ?array
    {
        $tree = $this->trees->findById($id);
        if ($tree === null) {
            return null;
        }

        return $this->applyState($tree, $this->states->get($userId), PolishDate::now());
    }

    /**
     * @param list<array<string, mixed>> $trees
     * @param array{category?: string, status?: string, q?: string} $criteria
     * @return list<array<string, mixed>>
     */
    public function filter(array $trees, array $criteria): array
    {
        $category = $criteria['category'] ?? '';
        $status = $criteria['status'] ?? '';
        $query = mb_strtolower(trim($criteria['q'] ?? ''));

        return array_values(array_filter($trees, static function (array $tree) use ($category, $status, $query): bool {
            if ($category !== '' && $tree['category'] !== $category) {
                return false;
            }

            if ($status === 'attention' && !in_array($tree['status'], ['attention', 'critical'], true)) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $tree['name'], $tree['kanji'], $tree['species'], $tree['commonName'],
                $tree['style'], $tree['styleDescription'], $tree['pot'],
            ]));

            return str_contains($haystack, $query);
        }));
    }

    /**
     * Liczniki do zakładek filtrów.
     *
     * @param list<array<string, mixed>> $trees
     * @return array{all: int, conifer: int, deciduous: int, attention: int}
     */
    public function counts(array $trees): array
    {
        return [
            'all' => count($trees),
            'conifer' => count(array_filter($trees, static fn (array $t): bool => $t['category'] === 'conifer')),
            'deciduous' => count(array_filter($trees, static fn (array $t): bool => $t['category'] === 'deciduous')),
            'attention' => count(array_filter($trees, static fn (array $t): bool => in_array($t['status'], ['attention', 'critical'], true))),
        ];
    }

    /**
     * Podsumowanie do panelu bocznego i statystyk.
     *
     * @return array{total: int, thirsty: int, wired: int, health: int, healthLabel: string, capacity: int}
     */
    public function summary(int $userId): array
    {
        return $this->summarize($this->all($userId));
    }

    /**
     * @param list<array<string, mixed>> $trees
     * @return array{total: int, thirsty: int, wired: int, health: int, healthLabel: string, capacity: int}
     */
    public function summarize(array $trees): array
    {
        $critical = count(array_filter($trees, static fn (array $t): bool => $t['status'] === 'critical'));
        $attention = count(array_filter($trees, static fn (array $t): bool => $t['status'] === 'attention'));
        $health = max(0, 100 - 7 * $critical - 4 * $attention);

        return [
            'total' => count($trees),
            'thirsty' => $critical + $attention,
            'wired' => count(array_filter($trees, static fn (array $t): bool => $t['care'] !== null)),
            'health' => $health,
            'healthLabel' => $health >= 90 ? 'Optymalna' : ($health >= 75 ? 'Dobra' : 'Wymaga uwagi'),
            'capacity' => 12,
        ];
    }

    /**
     * Rejestruje zabieg przy drzewku (`water` lub `adjust`). Zwraca zaktualizowane drzewko albo null,
     * gdy drzewko nie istnieje lub zabieg nie jest dla niego dostępny.
     *
     * @return array<string, mixed>|null
     */
    public function care(int $userId, int $treeId, string $type): ?array
    {
        $tree = $this->trees->findById($treeId);
        if ($tree === null || !in_array($type, ['water', 'adjust'], true)) {
            return null;
        }

        if ($type === 'adjust' && $tree['care'] === null) {
            return null;
        }

        $timestamp = PolishDate::now()->getTimestamp();
        $state = $this->states->update($userId, static function (array $state) use ($treeId, $type, $timestamp): array {
            $state['care'][(string) $treeId][$type] = $timestamp;

            return $state;
        });

        return $this->applyState($tree, $state, PolishDate::now());
    }

    /**
     * Nakłada na drzewko zabiegi zapisane „dzisiaj” (kolejnego dnia stan wraca do bazowego).
     *
     * @param array<string, mixed> $tree
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function applyState(array $tree, array $state, DateTimeImmutable $now): array
    {
        $tree['done'] = null;
        $care = $state['care'][(string) $tree['id']] ?? [];
        $today = $now->format('Y-m-d');

        $wateredAt = $this->todayTimestamp($care['water'] ?? null, $now);
        if ($wateredAt !== null && $tree['actionType'] === 'water') {
            $tree['moisture'] = self::WATERED_MOISTURE;
            $tree['moistureLabel'] = 'Optymalna';
            $tree['status'] = 'ideal';
            $tree['statusLabel'] = 'Idealny';
            $tree['alert'] = null;
            $tree['action'] = null;
            $tree['done'] = 'water';
            $tree['lastWatering'] = 'dzisiaj, ' . $wateredAt->format('H:i');
        }

        $adjustedAt = $this->todayTimestamp($care['adjust'] ?? null, $now);
        if ($adjustedAt !== null && $tree['actionType'] === 'adjust') {
            $tree['care'] = null;
            $tree['action'] = null;
            $tree['done'] = 'adjust';
            $tree['status'] = 'ideal';
            $tree['statusLabel'] = 'Skorygowano';
        }

        $tree['_date'] = $today;

        return $tree;
    }

    private function todayTimestamp(mixed $timestamp, DateTimeImmutable $now): ?DateTimeImmutable
    {
        if (!is_int($timestamp)) {
            return null;
        }

        $moment = $now->setTimestamp($timestamp);

        return $moment->format('Y-m-d') === $now->format('Y-m-d') ? $moment : null;
    }
}

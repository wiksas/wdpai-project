<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\SensorRepository;
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
    public const CAPACITY = 12;
    public const FIRST_CUSTOM_ID = 100;

    /** Wilgotność bryły po podlaniu. */
    private const WATERED_MOISTURE = 88;

    /** Odczyt starszy niż 12 godzin przestaje wpływać na stan okazu (czujnik mógł się rozładować). */
    private const SENSOR_FRESH_SECONDS = 12 * 3600;
    private const SENSOR_OK = 40;
    private const SENSOR_CRITICAL = 25;

    public function __construct(
        private readonly TreeRepository $trees,
        private readonly UserStateRepository $states,
        private readonly SensorRepository $sensors,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(int $userId): array
    {
        $state = $this->states->get($userId);
        $now = PolishDate::now();

        $sensors = $this->sensors->latestForUser($userId);

        return array_map(fn (array $tree): array => $this->applyState($tree, $state, $now, $sensors[$tree['id']] ?? null), $this->owned($state));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $userId, int $id): ?array
    {
        $state = $this->states->get($userId);
        $tree = $this->ownedById($state, $id);

        return $tree === null ? null : $this->applyState($tree, $state, PolishDate::now(), $this->sensors->latestForUser($userId)[$id] ?? null);
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
     * Drzewka z zabiegiem do wykonania, od najpilniejszego (krytyczne, wymagające uwagi, formowanie).
     *
     * @param list<array<string, mixed>> $trees
     * @return list<array<string, mixed>>
     */
    public function needingCare(array $trees): array
    {
        $rank = ['critical' => 0, 'attention' => 1];
        $due = array_values(array_filter($trees, static fn (array $t): bool => $t['action'] !== null));
        usort($due, static fn (array $a, array $b): int => ($rank[$a['status']] ?? 2) <=> ($rank[$b['status']] ?? 2));

        return $due;
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
            'capacity' => self::CAPACITY,
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
        $tree = $this->ownedById($this->states->get($userId), $treeId);
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

        return $this->applyState($tree, $state, PolishDate::now(), $this->sensors->latestForUser($userId)[$treeId] ?? null);
    }

    /**
     * Dodaje własny okaz użytkownika. Zwraca go albo null, gdy kolekcja jest pełna.
     *
     * @param array{name: string, commonName: string, species: string, category: string, style: string, pot: string, age: int} $data
     * @return array<string, mixed>|null
     */
    public function add(int $userId, array $data, ?string $photoFile): ?array
    {
        $created = null;
        $this->states->update($userId, function (array $state) use ($data, $photoFile, &$created): array {
            if (count($this->owned($state)) >= self::CAPACITY) {
                return $state;
            }

            $created = TreeRepository::custom(max([99, ...array_column($state['customTrees'], 'id')]) + 1, $data, $photoFile);
            $state['customTrees'][] = $created;

            return $state;
        });

        return $created === null ? null : $this->find($userId, $created['id']);
    }

    /**
     * Usuwa okaz z kolekcji użytkownika razem z jego zabiegami i zdjęciami. Przykładowe okazy są tylko ukrywane
     * (dla innych kont zostają). Zwraca usunięty okaz i pliki zdjęć do skasowania albo null, gdy okazu nie ma.
     *
     * @return array{tree: array<string, mixed>, files: list<string>}|null
     */
    public function remove(int $userId, int $treeId): ?array
    {
        $result = null;
        $this->states->update($userId, function (array $state) use ($treeId, &$result): array {
            $tree = $this->ownedById($state, $treeId);
            if ($tree === null) {
                return $state;
            }

            $files = array_values(array_filter(array_map(
                static fn (array $photo): string => (int) ($photo['treeId'] ?? 0) === $treeId ? (string) ($photo['file'] ?? '') : '',
                $state['photos'],
            )));
            if (is_string($tree['photoFile'] ?? null)) {
                $files[] = $tree['photoFile'];
            }
            foreach ($state['scans'] as $scan) {
                if ((int) ($scan['treeId'] ?? 0) === $treeId && is_string($scan['file'] ?? null)) {
                    $files[] = $scan['file'];
                }
            }

            if ($treeId >= self::FIRST_CUSTOM_ID) {
                $state['customTrees'] = array_values(array_filter($state['customTrees'], static fn (array $t): bool => $t['id'] !== $treeId));
            } else {
                $state['removedTrees'][] = $treeId;
            }
            unset($state['care'][(string) $treeId]);
            $state['photos'] = array_values(array_filter($state['photos'], static fn (array $photo): bool => (int) ($photo['treeId'] ?? 0) !== $treeId));
            $state['scans'] = array_values(array_filter($state['scans'], static fn (array $scan): bool => (int) ($scan['treeId'] ?? 0) !== $treeId));

            $result = ['tree' => $tree, 'files' => $files];

            return $state;
        });

        if ($result !== null) {
            $this->sensors->delete($userId, $treeId);
        }

        return $result;
    }

    /**
     * Okazy użytkownika: przykładowe bez ukrytych oraz własne.
     *
     * @param array<string, mixed> $state
     * @return list<array<string, mixed>>
     */
    private function owned(array $state): array
    {
        $demo = array_filter($this->trees->findAll(), static fn (array $tree): bool => !in_array($tree['id'], $state['removedTrees'], true));

        return [...array_values($demo), ...$state['customTrees']];
    }

    /**
     * @param array<string, mixed> $state
     * @return array<string, mixed>|null
     */
    private function ownedById(array $state, int $id): ?array
    {
        foreach ($this->owned($state) as $tree) {
            if ($tree['id'] === $id) {
                return $tree;
            }
        }

        return null;
    }

    /**
     * Nakłada na drzewko zabiegi zapisane „dzisiaj” (kolejnego dnia stan wraca do bazowego).
     *
     * @param array<string, mixed> $tree
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function applyState(array $tree, array $state, DateTimeImmutable $now, ?array $sensor = null): array
    {
        $tree['done'] = null;
        $care = $state['care'][(string) $tree['id']] ?? [];
        $today = $now->format('Y-m-d');

        $wateredAt = $this->todayTimestamp($care['water'] ?? null, $now);
        if ($wateredAt !== null) {
            $tree['lastWatering'] = 'dzisiaj, ' . $wateredAt->format('H:i');
            $tree['done'] = 'water';
            $tree['moisture'] = max((int) $tree['moisture'], self::WATERED_MOISTURE);
            $tree['moistureLabel'] = 'Optymalna';
            $tree['vitality'] = [
                'label' => $tree['vitality']['label'],
                'value' => 'Prawidłowy',
                'description' => 'Podłoże nawodnione, turgor wraca do normy w ciągu kilku godzin.',
            ];

            // Drzewko, dla którego podlanie było „akcją”, wraca do pełni zdrowia i traci alert.
            if ($tree['actionType'] === 'water') {
                $tree['status'] = 'ideal';
                $tree['statusLabel'] = 'Idealny';
                $tree['alert'] = null;
                $tree['action'] = null;
            }
        }

        $adjustedAt = $this->todayTimestamp($care['adjust'] ?? null, $now);
        if ($adjustedAt !== null && $tree['actionType'] === 'adjust') {
            $tree['care'] = null;
            $tree['action'] = null;
            $tree['done'] = 'adjust';
            $tree['status'] = 'ideal';
            $tree['statusLabel'] = 'Skorygowano';
        }
        if ($sensor !== null && $sensor['percent'] !== null && $now->getTimestamp() - $sensor['at'] <= self::SENSOR_FRESH_SECONDS) {
            $tree = $this->applySensor($tree, $sensor['percent'], $sensor);
        }

        $tree['_date'] = $today;

        return $tree;
    }

    /**
     * Odczyt z czujnika zastępuje wartość demonstracyjną: wilgotność, etykietę i (poza okazami do formowania) status
     * z alertem, więc niska wilgotność trafia też do listy „Wymaga zabiegu”, powiadomień i harmonogramu.
     *
     * @param array<string, mixed> $tree
     * @param array{raw: int, percent: ?int, at: int} $sensor
     * @return array<string, mixed>
     */
    private function applySensor(array $tree, int $percent, array $sensor): array
    {
        $tree['sensor'] = $sensor;
        $tree['moisture'] = $percent;
        $tree['moistureLabel'] = $percent >= self::SENSOR_OK ? 'Optymalna' : ($percent >= self::SENSOR_CRITICAL ? 'Niska' : 'Krytyczna');

        if ($tree['status'] === 'shaping') {
            return $tree;
        }

        if ($percent < self::SENSOR_CRITICAL) {
            $tree['status'] = 'critical';
            $tree['statusLabel'] = 'Wymaga podlania';
            $tree['alert'] = ['title' => 'Czujnik: bardzo sucho', 'text' => 'Odczyt ' . $percent . '% w podłożu. Podlej drzewko i sprawdź, czy woda dociera do korzeni.'];
            $tree['action'] = 'Zapisz podlanie';
            $tree['actionType'] = 'water';
        } elseif ($percent < self::SENSOR_OK) {
            $tree['status'] = 'attention';
            $tree['statusLabel'] = 'Wymaga działania';
            $tree['alert'] = ['title' => 'Czujnik: podłoże wysycha', 'text' => 'Odczyt ' . $percent . '% w podłożu. Zaplanuj podlanie na dziś.'];
            $tree['action'] = 'Zapisz podlanie';
            $tree['actionType'] = 'water';
        } elseif ($tree['actionType'] === 'water') {
            $tree['status'] = 'ideal';
            $tree['statusLabel'] = 'Idealny';
            $tree['vitality'] = [
                'label' => $tree['vitality']['label'],
                'value' => 'Prawidłowy',
                'description' => 'Czujnik pokazuje dobrze nawodnione podłoże.',
            ];
            $tree['alert'] = null;
            $tree['action'] = null;
        }

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

<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserStateRepository;
use App\Support\PolishDate;

/**
 * Powiadomienia pod ikoną dzwonka. Są wyliczane z aktualnego stanu kolekcji i harmonogramu,
 * a w stanie użytkownika zapisujemy tylko identyfikatory przeczytanych.
 * Identyfikator zawiera datę lub status, więc ta sama sytuacja (np. „Momiji jest przesuszone”)
 * zgłasza się ponownie, gdy wróci następnego dnia.
 */
final class NotificationService
{
    public function __construct(
        private readonly CollectionService $collection,
        private readonly ScheduleService $schedule,
        private readonly UserStateRepository $states,
    ) {
    }

    /**
     * @return array{items: list<array<string, mixed>>, unread: int}
     */
    public function forUser(int $userId): array
    {
        $state = $this->states->get($userId);
        $read = $state['notificationsRead'];
        $today = PolishDate::today();
        $todayKey = $today->format('Y-m-d');
        $items = [];

        foreach ($this->collection->all($userId) as $tree) {
            if ($tree['status'] === 'critical') {
                $items[] = $this->item("critical-{$tree['id']}-{$todayKey}", 'critical', 'water_drop', 'Krytyczna wilgotność: ' . $tree['name'], $tree['moisture'] . '%: ' . mb_strtolower((string) $tree['alert']['title']) . '. Wykonaj kąpiel zanurzeniową.', 'pilne', '/collection/' . $tree['id'], $read, 1);
            } elseif ($tree['status'] === 'attention') {
                $items[] = $this->item("attention-{$tree['id']}-{$todayKey}", 'warning', 'humidity_low', $tree['name'] . ' wymaga działania', (string) $tree['alert']['title'] . ' (wilgotność ' . $tree['moisture'] . '%).', 'dziś', '/collection/' . $tree['id'], $read, 2);
            }

            if ($tree['care'] !== null) {
                $items[] = $this->item("wire-{$tree['id']}-{$todayKey}", 'info', 'cable', 'Kontrola drutu: ' . $tree['name'], (string) $tree['care']['title'] . '. Sprawdź, czy drut nie wrasta w kambium.', 'ten tydzień', '/collection/' . $tree['id'], $read, 4);
            }
        }

        $tasks = $this->schedule->tasksFor($userId, $today, $state);
        $open = array_values(array_filter($tasks, static fn (array $t): bool => !$t['done']));
        if ($open !== []) {
            $count = count($open);
            $items[] = $this->item("tasks-{$todayKey}-{$count}", 'task', 'event_available', 'Dziś: ' . $count . ' ' . PolishDate::plural($count, 'zabieg', 'zabiegi', 'zabiegów') . ' do wykonania', 'Najpilniejszy: ' . $open[0]['title'] . '.', 'dziś', '/schedule', $read, 3);
        } elseif ($tasks !== []) {
            $items[] = $this->item("tasks-done-{$todayKey}", 'success', 'task_alt', 'Wszystkie zadania na dziś wykonane', 'Na dziś nie zostało nic do zrobienia.', 'dziś', '/schedule', $read, 6);
        }

        $window = $this->schedule->window($today);
        $windowKey = md5($window['title']);
        $items[] = $this->item("window-{$windowKey}-{$window['state']}", 'info', 'hourglass_bottom', $window['title'], $window['text'], $window['days'] <= 7 ? 'wkrótce' : 'sezon', '/studio', $read, 5);

        $sekki = $this->schedule->sekki($today);
        $items[] = $this->item('sekki-' . $sekki['number'] . '-' . $today->format('Y'), 'info', 'wb_twilight', 'Nowy sekki: ' . $sekki['romaji'] . ' (' . $sekki['kanji'] . ')', $sekki['name'] . ': ' . $sekki['blurb'], 'sezon', '/schedule', $read, 7);

        usort($items, static fn (array $a, array $b): int => [$a['read'], $a['order']] <=> [$b['read'], $b['order']]);

        return [
            'items' => $items,
            'unread' => count(array_filter($items, static fn (array $i): bool => !$i['read'])),
        ];
    }

    /**
     * Oznacza jako przeczytane jedno powiadomienie (`$id`) albo wszystkie bieżące (`$id === null`).
     */
    public function markRead(int $userId, ?string $id): void
    {
        $ids = $id !== null ? [$id] : array_column($this->forUser($userId)['items'], 'id');

        $this->states->update($userId, static function (array $state) use ($ids): array {
            // Lista nie rośnie bez końca: zostawiamy ostatnie 200 identyfikatorów.
            $state['notificationsRead'] = array_slice(array_values(array_unique([...$state['notificationsRead'], ...$ids])), -200);

            return $state;
        });
    }

    /**
     * @param list<string> $read
     * @return array<string, mixed>
     */
    private function item(string $id, string $tone, string $icon, string $title, string $text, string $when, string $href, array $read, int $order): array
    {
        return [
            'id' => $id,
            'tone' => $tone,
            'icon' => $icon,
            'title' => $title,
            'text' => $text,
            'when' => $when,
            'href' => $href,
            'read' => in_array($id, $read, true),
            'order' => $order,
        ];
    }
}

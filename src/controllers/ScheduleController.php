<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\ScheduleService;
use App\Support\PolishDate;

class ScheduleController extends AppController
{
    private const MAX_CUSTOM_TASKS = 100;

    // Obsługa trasy: GET /schedule?date=YYYY-MM-DD — Harmonogram zabiegów i zadań
    public function index(): void
    {
        $user = $this->requireLogin();
        $today = PolishDate::today();
        $selected = PolishDate::parse(isset($_GET['date']) ? (string) $_GET['date'] : null) ?? $today;

        $state = $this->states()->get($user['id']);
        $tasks = $this->schedule()->tasksFor($user['id'], $selected, $state);
        $open = count(array_filter($tasks, static fn (array $t): bool => !$t['done']));

        $this->render('schedule', [
            'pageTitle' => 'Harmonogram zabiegów — KOMOREBI',
            'activeNav' => 'schedule',
            'today' => $today,
            'selected' => $selected,
            'isToday' => $selected->format('Y-m-d') === $today->format('Y-m-d'),
            'week' => $this->schedule()->week($user['id'], $selected, $state),
            'tasks' => $tasks,
            'openCount' => $open,
            'urgentCount' => count(array_filter($tasks, static fn (array $t): bool => $t['urgent'] && !$t['done'])),
            'sekki' => $this->schedule()->sekki($selected),
            'window' => $this->schedule()->window($today),
            'upcoming' => $this->schedule()->upcoming($user['id'], $state),
            'tip' => $this->schedule()->tip($selected),
            'legend' => ScheduleService::TYPE_LABELS,
            'tones' => ScheduleService::TYPE_TONES,
        ]);
    }

    // Obsługa trasy: POST /schedule/tasks/done — oznacz zadanie jako wykonane / niewykonane
    public function done(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $id = (string) ($_POST['id'] ?? '');
        $date = PolishDate::parse((string) ($_POST['date'] ?? ''));
        $done = ($_POST['done'] ?? '') === '1';

        if ($date === null || !str_starts_with($id, $date->format('Y-m-d') . ':')) {
            $this->respond('Nieprawidłowe zadanie.', '/schedule', [], false, 422);
        }

        // Przyjmujemy tylko zadania, które naprawdę istnieją tego dnia (id nie może być dowolnym tekstem).
        $known = array_column($this->schedule()->tasksFor($user['id'], $date, $this->states()->get($user['id'])), 'id');
        if (!in_array($id, $known, true)) {
            $this->respond('Nie znaleziono zadania.', '/schedule', [], false, 404);
        }

        $state = $this->states()->update($user['id'], static function (array $state) use ($id, $done): array {
            $list = array_values(array_diff($state['tasksDone'], [$id]));
            if ($done) {
                $list[] = $id;
            }
            $state['tasksDone'] = array_slice($list, -500);

            return $state;
        });

        $tasks = $this->schedule()->tasksFor($user['id'], $date, $state);
        $open = count(array_filter($tasks, static fn (array $t): bool => !$t['done']));
        $week = $this->schedule()->week($user['id'], $date, $state);

        $this->respond(
            $done ? 'Zadanie wykonane.' : 'Przywrócono zadanie.',
            '/schedule?date=' . $date->format('Y-m-d'),
            ['done' => $done, 'open' => $open, 'total' => count($tasks), 'urgent' => count(array_filter($tasks, static fn (array $t): bool => $t['urgent'] && !$t['done'])), 'summary' => $this->summary($date, $open, count($tasks)), 'week' => $week['days'], 'unread' => $this->notifications()->forUser($user['id'])['unread']],
        );
    }

    // Obsługa trasy: POST /schedule/tasks — dodaj własny zabieg lub przypomnienie
    public function store(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $title = trim((string) ($_POST['title'] ?? ''));
        $note = trim((string) ($_POST['note'] ?? ''));
        $time = trim((string) ($_POST['time'] ?? ''));
        $type = (string) ($_POST['type'] ?? ScheduleService::TYPE_CARE);
        $date = PolishDate::parse((string) ($_POST['date'] ?? '')) ?? PolishDate::today();
        $back = '/schedule?date=' . $date->format('Y-m-d');

        if (mb_strlen($title) < 3 || mb_strlen($title) > 80) {
            $this->respond('Podaj nazwę zabiegu (3–80 znaków).', $back, [], false, 422);
        }

        if (mb_strlen($note) > 240 || !in_array($type, array_keys(ScheduleService::TYPE_TONES), true)) {
            $this->respond('Sprawdź opis (maks. 240 znaków) i rodzaj zabiegu.', $back, [], false, 422);
        }

        $time = preg_match('/^\d{2}:\d{2}$/', $time) === 1 ? $time : 'Cały dzień';

        $count = count($this->states()->get($user['id'])['customTasks']);
        if ($count >= self::MAX_CUSTOM_TASKS) {
            $this->respond('Osiągnięto limit własnych zabiegów. Usuń nieaktualne.', $back, [], false, 422);
        }

        $this->states()->update($user['id'], static function (array $state) use ($title, $note, $time, $type, $date): array {
            $state['customTasks'][] = [
                'id' => bin2hex(random_bytes(6)),
                'title' => $title,
                'note' => $note,
                'time' => $time,
                'type' => $type,
                'date' => $date->format('Y-m-d'),
            ];

            return $state;
        });

        $this->respond('Dodano zabieg: ' . $title . '.', $back, ['reload' => true]);
    }

    // Obsługa trasy: POST /schedule/tasks/delete — usuń własny zabieg
    public function delete(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $taskId = (string) ($_POST['task'] ?? '');
        $date = PolishDate::parse((string) ($_POST['date'] ?? '')) ?? PolishDate::today();
        $back = '/schedule?date=' . $date->format('Y-m-d');

        $removed = false;
        $this->states()->update($user['id'], static function (array $state) use ($taskId, &$removed): array {
            $before = count($state['customTasks']);
            $state['customTasks'] = array_values(array_filter($state['customTasks'], static fn (array $t): bool => ($t['id'] ?? '') !== $taskId));
            $removed = count($state['customTasks']) < $before;

            return $state;
        });

        $this->respond($removed ? 'Usunięto zabieg.' : 'Nie znaleziono zabiegu.', $back, ['reload' => true], $removed, 404);
    }

    private function summary(\DateTimeImmutable $date, int $open, int $total): string
    {
        $isToday = $date->format('Y-m-d') === PolishDate::today()->format('Y-m-d');

        return PolishDate::full($date) . ' • ' . $total . ' ' . PolishDate::plural($total, 'zaplanowany zabieg', 'zaplanowane zabiegi', 'zaplanowanych zabiegów')
            . ($isToday || $date > PolishDate::today() ? ' • do zrobienia: ' . $open : '');
    }
}

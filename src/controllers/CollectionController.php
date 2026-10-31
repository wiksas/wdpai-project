<?php

namespace App\Controllers;

use App\Service\MediaStorage;
use App\Support\PolishDate;

class CollectionController extends AppController
{
    private const MAX_JOURNAL_ENTRIES = 200;
    private const MAX_PHOTOS = 60;

    // Obsługa trasy zwykłej: GET /collection?category=&status=&q= — panel „Moja Kolekcja”
    public function index(): void
    {
        $user = $this->requireLogin();
        $state = $this->states()->get($user['id']);

        $all = $this->collection()->all($user['id']);
        $criteria = [
            'category' => $this->allowed($_GET['category'] ?? '', ['conifer', 'deciduous']),
            'status' => $this->allowed($_GET['status'] ?? '', ['attention']),
            'q' => mb_substr(trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : ''), 0, 60),
        ];
        $trees = $this->collection()->filter($all, $criteria);

        $this->render('collection', [
            'pageTitle' => 'Moja Kolekcja — KOMOREBI',
            'activeNav' => 'collection',
            'trees' => $trees,
            'treeCount' => count($all),
            'counts' => $this->collection()->counts($all),
            'rituals' => array_values(array_filter($all, static fn (array $t): bool => $t['action'] !== null)),
            'upcoming' => $this->schedule()->upcoming($user['id'], $state),
            'sekkiLabel' => $this->schedule()->sekki(PolishDate::today())['romaji'],
            'summary' => $this->collection()->summarize($all),
            'criteria' => $criteria,
            'journal' => array_slice(array_reverse($state['journal']), 0, 4),
            'misting' => $state['misting'],
            'tasksToday' => count(array_filter(
                $this->schedule()->tasksFor($user['id'], PolishDate::today(), $state),
                static fn (array $t): bool => !$t['done'],
            )),
        ]);
    }

    // Obsługa trasy z parametrem: GET /collection/{id} — karta okazu
    public function show(string $id): void
    {
        $user = $this->requireLogin();
        $tree = ctype_digit($id) ? $this->collection()->find($user['id'], (int) $id) : null;

        if ($tree === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Nie znaleziono okazu — KOMOREBI',
                'errorMessage' => 'Okaz o podanym identyfikatorze nie istnieje w kolekcji.',
            ]);
            return;
        }

        $this->render('specimen', [
            'pageTitle' => $tree['name'] . ' — Karta okazu — KOMOREBI',
            'activeNav' => 'collection',
            'tree' => $tree,
            'photos' => array_values(array_reverse(array_filter(
                $this->states()->get($user['id'])['photos'],
                static fn (array $photo): bool => (int) ($photo['treeId'] ?? 0) === $tree['id'],
            ))),
        ]);
    }

    // Obsługa trasy: POST /collection/{id}/care — zarejestruj podlanie (water) lub korektę drutu (adjust)
    public function care(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $type = (string) ($_POST['type'] ?? '');
        $tree = ctype_digit($id) ? $this->collection()->care($user['id'], (int) $id, $type) : null;

        if ($tree === null) {
            $this->respond('Nie można zarejestrować tego zabiegu.', '/collection', [], false, 422);
        }

        $message = $type === 'water'
            ? 'Zarejestrowano podlanie: ' . $tree['name'] . '.'
            : 'Zapisano korektę drutu: ' . $tree['name'] . '.';

        $this->respond($message, '/collection/' . $tree['id'], [
            'tree' => [
                'id' => $tree['id'],
                'moisture' => $tree['moisture'],
                'moistureLabel' => $tree['moistureLabel'],
                'status' => $tree['status'],
                'statusLabel' => $tree['statusLabel'],
                'lastWatering' => $tree['lastWatering'],
            ],
            'summary' => $this->collection()->summary($user['id']),
            'unread' => $this->notifications()->forUser($user['id'])['unread'],
            'reload' => true,
        ]);
    }

    // Obsługa trasy: POST /journal — dodaj wpis do szybkiego dziennika obserwacji
    public function journal(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $text = trim((string) ($_POST['text'] ?? ''));
        if (mb_strlen($text) < 3 || mb_strlen($text) > 500) {
            $this->respond('Wpis powinien mieć od 3 do 500 znaków.', '/collection', [], false, 422);
        }

        $entry = [
            'id' => bin2hex(random_bytes(6)),
            'text' => $text,
            'at' => PolishDate::now()->getTimestamp(),
        ];

        $this->states()->update($user['id'], static function (array $state) use ($entry): array {
            $state['journal'][] = $entry;
            $state['journal'] = array_slice($state['journal'], -self::MAX_JOURNAL_ENTRIES);

            return $state;
        });

        $this->respond('Zapisano wpis w dzienniku.', '/collection', [
            'entry' => ['text' => $text, 'when' => 'Dzisiaj, ' . PolishDate::now()->format('H:i')],
        ]);
    }

    // Obsługa trasy: POST /collection/{id}/photos — dodaj zdjęcie okazu (multipart, pole `photo`)
    public function addPhoto(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $tree = ctype_digit($id) ? $this->collection()->find($user['id'], (int) $id) : null;
        if ($tree === null) {
            $this->respond('Nie znaleziono okazu.', '/collection', [], false, 404);
        }

        $back = '/collection/' . $tree['id'];
        $existing = $this->states()->get($user['id'])['photos'];
        if (count($existing) >= self::MAX_PHOTOS) {
            $this->respond('Osiągnięto limit zdjęć. Usuń niepotrzebne, aby dodać nowe.', $back, [], false, 422);
        }

        $storage = new MediaStorage();
        try {
            $file = $storage->store($user['id'], $_FILES['photo'] ?? []);
        } catch (\InvalidArgumentException $exception) {
            $this->respond($exception->getMessage(), $back, [], false, 422);
        }

        $caption = mb_substr(trim((string) ($_POST['caption'] ?? '')), 0, 80);
        $entry = [
            'id' => bin2hex(random_bytes(6)),
            'treeId' => $tree['id'],
            'file' => $file,
            'caption' => $caption,
            'at' => PolishDate::now()->getTimestamp(),
        ];

        $this->states()->update($user['id'], static function (array $state) use ($entry): array {
            $state['photos'][] = $entry;

            return $state;
        });

        $this->respond('Dodano zdjęcie do karty: ' . $tree['name'] . '.', $back, ['photo' => ['id' => $entry['id'], 'url' => '/media/' . $file]]);
    }

    // Obsługa trasy: POST /collection/{id}/photos/delete — usuń własne zdjęcie okazu
    public function deletePhoto(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $photoId = (string) ($_POST['photo'] ?? '');
        $back = '/collection/' . (ctype_digit($id) ? $id : '');
        $file = null;

        $this->states()->update($user['id'], static function (array $state) use ($photoId, $id, &$file): array {
            $state['photos'] = array_values(array_filter($state['photos'], static function (array $photo) use ($photoId, $id, &$file): bool {
                if (($photo['id'] ?? '') === $photoId && (string) ($photo['treeId'] ?? '') === $id) {
                    $file = (string) $photo['file'];

                    return false;
                }

                return true;
            }));

            return $state;
        });

        if ($file === null) {
            $this->respond('Nie znaleziono zdjęcia.', $back, [], false, 404);
        }

        (new MediaStorage())->delete($user['id'], $file);
        $this->respond('Usunięto zdjęcie.', $back);
    }

    // Obsługa trasy: POST /misting — włącz / wyłącz automatyczne zamgławianie
    public function misting(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $on = ($_POST['on'] ?? '') === '1';
        $this->states()->update($user['id'], static function (array $state) use ($on): array {
            $state['misting'] = $on;

            return $state;
        });

        $this->respond($on ? 'Automatyczne zamgławianie włączone.' : 'Automatyczne zamgławianie wyłączone.', '/collection', ['on' => $on]);
    }

    /**
     * @param list<string> $allowed
     */
    private function allowed(mixed $value, array $allowed): string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : '';
    }
}

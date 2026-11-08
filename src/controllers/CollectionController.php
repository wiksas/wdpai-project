<?php

namespace App\Controllers;

use App\Repository\TreeRepository;
use App\Service\CollectionService;
use App\Service\MediaStorage;
use App\Service\PlantVision;
use App\Service\ScanHistory;
use App\Service\VisionException;
use App\Support\PolishDate;

class CollectionController extends AppController
{
    private const MAX_JOURNAL_ENTRIES = 200;
    private const MAX_PHOTOS = 60;

    // Obsługa trasy zwykłej: GET /collection?category=&status=&q=, panel kolekcji
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
            'pageTitle' => 'Kolekcja | KOMOREBI',
            'activeNav' => 'collection',
            'trees' => $trees,
            'treeCount' => count($all),
            'counts' => $this->collection()->counts($all),
            'dueTrees' => $this->collection()->needingCare($all),
            'doneToday' => count(array_filter($all, static fn (array $t): bool => $t['done'] !== null)),
            'upcoming' => $this->schedule()->upcoming($user['id'], $state),
            'sekki' => $this->schedule()->sekki(PolishDate::today()),
            'criteria' => $criteria,
            'journal' => array_slice(array_reverse($state['journal']), 0, 4),
            'tasksToday' => count(array_filter(
                $this->schedule()->tasksFor($user['id'], PolishDate::today(), $state),
                static fn (array $t): bool => !$t['done'],
            )),
        ]);
    }

    // Obsługa trasy z parametrem: GET /collection/{id}, karta okazu
    public function show(string $id): void
    {
        $user = $this->requireLogin();
        $tree = ctype_digit($id) ? $this->collection()->find($user['id'], (int) $id) : null;

        if ($tree === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Nie znaleziono okazu | KOMOREBI',
                'errorMessage' => 'Okaz o podanym identyfikatorze nie istnieje w kolekcji.',
            ]);
            return;
        }

        $state = $this->states()->get($user['id']);
        $treeScans = array_values(array_reverse(array_filter(
            $state['scans'],
            static fn (array $scan): bool => (int) ($scan['treeId'] ?? 0) === $tree['id'],
        )));

        $this->render('specimen', [
            'pageTitle' => $tree['name'] . ': karta okazu | KOMOREBI',
            'activeNav' => 'collection',
            'tree' => $tree,
            'treeScans' => $treeScans,
            'photos' => array_values(array_reverse(array_filter(
                $state['photos'],
                static fn (array $photo): bool => (int) ($photo['treeId'] ?? 0) === $tree['id'],
            ))),
        ]);
    }

    // Obsługa trasy: POST /collection/{id}/care, zarejestruj podlanie (water) lub korektę drutu (adjust)
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

    // Obsługa trasy: POST /journal, dodaj wpis do szybkiego dziennika obserwacji
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

    // Obsługa trasy: POST /collection/{id}/photos, dodaj zdjęcie okazu (multipart, pole `photo`)
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

        $entry = $this->savePhoto($user['id'], $tree['id'], $file, PolishDate::now()->getTimestamp(), mb_substr(trim((string) ($_POST['caption'] ?? '')), 0, 80));

        $this->respond('Dodano zdjęcie do karty: ' . $tree['name'] . '.', $back, ['photo' => ['id' => $entry['id'], 'url' => '/media/' . $file]]);
    }

    // Obsługa trasy: POST /collection/{id}/photos/delete, usuń własne zdjęcie okazu
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

    // Obsługa trasy: POST /collection/{id}/scan, skan kondycji okazu: nowe zdjęcie (`photo`) albo zapisane (`photo_id`)
    public function scan(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $tree = ctype_digit($id) ? $this->collection()->find($user['id'], (int) $id) : null;
        if ($tree === null) {
            $this->respond('Nie znaleziono okazu.', '/collection', [], false, 404);
        }

        $back = '/collection/' . $tree['id'] . '#passport-scan';
        $state = $this->states()->get($user['id']);
        $storage = new MediaStorage();
        $now = PolishDate::now()->getTimestamp();

        $photoId = is_string($_POST['photo_id'] ?? null) ? $_POST['photo_id'] : '';
        if ($photoId !== '') {
            $photo = null;
            foreach ($state['photos'] as $candidate) {
                if (($candidate['id'] ?? '') === $photoId && (int) ($candidate['treeId'] ?? 0) === $tree['id']) {
                    $photo = $candidate;
                }
            }
            if ($photo === null) {
                $this->respond('Nie znaleziono zdjęcia.', $back, [], false, 404);
            }
            $source = (string) $photo['file'];
            $photoAt = (int) ($photo['at'] ?? $now);
        } else {
            if (count($state['photos']) >= self::MAX_PHOTOS) {
                $this->respond('Osiągnięto limit zdjęć. Usuń niepotrzebne, aby dodać nowe.', $back, [], false, 422);
            }
            try {
                $source = $storage->store($user['id'], $_FILES['photo'] ?? []);
            } catch (\InvalidArgumentException $exception) {
                $this->respond($exception->getMessage(), $back, [], false, 422);
            }
            $photoAt = $now;
            $this->savePhoto($user['id'], $tree['id'], $source, $now);
        }

        $path = (string) $storage->path($user['id'], $source);
        try {
            $analysis = PlantVision::fromEnvironment()->analyze($path, 'leaves');
        } catch (VisionException $exception) {
            $this->respond($exception->getMessage() . ($photoId === '' ? ' Zdjęcie zostało zapisane w karcie okazu.' : ''), $back, [], false, 503);
        }

        // Poprzedni skan tego okazu: punkt odniesienia dla postępów.
        $previous = null;
        foreach (array_reverse($state['scans']) as $candidate) {
            if ((int) ($candidate['treeId'] ?? 0) === $tree['id'] && ($candidate['diagnosis']['plantVisible'] ?? false) === true) {
                $previous = $candidate;
                break;
            }
        }
        $diagnosis = $analysis;
        if ($analysis['plantVisible'] && $previous !== null) {
            $diagnosis['progress'] = PlantVision::progress($previous['diagnosis'], $analysis, (int) ($previous['photoAt'] ?? $previous['at']));
        }

        $copy = $storage->copy($user['id'], $source);
        if ($copy === null) {
            throw new \RuntimeException('Nie można skopiować zdjęcia do historii skanów.');
        }

        $scan = [
            'id' => bin2hex(random_bytes(6)),
            'treeId' => $tree['id'],
            'file' => $copy,
            'part' => 'leaves',
            'level' => $analysis['plantVisible'] ? $analysis['condition'] : 'watch',
            'vitality' => $analysis['plantVisible'] ? $analysis['vitality'] : 0,
            'title' => $analysis['plantVisible'] ? PlantVision::CAUSES[$analysis['cause']] : 'Nie widać drzewka na zdjęciu',
            'diagnosis' => $diagnosis,
            'at' => $now,
            'photoAt' => $photoAt,
        ];
        (new ScanHistory($this->states(), $storage))->add($user['id'], $scan);

        $this->respond('Oceniono okaz: ' . $tree['name'] . '.', $back, ['scan' => $scan['id']]);
    }

    /**
     * Dopisuje zdjęcie do karty okazu.
     *
     * @return array<string, mixed> zapisany wpis
     */
    private function savePhoto(int $userId, int $treeId, string $file, int $at, string $caption = ''): array
    {
        $entry = ['id' => bin2hex(random_bytes(6)), 'treeId' => $treeId, 'file' => $file, 'caption' => $caption, 'at' => $at];
        $this->states()->update($userId, static function (array $state) use ($entry): array {
            $state['photos'][] = $entry;

            return $state;
        });

        return $entry;
    }

    // Obsługa trasy: GET /collection/new, formularz dodawania własnego drzewka
    public function create(): void
    {
        $user = $this->requireLogin();
        if (count($this->collection()->all($user['id'])) >= CollectionService::CAPACITY) {
            $this->flash('error', 'Kolekcja jest pełna (' . CollectionService::CAPACITY . ' okazów). Usuń któryś okaz, żeby dodać nowy.');
            $this->redirect('/collection');
        }

        $this->renderTreeForm([], ['name' => '', 'commonName' => '', 'species' => '', 'category' => 'conifer', 'style' => 'Moyogi', 'pot' => '', 'age' => '']);
    }

    // Obsługa trasy: POST /collection, dodaj własne drzewko (multipart, opcjonalne pole `photo`)
    public function store(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $old = [];
        foreach (['name', 'commonName', 'species', 'category', 'style', 'pot', 'age'] as $field) {
            $old[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
        }

        $errors = [];
        $length = static fn (string $value): int => mb_strlen($value);
        if ($length($old['name']) < 1 || $length($old['name']) > 40) {
            $errors['name'] = 'Podaj nazwę drzewka (do 40 znaków).';
        }
        if ($length($old['commonName']) < 2 || $length($old['commonName']) > 60) {
            $errors['commonName'] = 'Podaj gatunek po polsku (2-60 znaków), np. Jałowiec chiński.';
        }
        if ($length($old['species']) > 60) {
            $errors['species'] = 'Nazwa łacińska może mieć do 60 znaków.';
        }
        if (!in_array($old['category'], ['conifer', 'deciduous'], true)) {
            $errors['category'] = 'Wybierz, czy drzewko jest iglaste, czy liściaste.';
        }
        if (!isset(TreeRepository::STYLES[$old['style']])) {
            $errors['style'] = 'Wybierz styl z listy.';
        }
        if ($length($old['pot']) > 40) {
            $errors['pot'] = 'Opis donicy może mieć do 40 znaków.';
        }
        if (!ctype_digit($old['age']) || (int) $old['age'] < 1 || (int) $old['age'] > 500) {
            $errors['age'] = 'Podaj przybliżony wiek w latach (1-500).';
        }

        $storage = new MediaStorage();
        $photo = null;
        if ($errors === [] && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            try {
                $photo = $storage->store($user['id'], $_FILES['photo']);
            } catch (\InvalidArgumentException $exception) {
                $errors['photo'] = $exception->getMessage();
            }
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->renderTreeForm($errors, $old);

            return;
        }

        $tree = $this->collection()->add($user['id'], [
            'name' => $old['name'],
            'commonName' => $old['commonName'],
            'species' => $old['species'],
            'category' => $old['category'],
            'style' => $old['style'],
            'pot' => $old['pot'],
            'age' => (int) $old['age'],
        ], $photo);

        if ($tree === null) {
            if ($photo !== null) {
                $storage->delete($user['id'], $photo);
            }
            $this->flash('error', 'Kolekcja jest pełna (' . CollectionService::CAPACITY . ' okazów). Usuń któryś okaz, żeby dodać nowy.');
            $this->redirect('/collection');
        }

        $this->flash('success', 'Dodano do kolekcji: ' . $tree['name'] . '.');
        $this->redirect('/collection/' . $tree['id']);
    }

    // Obsługa trasy: POST /collection/{id}/delete, usuń okaz z kolekcji użytkownika (wraz ze zdjęciami i zabiegami)
    public function destroy(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $removed = ctype_digit($id) ? $this->collection()->remove($user['id'], (int) $id) : null;
        if ($removed === null) {
            $this->respond('Nie ma takiego okazu w Twojej kolekcji.', '/collection', [], false, 404);
        }

        $storage = new MediaStorage();
        foreach ($removed['files'] as $file) {
            $storage->delete($user['id'], $file);
        }

        $this->flash('success', 'Usunięto z kolekcji: ' . $removed['tree']['name'] . '.');
        $this->redirect('/collection');
    }

    /**
     * @param array<string, string> $errors
     * @param array<string, string> $old
     */
    private function renderTreeForm(array $errors, array $old): void
    {
        $this->render('tree-new', [
            'pageTitle' => 'Nowe drzewko | KOMOREBI',
            'activeNav' => 'collection',
            'errors' => $errors,
            'old' => $old,
            'styles' => TreeRepository::STYLES,
        ]);
    }

    /**
     * @param list<string> $allowed
     */
    private function allowed(mixed $value, array $allowed): string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : '';
    }
}

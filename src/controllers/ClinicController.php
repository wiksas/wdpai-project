<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\ClinicConsultant;
use App\Service\ClinicKnowledge;
use App\Service\MediaStorage;
use App\Service\OllamaClient;
use App\Service\PlantVision;
use App\Service\ScanHistory;
use App\Service\VisionException;
use App\Support\PolishDate;

class ClinicController extends AppController
{
    private const MAX_CHAT = 40;
    private const CHAT_CONTEXT = 8;
    private const MAX_CUSTOM_TASKS = 100;
    private const PARTS = ['leaves', 'bark', 'roots', 'wiring'];
    private const LEVELS = ['good', 'watch', 'warning', 'critical'];

    // Obsługa trasy: GET /clinic?species=conifer|deciduous: skaner zdjęć, historia skanów, konsultant i notatki o gatunkach
    public function index(): void
    {
        $user = $this->requireLogin();
        $species = $_GET['species'] ?? '';
        $species = is_string($species) && in_array($species, ['conifer', 'deciduous'], true) ? $species : 'all';

        $allScans = $this->states()->get($user['id'])['scans'];
        $trees = $this->collection()->all($user['id']);

        $this->render('clinic', [
            'pageTitle' => 'Klinika | KOMOREBI',
            'activeNav' => 'clinic',
            'scans' => array_slice(array_reverse($allScans), 0, 5),
            'scanCount' => count($allScans),
            'chat' => $this->states()->get($user['id'])['chat'],
            'archiveFilter' => $species,
            'trees' => $trees,
            'treeNames' => array_column($trees, 'name', 'id'),
        ]);
    }

    // Obsługa trasy: POST /clinic/analyze: model wizyjny ogląda zdjęcie i zwraca rozpoznanie; nic nie zapisuje
    public function analyze(): void
    {
        $this->requireLogin();
        $this->requireCsrf();

        try {
            $path = (new MediaStorage())->validateUpload($_FILES['photo'] ?? []);
        } catch (\InvalidArgumentException $exception) {
            $this->respond($exception->getMessage(), '/clinic', [], false, 422);
        }

        try {
            $analysis = PlantVision::fromEnvironment()->analyze($path, $this->scanPart(), $this->submittedNote());
        } catch (VisionException $exception) {
            $this->respond($exception->getMessage(), '/clinic', ['fallback' => true], false, 503);
        }

        $this->respond('Zdjęcie ocenione.', '/clinic', ['analysis' => $analysis + $this->analysisLabels($analysis)]);
    }

    // Obsługa trasy: POST /clinic/scans: zapisz skan (zdjęcie + rozpoznanie modelu; bez JS model ocenia zdjęcie dopiero tutaj)
    public function scan(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $storage = new MediaStorage();
        try {
            $file = $storage->store($user['id'], $_FILES['photo'] ?? []);
        } catch (\InvalidArgumentException $exception) {
            $this->respond($exception->getMessage(), '/clinic', [], false, 422);
        }

        $part = $this->scanPart();
        $analysis = $this->submittedAnalysis();
        if ($analysis === null && !$this->isAjax()) {
            try {
                $analysis = PlantVision::fromEnvironment()->analyze((string) $storage->path($user['id'], $file), $part, $this->submittedNote());
            } catch (VisionException) {
                $analysis = null;
            }
        }

        if ($analysis !== null) {
            $level = $analysis['plantVisible'] ? $analysis['condition'] : 'watch';
            $vitality = $analysis['plantVisible'] ? $analysis['vitality'] : 0;
            $title = $analysis['plantVisible'] ? PlantVision::CAUSES[$analysis['cause']] : 'Nie widać drzewka na zdjęciu';
        } else {
            $level = (string) ($_POST['level'] ?? 'watch');
            $vitality = (int) ($_POST['vitality'] ?? 0);
            $title = mb_substr(trim((string) ($_POST['title'] ?? '')), 0, 90);
        }

        // Okaz z kolekcji (opcjonalny): skan trafia też na jego kartę, a ocena jest porównana z poprzednim skanem tego okazu.
        $tree = null;
        $treeField = $_POST['tree'] ?? '';
        if (is_string($treeField) && $treeField !== '') {
            $tree = ctype_digit($treeField) ? $this->collection()->find($user['id'], (int) $treeField) : null;
            if ($tree === null) {
                $storage->delete($user['id'], $file);
                $this->respond('Wybrany okaz nie istnieje w Twojej kolekcji.', '/clinic', [], false, 422);
            }
        }

        $now = PolishDate::now()->getTimestamp();
        $note = $this->submittedNote();
        if ($tree !== null && $analysis !== null && $analysis['plantVisible']) {
            foreach (array_reverse($this->states()->get($user['id'])['scans']) as $candidate) {
                if ((int) ($candidate['treeId'] ?? 0) === $tree['id'] && ($candidate['diagnosis']['plantVisible'] ?? false) === true) {
                    $analysis['progress'] = PlantVision::progress($candidate['diagnosis'], $analysis, (int) ($candidate['photoAt'] ?? $candidate['at']));
                    break;
                }
            }
        }

        $scan = [
            'id' => bin2hex(random_bytes(6)),
            'file' => $file,
            'part' => $part,
            'level' => in_array($level, self::LEVELS, true) ? $level : 'watch',
            'vitality' => max(0, min(100, $vitality)),
            'title' => $title !== '' ? $title : 'Zdjęcie bez analizy',
            'diagnosis' => $analysis,
            'at' => $now,
            'note' => $note,
        ];
        if ($tree !== null) {
            $scan['treeId'] = $tree['id'];
        }

        (new ScanHistory($this->states(), $storage))->add($user['id'], $scan);

        $message = $tree !== null ? 'Zapisano skan i przypisano go do okazu: ' . $tree['name'] . '.' : 'Zapisano skan w historii.';
        $this->respond($message, $tree !== null ? '/collection/' . $tree['id'] . '#passport-scan' : '/clinic', ['scan' => ['id' => $scan['id'], 'url' => '/media/' . $file]]);
    }

    // Obsługa trasy: POST /clinic/ask: odpowiedź konsultanta na pytanie (pole `q` lub `faq`); rozmowa zapisuje się na koncie
    public function ask(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $question = trim((string) ($_POST['faq'] ?? '') !== '' ? (string) $_POST['faq'] : (string) ($_POST['q'] ?? ''));
        if (mb_strlen($question) < 3 || mb_strlen($question) > 300) {
            $this->respond('Zadaj pytanie (3–300 znaków).', '/clinic#clinic-consultant', [], false, 422);
        }

        $history = array_map(
            static fn (array $message): array => ['role' => (string) $message['role'], 'text' => (string) $message['text']],
            array_slice($this->states()->get($user['id'])['chat'], -self::CHAT_CONTEXT),
        );
        $result = (new ClinicConsultant(new ClinicKnowledge(), OllamaClient::fromEnvironment('CHAT_MODEL', 'gemma3:4b')))
            ->reply($question, $history, $this->consultContext($user['id'], $question . ' ' . implode(' ', array_column(array_slice($history, -2), 'text'))));

        $now = PolishDate::now();
        $this->states()->update($user['id'], static function (array $state) use ($question, $result, $now): array {
            $state['chat'][] = ['role' => 'user', 'text' => $question, 'at' => $now->getTimestamp()];
            $state['chat'][] = ['role' => 'assistant', 'text' => $result['answer'], 'at' => $now->getTimestamp()];
            $state['chat'] = array_slice($state['chat'], -self::MAX_CHAT);

            return $state;
        });

        if ($this->isAjax()) {
            $this->json(['ok' => true, 'question' => $question, 'time' => $now->format('H:i')] + $result);
        }

        $this->flash('info', $result['answer']);
        $this->redirect('/clinic#clinic-consultant');
    }

    // Obsługa trasy: POST /clinic/protocol: dodaj kroki (protokołu ratunkowego albo oceny skanu) do harmonogramu na dziś
    public function protocol(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $steps = array_values(array_filter(array_map(
            static fn (mixed $step): string => mb_substr(trim((string) $step), 0, 80),
            array_slice((array) ($_POST['steps'] ?? []), 0, 5),
        ), static fn (string $step): bool => mb_strlen($step) >= 3));

        if ($steps === []) {
            $this->respond('Brak kroków do dodania.', '/clinic', [], false, 422);
        }

        // `label` (nazwa okazu) zastępuje „Protokół”; `scan` oznacza skan jako dodany do harmonogramu.
        $label = mb_substr(trim(is_string($_POST['label'] ?? null) ? $_POST['label'] : ''), 0, 40);
        $scanId = is_string($_POST['scan'] ?? null) ? $_POST['scan'] : '';
        $treeId = is_string($_POST['tree'] ?? null) && ctype_digit($_POST['tree']) ? (int) $_POST['tree'] : null;
        $today = PolishDate::today()->format('Y-m-d');
        $added = 0;
        $this->states()->update($user['id'], static function (array $state) use ($steps, $label, $scanId, $treeId, $today, &$added): array {
            foreach ($steps as $index => $step) {
                if (count($state['customTasks']) >= self::MAX_CUSTOM_TASKS) {
                    break;
                }
                $state['customTasks'][] = [
                    'id' => bin2hex(random_bytes(6)),
                    'title' => mb_substr(($label !== '' ? $label : 'Protokół') . ': ' . $step, 0, 120),
                    'note' => 'Krok ' . ($index + 1) . ($label !== '' ? ' z oceny skanu zdjęcia.' : ' protokołu ratunkowego z Kliniki.'),
                    'time' => 'Cały dzień',
                    'type' => 'care',
                    'date' => $today,
                    'treeId' => $treeId,
                ];
                $added++;
            }

            if ($scanId !== '' && $added > 0) {
                foreach ($state['scans'] as &$scan) {
                    if (($scan['id'] ?? '') === $scanId) {
                        $scan['scheduled'] = true;
                    }
                }
                unset($scan);
            }

            return $state;
        });

        $this->respond(
            'Dodano ' . $added . ' ' . PolishDate::plural($added, 'krok', 'kroki', 'kroków') . ' do harmonogramu na dziś.',
            '/schedule',
            ['added' => $added, 'href' => '/schedule'],
        );
    }

    // Obsługa trasy z parametrem: GET /clinic/scans/{id}, wynik skanu ze zdjęciem (lupa), ponowna ocena i usuwanie
    public function show(string $id): void
    {
        $user = $this->requireLogin();
        $scan = $this->findScan($user['id'], $id);

        if ($scan === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Nie znaleziono skanu | KOMOREBI',
                'errorMessage' => 'Skan o podanym identyfikatorze nie istnieje.',
            ]);
            return;
        }

        $tree = isset($scan['treeId']) ? $this->collection()->find($user['id'], (int) $scan['treeId']) : null;

        $this->render('clinic-scan', [
            'pageTitle' => 'Skan: ' . $scan['title'] . ' | KOMOREBI',
            'activeNav' => 'clinic',
            'scan' => $scan,
            'tree' => $tree,
            'maxNote' => PlantVision::MAX_NOTE,
        ]);
    }

    // Obsługa trasy: POST /clinic/scans/{id}/reassess, ponowna ocena tego samego zdjęcia (opcjonalnie z nowym opisem `note`)
    public function reassess(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $back = '/clinic/scans/' . $id;
        $scan = $this->findScan($user['id'], $id);
        if ($scan === null) {
            $this->respond('Nie znaleziono skanu.', '/clinic', [], false, 404);
        }

        $path = (new MediaStorage())->path($user['id'], (string) ($scan['file'] ?? ''));
        if ($path === null) {
            $this->respond('Zdjęcie tego skanu nie jest już dostępne.', $back, [], false, 404);
        }

        $note = $this->submittedNote();
        $part = in_array($scan['part'] ?? '', self::PARTS, true) ? (string) $scan['part'] : 'leaves';
        try {
            $analysis = PlantVision::fromEnvironment()->analyze($path, $part, $note);
        } catch (VisionException $exception) {
            $this->respond($exception->getMessage(), $back, [], false, 503);
        }

        $now = PolishDate::now()->getTimestamp();
        $treeId = (int) ($scan['treeId'] ?? 0);
        $this->states()->update($user['id'], static function (array $state) use ($id, $analysis, $note, $now, $treeId): array {
            $previous = null;
            foreach ($state['scans'] as &$item) {
                if (($item['id'] ?? '') !== $id) {
                    if ($treeId !== 0 && (int) ($item['treeId'] ?? 0) === $treeId && ($item['diagnosis']['plantVisible'] ?? false) === true) {
                        $previous = $item;
                    }
                    continue;
                }

                if ($previous !== null && $analysis['plantVisible']) {
                    $analysis['progress'] = PlantVision::progress($previous['diagnosis'], $analysis, (int) ($previous['photoAt'] ?? $previous['at']));
                }
                $item['diagnosis'] = $analysis;
                $item['level'] = $analysis['plantVisible'] ? $analysis['condition'] : 'watch';
                $item['vitality'] = $analysis['plantVisible'] ? $analysis['vitality'] : 0;
                $item['title'] = $analysis['plantVisible'] ? PlantVision::CAUSES[$analysis['cause']] : 'Nie widać drzewka na zdjęciu';
                $item['note'] = $note;
                $item['reassessedAt'] = $now;
                unset($item['scheduled']);
                break;
            }
            unset($item);

            return $state;
        });

        $this->respond('Oceniono ponownie na podstawie tego samego zdjęcia.', $back, ['reassessed' => true]);
    }

    // Obsługa trasy: POST /clinic/scans/{id}/delete, usuń skan razem z jego zdjęciem
    public function delete(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $file = null;
        $this->states()->update($user['id'], static function (array $state) use ($id, &$file): array {
            $state['scans'] = array_values(array_filter($state['scans'], static function (array $scan) use ($id, &$file): bool {
                if (($scan['id'] ?? '') !== $id) {
                    return true;
                }
                $file = (string) ($scan['file'] ?? '');

                return false;
            }));

            return $state;
        });

        if ($file === null) {
            $this->respond('Nie znaleziono skanu.', '/clinic', [], false, 404);
        }
        if ($file !== '') {
            (new MediaStorage())->delete($user['id'], $file);
        }

        $this->respond('Usunięto skan.', '/clinic', ['deleted' => true]);
    }

    private function scanPart(): string
    {
        $part = (string) ($_POST['part'] ?? 'leaves');

        return in_array($part, self::PARTS, true) ? $part : 'leaves';
    }

    /**
     * Rozpoznanie przysłane z formularza (wynik wcześniejszego /clinic/analyze), znormalizowane jak odpowiedź modelu.
     *
     * @return array<string, mixed>|null
     */
    private function submittedAnalysis(): ?array
    {
        $json = $_POST['analysis'] ?? '';
        $raw = is_string($json) && $json !== '' && strlen($json) <= 8000 ? json_decode($json, true) : null;

        return is_array($raw) ? PlantVision::normalize($raw) : null;
    }

    /**
     * Opis właściciela („co się ostatnio działo z drzewkiem”): zwykły tekst bez znaków sterujących, do 500 znaków.
     */
    private function submittedNote(): string
    {
        return PlantVision::cleanNote($_POST['note'] ?? '');
    }

    /**
     * @return array<string, mixed>|null skan zalogowanego użytkownika o podanym id
     */
    private function findScan(int $userId, string $id): ?array
    {
        if ($id === '' || !ctype_xdigit($id)) {
            return null;
        }
        foreach ($this->states()->get($userId)['scans'] as $scan) {
            if (($scan['id'] ?? '') === $id) {
                return $scan;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $analysis
     * @return array{causeLabel: string, confidenceLabel: string}
     */
    private function analysisLabels(array $analysis): array
    {
        return [
            'causeLabel' => PlantVision::CAUSES[$analysis['cause']],
            'confidenceLabel' => PlantVision::CONFIDENCE[$analysis['confidence']],
        ];
    }

    /**
     * Opis okazów i ostatniego skanu, które konsultant dostaje jako kontekst. Tylko to, o czym jest mowa w rozmowie
     * (nazwa lub gatunek drzewka, „moje”/„kolekcja”, „skan”), bo inaczej model wplata dane kolekcji w każdą odpowiedź.
     */
    private function consultContext(int $userId, string $topic): string
    {
        $topic = mb_strtolower($topic);
        $wholeCollection = (bool) preg_match('/\b(moj\w*|kolekcj\w*|wszystk\w*)\b/u', $topic);

        $lines = [];
        foreach ($this->collection()->all($userId) as $tree) {
            $species = mb_substr(mb_strtolower((string) strtok((string) $tree['commonName'], ' ')), 0, 5);
            if (!$wholeCollection && !str_contains($topic, mb_strtolower((string) $tree['name'])) && !($species !== '' && str_contains($topic, mb_substr($species, 0, 4)))) {
                continue;
            }
            $lines[] = sprintf(
                '%s (%s, %s): status %s, wilgotność %d%%, ostatnie podlanie: %s',
                $tree['name'],
                $tree['commonName'],
                $tree['category'] === 'conifer' ? 'iglaste' : 'liściaste',
                mb_strtolower((string) $tree['statusLabel']),
                (int) $tree['moisture'],
                $tree['lastWatering'],
            );
        }

        $scans = $this->states()->get($userId)['scans'];
        $last = $scans === [] ? null : end($scans);
        if (is_array($last) && is_array($last['diagnosis'] ?? null) && ($last['diagnosis']['summary'] ?? '') !== ''
            && preg_match('/skan|zdj[eę]c/u', $topic)) {
            $lines[] = 'Ostatni skan zdjęcia (' . PolishDate::ago((int) $last['at']) . '): ' . $last['title'] . '. ' . $last['diagnosis']['summary'];
        }

        return implode("\n", $lines);
    }
}

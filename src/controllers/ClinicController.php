<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\ClinicKnowledge;
use App\Service\MediaStorage;
use App\Service\PlantVision;
use App\Service\VisionException;
use App\Support\PolishDate;

class ClinicController extends AppController
{
    private const MAX_SCANS = 30;
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

        $this->render('clinic', [
            'pageTitle' => 'Klinika | KOMOREBI',
            'activeNav' => 'clinic',
            'scans' => array_slice(array_reverse($allScans), 0, 5),
            'scanCount' => count($allScans),
            'archiveFilter' => $species,
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
            $analysis = PlantVision::fromEnvironment()->analyze($path, $this->scanPart());
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
                $analysis = PlantVision::fromEnvironment()->analyze((string) $storage->path($user['id'], $file), $part);
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

        $scan = [
            'id' => bin2hex(random_bytes(6)),
            'file' => $file,
            'part' => $part,
            'level' => in_array($level, self::LEVELS, true) ? $level : 'watch',
            'vitality' => max(0, min(100, $vitality)),
            'title' => $title !== '' ? $title : 'Zdjęcie bez analizy',
            'diagnosis' => $analysis,
            'at' => PolishDate::now()->getTimestamp(),
        ];

        $removed = [];
        $this->states()->update($user['id'], static function (array $state) use ($scan, &$removed): array {
            $state['scans'][] = $scan;
            while (count($state['scans']) > self::MAX_SCANS) {
                $removed[] = array_shift($state['scans'])['file'] ?? '';
            }

            return $state;
        });

        foreach ($removed as $old) {
            $storage->delete($user['id'], (string) $old);
        }

        $this->respond('Zapisano skan w historii.', '/clinic', ['scan' => ['id' => $scan['id'], 'url' => '/media/' . $file]]);
    }

    // Obsługa trasy: POST /clinic/ask: odpowiedź z bazy wiedzy na pytanie (pole `q` lub `faq`)
    public function ask(): void
    {
        $this->requireLogin();
        $this->requireCsrf();

        $question = trim((string) ($_POST['faq'] ?? '') !== '' ? (string) $_POST['faq'] : (string) ($_POST['q'] ?? ''));
        if (mb_strlen($question) < 3 || mb_strlen($question) > 300) {
            $this->respond('Zadaj pytanie (3–300 znaków).', '/clinic#clinic-consultant', [], false, 422);
        }

        $result = (new ClinicKnowledge())->answer($question);

        if ($this->isAjax()) {
            $this->json(['ok' => true, 'question' => $question, 'time' => PolishDate::now()->format('H:i')] + $result);
        }

        $this->flash('info', $result['answer']);
        $this->redirect('/clinic#clinic-consultant');
    }

    // Obsługa trasy: POST /clinic/protocol: dodaj kroki protokołu ratunkowego do harmonogramu (dziś)
    public function protocol(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $steps = array_values(array_filter(array_map(
            static fn (mixed $step): string => mb_substr(trim((string) $step), 0, 80),
            array_slice((array) ($_POST['steps'] ?? []), 0, 5),
        ), static fn (string $step): bool => mb_strlen($step) >= 3));

        if ($steps === []) {
            $this->respond('Brak kroków protokołu do dodania.', '/clinic', [], false, 422);
        }

        $today = PolishDate::today()->format('Y-m-d');
        $added = 0;
        $this->states()->update($user['id'], static function (array $state) use ($steps, $today, &$added): array {
            foreach ($steps as $index => $step) {
                if (count($state['customTasks']) >= self::MAX_CUSTOM_TASKS) {
                    break;
                }
                $state['customTasks'][] = [
                    'id' => bin2hex(random_bytes(6)),
                    'title' => 'Protokół: ' . $step,
                    'note' => 'Krok ' . ($index + 1) . ' protokołu ratunkowego z Kliniki.',
                    'time' => 'Cały dzień',
                    'type' => 'care',
                    'date' => $today,
                ];
                $added++;
            }

            return $state;
        });

        $this->respond(
            'Dodano ' . $added . ' ' . PolishDate::plural($added, 'krok', 'kroki', 'kroków') . ' protokołu do harmonogramu na dziś.',
            '/schedule',
            ['added' => $added, 'href' => '/schedule'],
        );
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
}

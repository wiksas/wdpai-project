<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repository\SensorRepository;

class SensorController extends AppController
{
    // Obsługa trasy: POST /collection/{id}/sensor, dodaj czujnik okazu albo wystaw mu nowy token
    public function issue(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();
        $tree = $this->ownedTree($user['id'], $id);

        $name = mb_substr(trim(is_string($_POST['name'] ?? null) ? $_POST['name'] : ''), 0, 40);
        $token = (new SensorRepository())->issueToken($user['id'], $tree['id'], $name);

        // Token pokazujemy raz, zaraz po wystawieniu; w bazie zostaje tylko jego skrót.
        $this->session()->set('sensor_token_' . $tree['id'], $token);

        $this->respond('Czujnik dodany. Skopiuj token do urządzenia, bo drugi raz go nie pokażemy.', '/collection/' . $tree['id'] . '#passport-sensor');
    }

    // Obsługa trasy: POST /collection/{id}/sensor/delete, usuń czujnik okazu razem z odczytami
    public function remove(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();
        $tree = $this->ownedTree($user['id'], $id);

        (new SensorRepository())->delete($user['id'], $tree['id']);
        $this->session()->forget('sensor_token_' . $tree['id']);

        $this->respond('Usunięto czujnik okazu ' . $tree['name'] . '.', '/collection/' . $tree['id'] . '#passport-sensor');
    }

    // Obsługa trasy: POST /collection/{id}/sensor/calibrate, ostatni odczyt jako punkt `dry` (0%) albo `wet` (100%)
    public function calibrate(string $id): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();
        $tree = $this->ownedTree($user['id'], $id);
        $back = '/collection/' . $tree['id'] . '#passport-sensor';

        $point = (string) ($_POST['point'] ?? '');
        if (!in_array($point, ['dry', 'wet'], true)) {
            $this->respond('Nieznany punkt kalibracji.', $back, [], false, 422);
        }
        if (!(new SensorRepository())->calibrate($user['id'], $tree['id'], $point)) {
            $this->respond('Czujnik nie wysłał jeszcze żadnego odczytu, więc nie ma czego zapisać.', $back, [], false, 422);
        }

        $this->respond($point === 'dry' ? 'Zapisano ostatni odczyt jako suche podłoże (0%).' : 'Zapisano ostatni odczyt jako mokre podłoże (100%).', $back);
    }

    // Obsługa trasy: POST /api/sensor/readings, odczyt wysyłany przez urządzenie (token zamiast sesji i CSRF)
    public function ingest(): void
    {
        $token = $this->deviceToken();
        $raw = $this->rawValue();

        if (preg_match(SensorRepository::TOKEN_PATTERN, $token) !== 1) {
            $this->json(['ok' => false, 'message' => 'Brak lub zły token czujnika.'], 401);
        }
        if ($raw === null) {
            $this->json(['ok' => false, 'message' => 'Pole raw musi być liczbą całkowitą 0-65535.'], 422);
        }

        $result = (new SensorRepository())->record($token, $raw);
        if ($result['status'] === 'unknown') {
            $this->json(['ok' => false, 'message' => 'Brak lub zły token czujnika.'], 401);
        }
        if ($result['status'] === 'rate') {
            header('Retry-After: 30');
            $this->json(['ok' => false, 'message' => 'Za często: wysyłaj odczyt najwyżej co 30 sekund.'], 429);
        }

        $this->json(['ok' => true, 'percent' => $result['percent']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function ownedTree(int $userId, string $id): array
    {
        $tree = ctype_digit($id) ? $this->collection()->find($userId, (int) $id) : null;
        if ($tree === null) {
            $this->respond('Nie znaleziono okazu.', '/collection', [], false, 404);
        }

        return $tree;
    }

    /** Token z nagłówka `Authorization: Bearer …`, `X-Sensor-Token` albo pola `token` (proste urządzenia). */
    private function deviceToken(): string
    {
        $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (str_starts_with($header, 'Bearer ')) {
            return trim(substr($header, 7));
        }

        $custom = $_SERVER['HTTP_X_SENSOR_TOKEN'] ?? ($_POST['token'] ?? '');

        return is_string($custom) ? trim($custom) : '';
    }

    /** Wartość z ciała JSON (`{"raw": 1850}`) albo formularza (`raw=1850`). */
    private function rawValue(): ?int
    {
        $value = $_POST['raw'] ?? null;
        if ($value === null) {
            $body = json_decode((string) file_get_contents('php://input', false, null, 0, 2048), true);
            $value = is_array($body) ? ($body['raw'] ?? null) : null;
        }

        $number = filter_var($value, FILTER_VALIDATE_INT);

        return $number !== false && $number >= 0 && $number <= 65535 ? $number : null;
    }
}

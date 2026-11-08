<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Klient lokalnego modelu w usłudze `ollama` (`OLLAMA_URL`): jedno pytanie, jedna odpowiedź tekstowa.
 * Komunikaty wyjątków nadają się do pokazania użytkownikowi; szczegóły błędu trafiają do logu.
 */
final class OllamaClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
    ) {
    }

    /**
     * @param string $modelVariable zmienna środowiskowa z nazwą modelu (`VISION_MODEL` dla zdjęć, `CHAT_MODEL` dla rozmowy)
     */
    public static function fromEnvironment(string $modelVariable = 'VISION_MODEL', string $defaultModel = 'qwen2.5vl:7b'): self
    {
        $url = getenv('OLLAMA_URL');
        $model = getenv($modelVariable);

        return new self(
            rtrim($url !== false && $url !== '' ? $url : 'http://ollama:11434', '/'),
            $model !== false && $model !== '' ? $model : $defaultModel,
        );
    }

    /**
     * @param list<array<string, mixed>> $messages wiadomości w formacie Ollama (`role`, `content`, opcjonalnie `images`)
     * @param array<string, mixed> $options parametry generowania (temperature, num_predict…)
     * @throws VisionException
     */
    public function chat(array $messages, array $options = [], int $timeout = 150): string
    {
        $curl = curl_init($this->baseUrl . '/api/chat');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $this->model,
                'stream' => false,
                'keep_alive' => '15m',
                'options' => (object) $options,
                'messages' => $messages,
            ], JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => $timeout,
        ]);

        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($body === false) {
            error_log('Ollama: request failed: ' . $error);
            throw new VisionException('Model jest teraz niedostępny. Spróbuj za chwilę.');
        }

        if ($status === 404) {
            error_log("Ollama: model {$this->model} is not pulled; run `docker compose exec ollama ollama pull {$this->model}`");
            throw new VisionException('Model nie jest jeszcze pobrany.');
        }

        $reply = json_decode((string) $body, true);
        if ($status !== 200 || !is_array($reply)) {
            error_log("Ollama: HTTP {$status}: " . substr((string) $body, 0, 300));
            throw new VisionException('Model nie odpowiedział poprawnie. Spróbuj ponownie.');
        }

        return trim((string) ($reply['message']['content'] ?? ''));
    }
}

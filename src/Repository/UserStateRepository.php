<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Stan aplikacji zapisywany osobno dla każdego użytkownika (podlania, odhaczone zadania,
 * własne zabiegi, dziennik, przeczytane powiadomienia) w `storage/state/user-{id}.json`.
 * Rozwiązanie tymczasowe do czasu dodania bazy danych — po jej dodaniu wystarczy podmienić tę klasę.
 *
 * @phpstan-type State array{
 *     notificationsRead: list<string>,
 *     tasksDone: list<string>,
 *     customTasks: list<array<string, mixed>>,
 *     care: array<string, array<string, int>>,
 *     journal: list<array<string, mixed>>,
 *     misting: bool
 * }
 */
final class UserStateRepository
{
    private const DEFAULTS = [
        'notificationsRead' => [],
        'tasksDone' => [],
        'customTasks' => [],
        'care' => [],
        'journal' => [],
        'misting' => true,
    ];

    private readonly string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?? dirname(__DIR__, 2) . '/storage/state';
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int $userId): array
    {
        return $this->read($userId);
    }

    /**
     * Odczyt, modyfikacja i zapis pod blokadą pliku. Funkcja dostaje aktualny stan i zwraca nowy.
     *
     * @param callable(array<string, mixed>): array<string, mixed> $mutator
     * @return array<string, mixed> zapisany stan
     */
    public function update(int $userId, callable $mutator): array
    {
        $this->ensureDirectory();
        $path = $this->path($userId);

        $handle = fopen($path, 'c+');
        if ($handle === false) {
            throw new \RuntimeException('Nie można otworzyć pliku stanu: ' . $path);
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new \RuntimeException('Nie można zablokować pliku stanu: ' . $path);
            }

            $raw = stream_get_contents($handle);
            $current = $this->normalize(is_string($raw) && $raw !== '' ? json_decode($raw, true) : null);
            $next = $this->normalize($mutator($current));

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($next, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            fflush($handle);
            flock($handle, LOCK_UN);

            return $next;
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function read(int $userId): array
    {
        $path = $this->path($userId);
        if (!is_file($path)) {
            return self::DEFAULTS;
        }

        return $this->normalize(json_decode((string) file_get_contents($path), true));
    }

    /**
     * @return array<string, mixed>
     */
    private function normalize(mixed $state): array
    {
        $state = is_array($state) ? $state : [];
        $result = self::DEFAULTS;

        foreach (self::DEFAULTS as $key => $default) {
            if (!array_key_exists($key, $state) || gettype($state[$key]) !== gettype($default)) {
                continue;
            }
            $result[$key] = $state[$key];
        }

        return $result;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new \RuntimeException('Nie można utworzyć katalogu stanu: ' . $this->directory);
        }
    }

    private function path(int $userId): string
    {
        return $this->directory . '/user-' . $userId . '.json';
    }
}

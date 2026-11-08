<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Stan aplikacji per konto w tabeli `user_state` (JSONB, `docker/db/04-user-state.sql`): kolekcja, zabiegi, skany,
 * zdjęcia, dziennik, rozmowa z konsultantem. Przetrwa restart i przebudowę kontenerów, bo leży w wolumenie bazy.
 * Starszy plik `storage/state/user-{id}.json` jest importowany przy pierwszym użyciu konta i oznaczany jako `.migrated`.
 */
final class UserStateRepository
{
    private const DEFAULTS = [
        'notificationsRead' => [],
        'tasksDone' => [],
        'customTasks' => [],
        'care' => [],
        'journal' => [],
        'photos' => [],
        'scans' => [],
        'customTrees' => [],
        'removedTrees' => [],
        'chat' => [],
    ];

    private readonly string $legacyDirectory;
    private readonly string $migrationFile;

    /** @var array<int, array<string, mixed>> stan odczytany w tym żądaniu; unieważniany przez update() */
    private array $cache = [];

    public function __construct(?string $legacyDirectory = null)
    {
        $this->legacyDirectory = $legacyDirectory ?? dirname(__DIR__, 2) . '/storage/state';
        $this->migrationFile = dirname(__DIR__, 2) . '/docker/db/04-user-state.sql';
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int $userId): array
    {
        return $this->cache[$userId] ??= $this->withTable(fn (): array => $this->read($userId));
    }

    /**
     * Odczyt, modyfikacja i zapis w transakcji z blokadą wiersza. Funkcja dostaje aktualny stan i zwraca nowy.
     *
     * @param callable(array<string, mixed>): array<string, mixed> $mutator
     * @return array<string, mixed> zapisany stan
     */
    public function update(int $userId, callable $mutator): array
    {
        unset($this->cache[$userId]);

        return $this->cache[$userId] = $this->withTable(fn (): array => $this->write($userId, $mutator));
    }

    /**
     * @param callable(array<string, mixed>): array<string, mixed> $mutator
     * @return array<string, mixed>
     */
    private function write(int $userId, callable $mutator): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            // Wiersz musi istnieć, żeby dało się go zablokować; pierwszy zapis przenosi też stary plik JSON.
            $pdo->prepare('INSERT INTO user_state (user_id, state) VALUES (:id, :state) ON CONFLICT (user_id) DO NOTHING')
                ->execute(['id' => $userId, 'state' => $this->encode($this->legacy($userId))]);

            $select = $pdo->prepare('SELECT state FROM user_state WHERE user_id = :id FOR UPDATE');
            $select->execute(['id' => $userId]);
            $current = $this->normalize(json_decode((string) $select->fetchColumn(), true));

            $next = $this->normalize($mutator($current));
            $pdo->prepare('UPDATE user_state SET state = :state, updated_at = now() WHERE user_id = :id')
                ->execute(['id' => $userId, 'state' => $this->encode($next)]);

            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }

        $this->retireLegacy($userId);

        return $next;
    }

    /**
     * @return array<string, mixed>
     */
    private function read(int $userId): array
    {
        $select = Database::connection()->prepare('SELECT state FROM user_state WHERE user_id = :id');
        $select->execute(['id' => $userId]);
        $raw = $select->fetchColumn();

        return $raw === false ? $this->legacy($userId) : $this->normalize(json_decode((string) $raw, true));
    }

    /**
     * Stan z dawnego pliku JSON (albo domyślny), dopóki konto nie ma wiersza w bazie.
     *
     * @return array<string, mixed>
     */
    private function legacy(int $userId): array
    {
        $path = $this->legacyPath($userId);

        return $this->normalize(is_file($path) ? json_decode((string) file_get_contents($path), true) : null);
    }

    /** Po przeniesieniu do bazy plik nie może zasilić innego konta, które dostanie to samo id po resecie bazy. */
    private function retireLegacy(int $userId): void
    {
        $path = $this->legacyPath($userId);
        if (is_file($path)) {
            @rename($path, $path . '.migrated');
        }
    }

    private function withTable(callable $operation): mixed
    {
        return Database::withMigration($this->migrationFile, $operation);
    }

    /**
     * @param array<string, mixed> $state
     */
    private function encode(array $state): string
    {
        return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
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

    private function legacyPath(int $userId): string
    {
        return $this->legacyDirectory . '/user-' . $userId . '.json';
    }
}

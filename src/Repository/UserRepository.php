<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Tymczasowe źródło kont użytkowników — do czasu dodania warstwy bazy danych.
 *
 * Konto demonstracyjne `admin` / `admin` jest zapisane w kodzie, a konta założone
 * przez formularz rejestracji trafiają do pliku `storage/users.json` (poza `public/`).
 * Docelowo wystarczy podmienić implementację tej klasy na zapytania PDO.
 *
 * @phpstan-type User array{id: int, login: string, email: string, name: string, passwordHash: string, role: string, level: string, species: list<string>}
 */
final class UserRepository
{
    // Hash hasła „admin” (konto demonstracyjne — usuń przed wdrożeniem produkcyjnym).
    private const DEMO_ADMIN = [
        'id' => 1,
        'login' => 'admin',
        'email' => 'admin@komorebi.pl',
        'name' => 'Admin',
        'passwordHash' => '$2y$10$8rB9Lmk.ToPG4r9BnTeNbuKapxIFsJ65RV.jcjg/mmKUutm39HYe2',
        'role' => 'admin',
        'level' => 'master',
        'species' => [],
    ];

    private readonly string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?? dirname(__DIR__, 2) . '/storage/users.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        foreach ($this->all() as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Szuka po loginie lub adresie e-mail (bez rozróżniania wielkości liter).
     *
     * @return array<string, mixed>|null
     */
    public function findByLogin(string $login): ?array
    {
        $needle = mb_strtolower(trim($login));

        foreach ($this->all() as $user) {
            if ($needle !== '' && ($needle === mb_strtolower($user['login']) || $needle === mb_strtolower($user['email']))) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param list<string> $species
     * @return array<string, mixed>
     */
    public function create(string $name, string $email, string $passwordHash, string $level, array $species): array
    {
        $users = $this->stored();
        $nextId = max([self::DEMO_ADMIN['id'], ...array_map(static fn (array $user): int => $user['id'], $users)]) + 1;

        $user = [
            'id' => $nextId,
            'login' => $email,
            'email' => $email,
            'name' => $name,
            'passwordHash' => $passwordHash,
            'role' => 'user',
            'level' => $level,
            'species' => $species,
        ];

        $users[] = $user;
        $this->write($users);

        return $user;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function all(): array
    {
        return [self::DEMO_ADMIN, ...$this->stored()];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function stored(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($this->file), true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    /**
     * @param list<array<string, mixed>> $users
     */
    private function write(array $users): void
    {
        $directory = dirname($this->file);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Nie można utworzyć katalogu danych: ' . $directory);
        }

        $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        if (file_put_contents($this->file, $json, LOCK_EX) === false) {
            throw new \RuntimeException('Nie można zapisać pliku użytkowników: ' . $this->file);
        }
    }
}

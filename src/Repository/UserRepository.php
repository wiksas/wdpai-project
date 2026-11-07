<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use PDOException;

/**
 * Konta użytkowników w bazie PostgreSQL (tabele `users` i `user_species`, schemat: docker/db/01-schema.sql).
 * Hash hasła nigdy nie opuszcza warstwy repozytorium/AuthService (widoki dostają AuthService::present()).
 *
 * @phpstan-type User array{id: int, login: string, email: string, name: string, passwordHash: string, role: string, level: string, species: list<string>}
 */
final class UserRepository
{
    private const UNIQUE_VIOLATION = '23505';

    private const COLUMNS = 'id, login, email, name, password_hash, role, level';

    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT ' . self::COLUMNS . ' FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $this->hydrate($statement->fetch());
    }

    /**
     * Szuka po loginie lub adresie e-mail (bez rozróżniania wielkości liter).
     *
     * @return array<string, mixed>|null
     */
    public function findByLogin(string $login): ?array
    {
        $needle = mb_strtolower(trim($login));
        if ($needle === '') {
            return null;
        }

        $statement = $this->db->prepare('SELECT ' . self::COLUMNS . ' FROM users WHERE lower(login) = :login OR lower(email) = :email LIMIT 1');
        $statement->execute(['login' => $needle, 'email' => $needle]);

        return $this->hydrate($statement->fetch());
    }

    public function emailExists(string $email): bool
    {
        $needle = mb_strtolower(trim($email));
        $statement = $this->db->prepare('SELECT 1 FROM users WHERE lower(email) = :email OR lower(login) = :login');
        $statement->execute(['email' => $needle, 'login' => $needle]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Zakłada konto (login = e-mail) razem z wybranymi gatunkami w jednej transakcji.
     * Zwraca null, gdy adres zajęło w międzyczasie inne konto (unikalny indeks w bazie).
     *
     * @param list<string> $species
     * @return array<string, mixed>|null
     */
    public function create(string $name, string $email, string $passwordHash, string $level, array $species): ?array
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare(
                'INSERT INTO users (login, email, name, password_hash, level) VALUES (:login, :email, :name, :hash, :level) RETURNING id',
            );
            $statement->execute(['login' => $email, 'email' => $email, 'name' => $name, 'hash' => $passwordHash, 'level' => $level]);
            $id = (int) $statement->fetchColumn();

            $insertSpecies = $this->db->prepare('INSERT INTO user_species (user_id, species) VALUES (:id, :species)');
            foreach (array_unique($species) as $item) {
                $insertSpecies->execute(['id' => $id, 'species' => $item]);
            }

            $this->db->commit();
        } catch (PDOException $exception) {
            $this->db->rollBack();
            if ($exception->getCode() === self::UNIQUE_VIOLATION) {
                return null;
            }

            throw $exception;
        }

        return $this->findById($id);
    }

    /** Po udanym logowaniu: czas logowania i (opcjonalnie) hash przeliczony silniejszym algorytmem. */
    public function recordLogin(int $id, ?string $newPasswordHash = null): void
    {
        if ($newPasswordHash !== null) {
            $statement = $this->db->prepare('UPDATE users SET last_login_at = now(), password_hash = :hash WHERE id = :id');
            $statement->execute(['id' => $id, 'hash' => $newPasswordHash]);

            return;
        }

        $statement = $this->db->prepare('UPDATE users SET last_login_at = now() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @param array<string, mixed>|false $row
     * @return array<string, mixed>|null
     */
    private function hydrate(array|false $row): ?array
    {
        if ($row === false) {
            return null;
        }

        $statement = $this->db->prepare('SELECT species FROM user_species WHERE user_id = :id ORDER BY species');
        $statement->execute(['id' => $row['id']]);

        return [
            'id' => (int) $row['id'],
            'login' => (string) $row['login'],
            'email' => (string) $row['email'],
            'name' => (string) $row['name'],
            'passwordHash' => (string) $row['password_hash'],
            'role' => (string) $row['role'],
            'level' => (string) $row['level'],
            'species' => $statement->fetchAll(PDO::FETCH_COLUMN),
        ];
    }
}

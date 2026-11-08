<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use PDOException;

/**
 * Konta użytkowników (`users`, `user_species`).
 * Hash hasła nie opuszcza repozytorium i AuthService: widoki dostają AuthService::present().
 *
 * @phpstan-type User array{id: int, login: string, email: string, name: string, passwordHash: string, role: string, level: string, species: list<string>}
 */
final class UserRepository
{
    private const UNIQUE_VIOLATION = '23505';

    private const COLUMNS = 'id, login, email, name, password_hash, role, level, email_verified_at';

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
        $statement = $this->db->prepare('SELECT 1 FROM users WHERE lower(email) = :email');
        $statement->execute(['email' => mb_strtolower(trim($email))]);

        return $statement->fetchColumn() !== false;
    }

    public function loginExists(string $login): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM users WHERE lower(login) = :login');
        $statement->execute(['login' => mb_strtolower(trim($login))]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Zakłada konto z gatunkami w jednej transakcji.
     * Gdy login lub e-mail zajęto w międzyczasie, rzuca DuplicateUserException z nazwą pola.
     *
     * @param list<string> $species
     * @return array<string, mixed>
     */
    public function create(string $login, string $name, string $email, string $passwordHash, string $level, array $species): array
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare(
                'INSERT INTO users (login, email, name, password_hash, level) VALUES (:login, :email, :name, :hash, :level) RETURNING id',
            );
            $statement->execute(['login' => $login, 'email' => $email, 'name' => $name, 'hash' => $passwordHash, 'level' => $level]);
            $id = (int) $statement->fetchColumn();

            $insertSpecies = $this->db->prepare('INSERT INTO user_species (user_id, species) VALUES (:id, :species)');
            foreach (array_unique($species) as $item) {
                $insertSpecies->execute(['id' => $id, 'species' => $item]);
            }

            $this->db->commit();
        } catch (PDOException $exception) {
            $this->db->rollBack();
            if ($exception->getCode() === self::UNIQUE_VIOLATION) {
                // Komunikat PostgreSQL zawiera nazwę naruszonego indeksu (users_login_lower_key / users_email_lower_key).
                throw new DuplicateUserException(str_contains($exception->getMessage(), 'users_login_lower_key') ? 'login' : 'email');
            }

            throw $exception;
        }

        return (array) $this->findById($id);
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

    /** Potwierdza adres e-mail (link z wiadomości). Nie nadpisuje daty wcześniejszego potwierdzenia. */
    public function markEmailVerified(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET email_verified_at = now() WHERE id = :id AND email_verified_at IS NULL');
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
            'verified' => $row['email_verified_at'] !== null,
            'passwordHash' => (string) $row['password_hash'],
            'role' => (string) $row['role'],
            'level' => (string) $row['level'],
            'species' => $statement->fetchAll(PDO::FETCH_COLUMN),
        ];
    }
}

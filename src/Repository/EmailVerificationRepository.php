<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Jednorazowe linki potwierdzające adres e-mail (tabela `email_verifications`, migracja 03).
 * Przechowujemy tylko skrót SHA-256 tokenu: wyciek bazy nie daje działających linków.
 */
final class EmailVerificationRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** Zapisuje nowy token i unieważnia poprzednie linki tego konta. */
    public function replace(int $userId, string $tokenHash, int $ttlSeconds): void
    {
        $this->db->beginTransaction();
        try {
            $this->deleteForUser($userId);
            $statement = $this->db->prepare(
                "INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (:user, :hash, now() + make_interval(secs => :ttl))",
            );
            $statement->execute(['user' => $userId, 'hash' => $tokenHash, 'ttl' => $ttlSeconds]);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @return array{userId: int, expired: bool}|null
     */
    public function findByHash(string $tokenHash): ?array
    {
        $statement = $this->db->prepare('SELECT user_id, expires_at <= now() AS expired FROM email_verifications WHERE token_hash = :hash');
        $statement->execute(['hash' => $tokenHash]);
        $row = $statement->fetch();

        return $row === false ? null : ['userId' => (int) $row['user_id'], 'expired' => (bool) $row['expired']];
    }

    /** Ile sekund minęło od wysłania ostatniego linku (null, gdy żadnego nie ma). */
    public function secondsSinceLast(int $userId): ?int
    {
        $statement = $this->db->prepare('SELECT EXTRACT(EPOCH FROM now() - MAX(created_at)) FROM email_verifications WHERE user_id = :user');
        $statement->execute(['user' => $userId]);
        $value = $statement->fetchColumn();

        return $value === null || $value === false ? null : (int) $value;
    }

    public function deleteForUser(int $userId): void
    {
        $statement = $this->db->prepare('DELETE FROM email_verifications WHERE user_id = :user');
        $statement->execute(['user' => $userId]);
    }
}

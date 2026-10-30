<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

/**
 * Logowanie i bieżący użytkownik. Jedyne miejsce, które dotyka `$_SESSION`.
 */
final class AuthService
{
    private const SESSION_KEY = 'user_id';

    // Hash losowego ciągu — używany tylko po to, by wyrównać czas odpowiedzi dla nieistniejących kont.
    private const DUMMY_HASH = '$2y$10$oqut/KBQRpeCmXEWD/MJZeSGVEHc1gtPWybk7F00I8spkNZCpJVda';

    private const LEVEL_LABELS = [
        'beginner' => 'Początkujący',
        'enthusiast' => 'Pasjonat',
        'master' => 'Mistrz',
    ];

    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Zalogowany użytkownik w postaci bezpiecznej dla widoków (bez hasha hasła) albo null.
     *
     * @return array{id: int, name: string, email: string, role: string, initials: string, label: string}|null
     */
    public function currentUser(): ?array
    {
        $id = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_int($id)) {
            return null;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            unset($_SESSION[self::SESSION_KEY]);
            return null;
        }

        return $this->present($user);
    }

    /**
     * Sprawdza dane logowania; przy poprawnych otwiera sesję.
     */
    public function attempt(string $login, string $password): bool
    {
        $user = $this->users->findByLogin($login);

        // password_verify wykonujemy zawsze, żeby czas odpowiedzi nie zdradzał, czy konto istnieje.
        $valid = password_verify($password, $user['passwordHash'] ?? self::DUMMY_HASH);

        if ($user === null || !$valid) {
            return false;
        }

        $this->signIn((int) $user['id']);

        return true;
    }

    public function signIn(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $userId;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    /**
     * @param array<string, mixed> $user
     * @return array{id: int, name: string, email: string, role: string, initials: string, label: string}
     */
    private function present(array $user): array
    {
        $words = preg_split('/\s+/u', trim((string) $user['name'])) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
            'initials' => $initials !== '' ? $initials : '?',
            'label' => $user['role'] === 'admin'
                ? 'Administrator'
                : (self::LEVEL_LABELS[$user['level']] ?? 'Opiekun kolekcji'),
        ];
    }
}

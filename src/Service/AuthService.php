<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

/**
 * Logowanie i bieżący użytkownik.
 */
final class AuthService
{
    private const SESSION_KEY = 'user_id';

    /** Argon2id: odporny na łamanie na GPU; starsze hashe (bcrypt) są przeliczane przy logowaniu. */
    private const HASH_ALGORITHM = PASSWORD_ARGON2ID;

    // Hash losowego ciągu: używany tylko po to, by wyrównać czas odpowiedzi dla nieistniejących kont.
    private const DUMMY_HASH = '$argon2id$v=19$m=65536,t=4,p=1$aE16c2Y4ZDRXMTNDN0c3Wg$Q/0dR+oc+72HYi+WsxmSVa7ib40F8cBAEwuxKKdhKRY';

    private const LEVEL_LABELS = [
        'beginner' => 'Początkujący',
        'enthusiast' => 'Pasjonat',
        'master' => 'Mistrz',
    ];

    /** Zapamiętany wynik currentUser() dla bieżącego żądania (jedno zapytanie zamiast kilku). */
    private ?array $current = null;
    private bool $resolved = false;

    public function __construct(
        private readonly UserRepository $users,
        private readonly Session $session,
    ) {
    }

    /**
     * Zalogowany użytkownik w postaci bezpiecznej dla widoków (bez hasha hasła) albo null.
     *
     * @return array{id: int, name: string, email: string, role: string, initials: string, label: string}|null
     */
    public function currentUser(): ?array
    {
        if ($this->resolved) {
            return $this->current;
        }

        $this->resolved = true;
        $id = $this->session->get(self::SESSION_KEY);
        if (!is_int($id)) {
            return $this->current = null;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            $this->session->forget(self::SESSION_KEY);
            return $this->current = null;
        }

        return $this->current = $this->present($user);
    }

    /**
     * Sprawdza dane logowania; przy poprawnych otwiera sesję, zapisuje czas logowania
     * i przelicza hash, jeśli powstał starszym algorytmem.
     */
    public function attempt(string $login, string $password): bool
    {
        $user = $this->users->findByLogin($login);

        // password_verify wykonujemy zawsze, żeby czas odpowiedzi nie zdradzał, czy konto istnieje.
        $valid = password_verify($password, $user['passwordHash'] ?? self::DUMMY_HASH);

        if ($user === null || !$valid) {
            return false;
        }

        $rehash = password_needs_rehash($user['passwordHash'], self::HASH_ALGORITHM)
            ? password_hash($password, self::HASH_ALGORITHM)
            : null;
        $this->users->recordLogin((int) $user['id'], $rehash);
        $this->signIn((int) $user['id']);

        return true;
    }

    /**
     * Zakłada konto z zahashowanym hasłem i od razu loguje. Zwraca false, gdy e-mail jest zajęty.
     *
     * @param list<string> $species
     */
    public function register(string $name, string $email, string $password, string $level, array $species): bool
    {
        $user = $this->users->create($name, $email, password_hash($password, self::HASH_ALGORITHM), $level, $species);
        if ($user === null) {
            return false;
        }

        $this->users->recordLogin((int) $user['id']);
        $this->signIn((int) $user['id']);

        return true;
    }

    public function emailTaken(string $email): bool
    {
        return $this->users->emailExists($email);
    }

    public function signIn(int $userId): void
    {
        $this->session->regenerate();
        $this->session->set(self::SESSION_KEY, $userId);
        $this->resolved = false;
    }

    public function logout(): void
    {
        $this->session->clear();
        $this->session->regenerate();
        $this->resolved = false;
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

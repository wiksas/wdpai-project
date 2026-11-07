<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

/**
 * Logowanie i bieżący użytkownik.
 */
final class AuthService
{
    public const LOGIN_OK = 'ok';
    public const LOGIN_INVALID = 'invalid';
    public const LOGIN_UNVERIFIED = 'unverified';

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
     * @return array{id: int, name: string, login: string, email: string, role: string, initials: string, label: string}|null
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
     * Sprawdza dane logowania. Przy poprawnych otwiera sesję, zapisuje czas logowania
     * i przelicza hash, jeśli powstał starszym algorytmem. Konto z niepotwierdzonym adresem
     * nie loguje się (status LOGIN_UNVERIFIED zwracamy dopiero po poprawnym haśle).
     *
     * @return self::LOGIN_* status logowania
     */
    public function attempt(string $login, string $password): string
    {
        $user = $this->users->findByLogin($login);

        // password_verify wykonujemy zawsze, żeby czas odpowiedzi nie zdradzał, czy konto istnieje.
        $valid = password_verify($password, $user['passwordHash'] ?? self::DUMMY_HASH);

        if ($user === null || !$valid) {
            return self::LOGIN_INVALID;
        }

        if (!$user['verified']) {
            return self::LOGIN_UNVERIFIED;
        }

        $rehash = password_needs_rehash($user['passwordHash'], self::HASH_ALGORITHM)
            ? password_hash($password, self::HASH_ALGORITHM)
            : null;
        $this->users->recordLogin((int) $user['id'], $rehash);
        $this->signIn((int) $user['id']);

        return self::LOGIN_OK;
    }

    /**
     * Zakłada konto z zahashowanym hasłem i niepotwierdzonym adresem (bez logowania:
     * najpierw link z e-maila). Gdy login lub e-mail ktoś zajął w międzyczasie, rzuca DuplicateUserException.
     *
     * @param list<string> $species
     * @return array<string, mixed>
     */
    public function register(string $login, string $name, string $email, string $password, string $level, array $species): array
    {
        return $this->users->create($login, $name, $email, password_hash($password, self::HASH_ALGORITHM), $level, $species);
    }

    public function emailTaken(string $email): bool
    {
        return $this->users->emailExists($email);
    }

    public function loginTaken(string $login): bool
    {
        return $this->users->loginExists($login);
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
     * @return array{id: int, name: string, login: string, email: string, role: string, initials: string, label: string}
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
            'login' => (string) $user['login'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
            'initials' => $initials !== '' ? $initials : '?',
            'label' => $user['role'] === 'admin'
                ? 'Administrator'
                : (self::LEVEL_LABELS[$user['level']] ?? 'Opiekun kolekcji'),
        ];
    }
}

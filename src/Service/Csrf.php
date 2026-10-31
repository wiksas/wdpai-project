<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Token CSRF zapisany w sesji. Formularze wysyłają go w polu `_csrf`,
 * a zapytania `fetch` w nagłówku `X-CSRF-Token`.
 */
final class Csrf
{
    private const KEY = 'csrf_token';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::KEY);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::KEY, $token);
        }

        return $token;
    }

    public function isValid(?string $candidate): bool
    {
        return is_string($candidate) && $candidate !== '' && hash_equals($this->token(), $candidate);
    }
}

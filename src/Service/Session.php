<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Cienka warstwa nad `$_SESSION`: jedyne miejsce, które dotyka tej tablicy.
 * Sesję startuje front controller (`public/index.php`).
 */
final class Session
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Zwraca wartość i od razu ją usuwa (np. komunikaty flash).
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);

        return $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }
}

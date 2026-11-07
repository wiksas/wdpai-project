<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Połączenie PDO z bazą PostgreSQL (usługa `db` z docker-compose).
 * Dane dostępowe pochodzą ze zmiennych środowiskowych kontenera PHP (`.env`), nie z kodu.
 * Jedno połączenie na żądanie HTTP, otwierane dopiero przy pierwszym zapytaniu.
 */
final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                self::env('DB_HOST', 'db'),
                self::env('DB_PORT', '5432'),
                self::env('POSTGRES_DB'),
            );

            self::$connection = new PDO($dsn, self::env('POSTGRES_USER'), self::env('POSTGRES_PASSWORD'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }

    private static function env(string $name, ?string $default = null): string
    {
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            return $value;
        }

        if ($default === null) {
            throw new \RuntimeException('Brak zmiennej środowiskowej ' . $name . ' (konfiguracja bazy danych w .env).');
        }

        return $default;
    }
}

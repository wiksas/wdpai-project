<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use PDOException;

/**
 * Połączenie PDO z PostgreSQL, dane dostępowe ze zmiennych środowiskowych (`.env`).
 * Jedno połączenie na żądanie, otwierane przy pierwszym zapytaniu.
 */
final class Database
{
    private const UNDEFINED_TABLE = '42P01';

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

    /**
     * Wykonuje operację; gdy brakuje tabeli (baza sprzed migracji), wykonuje plik migracji i powtarza operację raz.
     *
     * @template T
     * @param callable(): T $operation
     * @return T
     */
    public static function withMigration(string $migrationFile, callable $operation): mixed
    {
        try {
            return $operation();
        } catch (PDOException $exception) {
            if (($exception->errorInfo[0] ?? '') !== self::UNDEFINED_TABLE) {
                throw $exception;
            }

            self::connection()->exec((string) file_get_contents($migrationFile));

            return $operation();
        }
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

<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Polskie nazwy dni i miesięcy (bez zależności od locale systemowego).
 */
final class PolishDate
{
    public const TIMEZONE = 'Europe/Warsaw';

    private const DAYS = [1 => 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'];
    private const DAYS_SHORT = [1 => 'PN', 'WT', 'ŚR', 'CZ', 'PT', 'SO', 'ND'];
    private const MONTHS = [1 => 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];
    private const MONTHS_GENITIVE = [1 => 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'];
    private const MONTHS_LOCATIVE = [1 => 'styczniu', 'lutym', 'marcu', 'kwietniu', 'maju', 'czerwcu', 'lipcu', 'sierpniu', 'wrześniu', 'październiku', 'listopadzie', 'grudniu'];

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE));
    }

    public static function today(): DateTimeImmutable
    {
        return self::now()->setTime(0, 0);
    }

    /**
     * Parsuje datę `Y-m-d` (np. z parametru `?date=`); przy błędnej wartości zwraca null.
     */
    public static function parse(?string $value): ?DateTimeImmutable
    {
        if ($value === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone(self::TIMEZONE));
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }

        return $date;
    }

    public static function dayName(DateTimeImmutable $date): string
    {
        return self::DAYS[(int) $date->format('N')];
    }

    public static function dayShort(DateTimeImmutable $date): string
    {
        return self::DAYS_SHORT[(int) $date->format('N')];
    }

    public static function month(DateTimeImmutable $date): string
    {
        return self::MONTHS[(int) $date->format('n')];
    }

    public static function monthGenitive(DateTimeImmutable $date): string
    {
        return self::MONTHS_GENITIVE[(int) $date->format('n')];
    }

    public static function monthLocative(DateTimeImmutable $date): string
    {
        return self::MONTHS_LOCATIVE[(int) $date->format('n')];
    }

    /** „4 maja” */
    public static function dayMonth(DateTimeImmutable $date): string
    {
        return (int) $date->format('j') . ' ' . self::monthGenitive($date);
    }

    /** „Niedziela, 4 maja” */
    public static function full(DateTimeImmutable $date): string
    {
        return self::dayName($date) . ', ' . self::dayMonth($date);
    }

    /** Odmiana rzeczownika po liczbie: 1 zabieg, 2 zabiegi, 5 zabiegów. */
    public static function plural(int $count, string $one, string $few, string $many): string
    {
        if ($count === 1) {
            return $one;
        }

        $lastTwo = $count % 100;
        $last = $count % 10;

        return ($last >= 2 && $last <= 4 && ($lastTwo < 12 || $lastTwo > 14)) ? $few : $many;
    }

    /** „dziś”, „wczoraj”, „5 dni temu” — dla znacznika czasu z przeszłości */
    public static function ago(int $timestamp): string
    {
        $then = self::now()->setTimestamp($timestamp)->setTime(0, 0);
        $days = (int) $then->diff(self::today())->days;

        return match (true) {
            $days <= 0 => 'dziś',
            $days === 1 => 'wczoraj',
            default => $days . ' dni temu',
        };
    }

    /** „za 8 dni”, „jutro”, „dziś” */
    public static function inDays(int $days): string
    {
        return match (true) {
            $days <= 0 => 'dziś',
            $days === 1 => 'jutro',
            default => 'za ' . $days . ' ' . self::plural($days, 'dzień', 'dni', 'dni'),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Konto z tym loginem albo adresem e-mail już istnieje (naruszony unikalny indeks w bazie).
 * `$field` mówi, które pole formularza zgłosić: `login` albo `email`.
 */
final class DuplicateUserException extends \RuntimeException
{
    public function __construct(public readonly string $field)
    {
        parent::__construct('Zajęte pole: ' . $field);
    }
}

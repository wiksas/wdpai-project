<?php

declare(strict_types=1);

namespace App\Service;

/** Wiadomość nie została wysłana (brak połączenia z SMTP albo odrzucenie przez serwer). */
final class MailerException extends \RuntimeException
{
}

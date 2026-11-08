<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Wysyłka e-maili przez SMTP bez zewnętrznej biblioteki; w Dockerze serwerem jest Mailpit (`mailpit:1025`, UI na :8025).
 * Klient nie obsługuje TLS ani AUTH, więc produkcyjny serwer wymagałby ich dodania.
 */
final class Mailer
{
    private const TIMEOUT = 5;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $fromAddress,
        private readonly string $fromName,
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            (string) (getenv('MAIL_HOST') ?: 'mailpit'),
            (int) (getenv('MAIL_PORT') ?: 1025),
            (string) (getenv('MAIL_FROM') ?: 'no-reply@komorebi.local'),
            (string) (getenv('MAIL_FROM_NAME') ?: 'KOMOREBI'),
        );
    }

    /**
     * Wysyła wiadomość z wersją tekstową i HTML (multipart/alternative).
     *
     * @throws MailerException gdy serwer nie odpowiada albo odrzuci wiadomość
     */
    public function send(string $to, string $subject, string $text, string $html): void
    {
        // Adres i temat trafiają do nagłówków: znak nowej linii pozwoliłby dopisać własne nagłówki.
        if (preg_match('/[\r\n]/', $to . $subject) === 1 || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new MailerException('Nieprawidłowy adres odbiorcy lub temat.');
        }

        $socket = @fsockopen($this->host, $this->port, $errorCode, $errorMessage, self::TIMEOUT);
        if ($socket === false) {
            throw new MailerException(sprintf('Brak połączenia z serwerem SMTP %s:%d (%s).', $this->host, $this->port, $errorMessage));
        }

        stream_set_timeout($socket, self::TIMEOUT);

        try {
            $this->expect($socket, 220);
            $this->command($socket, 'EHLO komorebi.local', 250);
            $this->command($socket, 'MAIL FROM:<' . $this->fromAddress . '>', 250);
            $this->command($socket, 'RCPT TO:<' . $to . '>', 250, 251);
            $this->command($socket, 'DATA', 354);
            $this->command($socket, $this->message($to, $subject, $text, $html) . "\r\n.", 250);
            $this->command($socket, 'QUIT', 221);
        } finally {
            fclose($socket);
        }
    }

    private function message(string $to, string $subject, string $text, string $html): string
    {
        $boundary = 'komorebi-' . bin2hex(random_bytes(12));
        $domain = substr(strrchr($this->fromAddress, '@') ?: '@komorebi.local', 1);

        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'From: ' . mb_encode_mimeheader($this->fromName, 'UTF-8', 'B') . ' <' . $this->fromAddress . '>',
            'To: <' . $to . '>',
            'Subject: ' . mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n"),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $part = static fn (string $type, string $body): string => '--' . $boundary . "\r\n"
            . 'Content-Type: ' . $type . '; charset=UTF-8' . "\r\n"
            . 'Content-Transfer-Encoding: base64' . "\r\n\r\n"
            . rtrim(chunk_split(base64_encode($body), 76, "\r\n")) . "\r\n";

        // Treść jest w base64, więc żadna linia nie zaczyna się kropką (nie trzeba „dot-stuffingu”).
        return implode("\r\n", $headers) . "\r\n\r\n"
            . $part('text/plain', $text)
            . $part('text/html', $html)
            . '--' . $boundary . '--';
    }

    /**
     * @param resource $socket
     */
    private function command($socket, string $line, int ...$expected): void
    {
        fwrite($socket, $line . "\r\n");
        $this->expect($socket, ...$expected);
    }

    /**
     * Czyta odpowiedź SMTP (także wielolinijkową, „250-…”) i sprawdza jej kod.
     *
     * @param resource $socket
     */
    private function expect($socket, int ...$expected): void
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $expected, true)) {
            throw new MailerException('Serwer SMTP odpowiedział: ' . trim($response ?: 'brak odpowiedzi'));
        }
    }
}

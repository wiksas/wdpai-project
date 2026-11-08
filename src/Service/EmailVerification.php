<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\EmailVerificationRepository;
use App\Repository\UserRepository;

/**
 * Potwierdzanie adresu e-mail: token (32 bajty) trafia do linku, w bazie leży tylko jego skrót SHA-256.
 * Link jest jednorazowy i ważny 24 godziny; ponowna wysyłka unieważnia poprzedni i jest możliwa raz na minutę.
 */
final class EmailVerification
{
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_INVALID = 'invalid';

    private const TTL_SECONDS = 86400;
    private const RESEND_AFTER_SECONDS = 60;

    public function __construct(
        private readonly EmailVerificationRepository $tokens,
        private readonly UserRepository $users,
        private readonly Mailer $mailer,
        private readonly string $appUrl,
    ) {
    }

    /**
     * Tworzy nowy link i wysyła go na adres konta.
     *
     * @param array<string, mixed> $user
     * @throws MailerException
     */
    public function send(array $user): void
    {
        $token = bin2hex(random_bytes(32));
        $this->tokens->replace((int) $user['id'], hash('sha256', $token), self::TTL_SECONDS);

        $link = rtrim($this->appUrl, '/') . '/verify-email?token=' . $token;

        try {
            $this->mailer->send(
                (string) $user['email'],
                'Potwierdź adres e-mail w KOMOREBI',
                $this->textBody((string) $user['name'], (string) $user['login'], $link),
                $this->htmlBody((string) $user['name'], (string) $user['login'], $link),
            );
        } catch (MailerException $exception) {
            // Link, który nie dotarł, nie powinien blokować ponownej wysyłki limitem czasu.
            $this->tokens->deleteForUser((int) $user['id']);
            throw $exception;
        }
    }

    /**
     * Sprawdza token z linku i potwierdza adres.
     *
     * @return array{status: string, user: array<string, mixed>|null}
     */
    public function verify(string $token): array
    {
        if (preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            return ['status' => self::STATUS_INVALID, 'user' => null];
        }

        $record = $this->tokens->findByHash(hash('sha256', $token));
        $user = $record !== null ? $this->users->findById($record['userId']) : null;
        if ($record === null || $user === null) {
            return ['status' => self::STATUS_INVALID, 'user' => null];
        }

        if ($record['expired']) {
            return ['status' => self::STATUS_EXPIRED, 'user' => $user];
        }

        $this->users->markEmailVerified((int) $user['id']);
        $this->tokens->deleteForUser((int) $user['id']);

        return ['status' => self::STATUS_VERIFIED, 'user' => $user];
    }

    /**
     * Ponowna wysyłka dla loginu lub adresu. Nic nie robi, gdy konto nie istnieje, jest już potwierdzone
     * albo poprzedni link wysłano przed chwilą: odpowiedź dla użytkownika jest zawsze taka sama.
     *
     * @throws MailerException
     */
    public function resend(string $loginOrEmail): void
    {
        $user = $this->users->findByLogin($loginOrEmail);
        if ($user === null || $user['verified']) {
            return;
        }

        $since = $this->tokens->secondsSinceLast((int) $user['id']);
        if ($since !== null && $since < self::RESEND_AFTER_SECONDS) {
            return;
        }

        $this->send($user);
    }

    private function textBody(string $name, string $login, string $link): string
    {
        return "Cześć {$name},\n\n"
            . "dziękujemy za założenie konta w KOMOREBI (login: {$login}).\n"
            . "Aby je aktywować, otwórz link poniżej:\n\n"
            . "{$link}\n\n"
            . "Link działa 24 godziny i można go użyć raz.\n"
            . "Jeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.\n\n"
            . "KOMOREBI, dziennik opiekuna bonsai\n";
    }

    private function htmlBody(string $name, string $login, string $link): string
    {
        $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        // Klienty poczty nie znają zmiennych CSS, więc kolory palety są tu wpisane wprost (inline).
        return '<!DOCTYPE html><html lang="pl"><body style="margin:0;padding:24px;background:#fbf9f4;font-family:Georgia,serif;color:#1b1c19;">'
            . '<table role="presentation" width="100%" style="max-width:520px;margin:0 auto;background:#ffffff;border:1px solid #e4e2dd;border-radius:12px;">'
            . '<tr><td style="padding:32px;">'
            . '<p style="margin:0 0 4px;font-size:22px;color:#072417;">KOMOREBI</p>'
            . '<p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:13px;color:#424843;">Dziennik opiekuna bonsai</p>'
            . '<p style="margin:0 0 12px;font-size:18px;">Cześć ' . $e($name) . ',</p>'
            . '<p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:15px;line-height:1.6;color:#1b1c19;">'
            . 'dziękujemy za założenie konta (login: <strong>' . $e($login) . '</strong>). Potwierdź adres e-mail, aby się zalogować.</p>'
            . '<p style="margin:0 0 24px;"><a href="' . $e($link) . '" style="display:inline-block;padding:12px 24px;background:#072417;color:#ffffff;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;text-decoration:none;border-radius:2px;">Potwierdź adres e-mail</a></p>'
            . '<p style="margin:0 0 8px;font-family:Arial,sans-serif;font-size:13px;line-height:1.6;color:#424843;">Link działa 24 godziny i można go użyć raz. Jeśli przycisk nie działa, skopiuj adres:</p>'
            . '<p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:12px;word-break:break-all;"><a href="' . $e($link) . '" style="color:#486554;">' . $e($link) . '</a></p>'
            . '<p style="margin:0;font-family:Arial,sans-serif;font-size:13px;color:#424843;">Jeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.</p>'
            . '</td></tr></table></body></html>';
    }
}

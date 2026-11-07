<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\EmailVerification;
use App\Service\MailerException;

class VerificationController extends AppController
{
    /** Adres z ostatniej rejestracji w tej sesji (strona „Sprawdź skrzynkę” i domyślna wartość ponownej wysyłki). */
    public const PENDING_EMAIL = 'pending_verification_email';

    // Obsługa trasy: GET /verify-email (bez parametru: „Sprawdź skrzynkę”; ?token=…: link z e-maila)
    public function show(): void
    {
        $token = $_GET['token'] ?? null;

        if (is_string($token) && $token !== '') {
            $this->confirm($token);
            return;
        }

        $pending = $this->session()->get(self::PENDING_EMAIL);

        $this->renderPage(is_string($pending) ? 'sent' : 'resend', is_string($pending) ? $pending : '');
    }

    // Obsługa trasy: POST /verify-email/resend (pole `identifier`: login albo adres e-mail)
    public function resend(): void
    {
        $this->requireCsrf();

        $identifier = mb_substr(trim((string) ($_POST['identifier'] ?? '')), 0, 120);
        if ($identifier === '') {
            $this->flash('error', 'Podaj login albo adres e-mail konta.');
            $this->redirect('/verify-email');
        }

        try {
            $this->emailVerification()->resend($identifier);
        } catch (MailerException $exception) {
            error_log('KOMOREBI: nie wysłano e-maila weryfikacyjnego: ' . $exception->getMessage());
            $this->flash('error', 'Nie udało się wysłać wiadomości. Spróbuj ponownie za chwilę.');
            $this->redirect('/verify-email');
        }

        // Ta sama odpowiedź bez względu na to, czy konto istnieje: formularz nie zdradza, kto ma konto.
        // Dlatego nie przechodzimy do stanu „Sprawdź skrzynkę” (on zostaje tylko po własnej rejestracji w tej sesji).
        $this->flash('info', 'Jeśli konto czeka na potwierdzenie, wysłaliśmy nowy link. Kolejny można wysłać po minucie.');
        $this->redirect('/verify-email');
    }

    private function confirm(string $token): void
    {
        $result = $this->emailVerification()->verify($token);
        $user = $result['user'];

        if ($result['status'] === EmailVerification::STATUS_VERIFIED && $user !== null) {
            $this->session()->forget(self::PENDING_EMAIL);
            $this->flash('success', 'Adres e-mail potwierdzony. Zaloguj się, aby zacząć.');
            $this->redirect('/login?login=' . rawurlencode((string) $user['login']));
        }

        if ($result['status'] === EmailVerification::STATUS_EXPIRED && $user !== null) {
            http_response_code(410);
            $this->renderPage('expired', (string) $user['email']);
            return;
        }

        http_response_code(404);
        $this->renderPage('invalid', '');
    }

    private function renderPage(string $state, string $email): void
    {
        $titles = [
            'sent' => 'Sprawdź skrzynkę',
            'resend' => 'Wyślij link ponownie',
            'expired' => 'Link wygasł',
            'invalid' => 'Link nie działa',
        ];

        $this->render('verify-email', [
            'pageTitle' => $titles[$state] . ' | KOMOREBI',
            'state' => $state,
            'email' => $email,
            // Podgląd skrzynki deweloperskiej (Mailpit); pusty poza środowiskiem Docker.
            'mailPreviewUrl' => (string) (getenv('MAIL_PREVIEW_URL') ?: ''),
        ]);
    }
}

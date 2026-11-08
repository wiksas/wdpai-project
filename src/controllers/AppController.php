<?php

namespace App\Controllers;

use App\Repository\Database;
use App\Repository\EmailVerificationRepository;
use App\Repository\TreeRepository;
use App\Repository\UserRepository;
use App\Repository\UserStateRepository;
use App\Service\AuthService;
use App\Service\CollectionService;
use App\Service\Csrf;
use App\Service\EmailVerification;
use App\Service\Flash;
use App\Service\Mailer;
use App\Service\NotificationService;
use App\Service\ScheduleService;
use App\Service\Session;

class AppController
{
    /** Motywy wymuszane ciasteczkiem; bez ciasteczka obowiązuje motyw systemu (prefers-color-scheme). */
    protected const THEMES = ['light', 'dark'];
    protected const THEME_COOKIE = 'theme';

    private ?Session $session = null;
    private ?AuthService $auth = null;
    private ?UserRepository $users = null;
    private ?UserStateRepository $states = null;
    private ?CollectionService $collection = null;
    private ?ScheduleService $schedule = null;

    protected function session(): Session
    {
        return $this->session ??= new Session();
    }

    protected function users(): UserRepository
    {
        return $this->users ??= new UserRepository(Database::connection());
    }

    protected function auth(): AuthService
    {
        return $this->auth ??= new AuthService($this->users(), $this->session());
    }

    protected function emailVerification(): EmailVerification
    {
        return new EmailVerification(
            new EmailVerificationRepository(Database::connection()),
            $this->users(),
            Mailer::fromEnv(),
            (string) (getenv('APP_URL') ?: 'http://localhost:8080'),
        );
    }

    protected function csrf(): Csrf
    {
        return new Csrf($this->session());
    }

    protected function states(): UserStateRepository
    {
        return $this->states ??= new UserStateRepository();
    }

    protected function collection(): CollectionService
    {
        return $this->collection ??= new CollectionService(new TreeRepository(), $this->states());
    }

    protected function schedule(): ScheduleService
    {
        return $this->schedule ??= new ScheduleService($this->collection());
    }

    protected function notifications(): NotificationService
    {
        return new NotificationService($this->collection(), $this->schedule(), $this->states());
    }

    /**
     * Motyw z ciasteczka (`light`/`dark`) albo `system`, gdy użytkownik go nie wybrał.
     */
    protected function theme(): string
    {
        $theme = $_COOKIE[self::THEME_COOKIE] ?? null;

        return is_string($theme) && in_array($theme, self::THEMES, true) ? $theme : 'system';
    }

    protected function flash(string $type, string $message): void
    {
        (new Flash($this->session()))->add($type, $message);
    }

    /**
     * Zwraca zalogowanego użytkownika albo przekierowuje gościa na stronę logowania
     * (dla zapytań `fetch` zwraca JSON 401).
     *
     * @return array<string, mixed>
     */
    protected function requireLogin(): array
    {
        $user = $this->auth()->currentUser();
        if ($user === null) {
            if ($this->isAjax()) {
                $this->json(['ok' => false, 'message' => 'Sesja wygasła. Zaloguj się ponownie.', 'redirect' => '/login'], 401);
            }
            $this->redirect('/login');
        }

        return $user;
    }

    /**
     * Przerywa żądanie POST bez poprawnego tokenu CSRF (pole `_csrf` lub nagłówek `X-CSRF-Token`).
     */
    protected function requireCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if ($this->csrf()->isValid(is_string($token) ? $token : null)) {
            return;
        }

        $this->abort(419, 'Sesja formularza wygasła. Odśwież stronę i spróbuj ponownie.');
    }

    /**
     * Renderuje sam partial (bez pełnej strony), używane przez endpointy odświeżające fragmenty przez `fetch`.
     *
     * @param array<string, mixed> $variables
     */
    protected function renderPartial(string $partial, array $variables = []): never
    {
        $variables += [
            'currentUser' => $this->auth()->currentUser(),
            'csrfToken' => $this->csrf()->token(),
        ];
        extract($variables, EXTR_SKIP);

        header('Content-Type: text/html; charset=UTF-8');
        include __DIR__ . '/../../public/views/partials/' . $partial . '.html.php';
        exit;
    }

    /**
     * Zapytanie wysłane przez `fetch` z `public/js/app.js` (nagłówek `X-Requested-With: fetch`).
     */
    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Odpowiedź na akcję POST: JSON dla `fetch`, a dla zwykłego formularza komunikat flash i powrót na stronę.
     *
     * @param array<string, mixed> $data dodatkowe dane dla JSON
     */
    protected function respond(string $message, string $fallback, array $data = [], bool $ok = true, int $status = 200): never
    {
        if ($this->isAjax()) {
            $this->json(['ok' => $ok, 'message' => $message] + $data, $ok ? 200 : $status);
        }

        $this->flash($ok ? 'success' : 'error', $message);
        $back = $_POST['_back'] ?? $fallback;
        $this->redirect(is_string($back) ? $back : $fallback);
    }

    protected function abort(int $status, string $message): never
    {
        if ($this->isAjax()) {
            $this->json(['ok' => false, 'message' => $message], $status);
        }

        http_response_code($status);
        $this->render('not-found', [
            'pageTitle' => 'Błąd ' . $status . ' | KOMOREBI',
            'errorCode' => $status,
            'errorTitle' => 'Coś poszło nie tak',
            'errorMessage' => $message,
        ]);
        exit;
    }

    /**
     * Przekierowanie wewnątrz aplikacji. Odrzuca adresy zewnętrzne (ochrona przed open redirect).
     */
    protected function redirect(string $location): never
    {
        if (!str_starts_with($location, '/') || str_starts_with($location, '//') || str_contains($location, "\n") || str_contains($location, "\r")) {
            $location = '/';
        }

        header('Location: ' . $location, true, 303);
        exit;
    }

    protected function render(?string $template = null, array $variables = []): void
    {
        $templatePath = __DIR__ . '/../../public/views/' . $template . '.html.php';
        $output = 'File not found';

        // Widok może podać `currentUser` sam (np. strona 503, gdy baza nie odpowiada i nie da się go odczytać).
        $user = array_key_exists('currentUser', $variables) ? $variables['currentUser'] : $this->auth()->currentUser();

        $variables += [
            'currentUser' => $user,
            'csrfToken' => $this->csrf()->token(),
            'flashes' => (new Flash($this->session()))->pull(),
            'notifications' => $user !== null ? $this->notifications()->forUser($user['id']) : ['items' => [], 'unread' => 0],
            'sidebarSummary' => $user !== null ? $this->collection()->summary($user['id']) : null,
            'theme' => $this->theme(),
            'currentPath' => (string) ($_SERVER['REQUEST_URI'] ?? '/'),
        ];

        if (file_exists($templatePath)) {
            extract($variables, EXTR_SKIP);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        echo $output;
    }

    /**
     * Strona 503, gdy baza danych nie odpowiada. Nie odpytuje bazy (gość, bez powiadomień).
     */
    public function databaseUnavailable(): void
    {
        http_response_code(503);
        header('Retry-After: 30');
        $this->render('not-found', [
            'pageTitle' => 'Serwis chwilowo niedostępny | KOMOREBI',
            'currentUser' => null,
            'errorCode' => 503,
            'errorTitle' => 'Baza danych chwilowo nie odpowiada',
            'errorMessage' => 'Nie możemy teraz odczytać kont ani zapisać zmian. Spróbuj ponownie za chwilę.',
        ]);
    }

    /**
     * Strona 500 dla nieobsłużonego błędu. Nie odpytuje bazy ani sesji, bo to one mogą być przyczyną.
     */
    public function serverError(): void
    {
        http_response_code(500);
        $this->render('not-found', [
            'pageTitle' => 'Błąd serwera | KOMOREBI',
            'currentUser' => null,
            'errorCode' => 500,
            'errorTitle' => 'Coś poszło nie tak po naszej stronie',
            'errorMessage' => 'Nie udało się dokończyć tej operacji. Spróbuj ponownie za chwilę.',
        ]);
    }
}

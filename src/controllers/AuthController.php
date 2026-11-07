<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repository\DuplicateUserException;
use App\Service\AuthService;
use App\Service\MailerException;

class AuthController extends AppController
{
    /** Login: zaczyna się literą, potem litery, cyfry, `.`, `_`, `-` (razem 3–30). Bez `@`, więc nie pomyli się z e-mailem. */
    private const LOGIN_PATTERN = '/^[a-z][a-z0-9._-]{2,29}$/';
    private const RESERVED_LOGINS = ['admin', 'administrator', 'root', 'komorebi', 'support', 'system'];
    private const LEVELS = ['beginner', 'enthusiast', 'master'];
    private const SPECIES = ['pinus', 'acer', 'juniperus', 'ficus', 'ulmus'];

    // Obsługa trasy: GET /login (opcjonalnie ?login=…: wpisany login po potwierdzeniu adresu e-mail)
    public function login(): void
    {
        $this->redirectIfLoggedIn();

        $prefill = $_GET['login'] ?? '';

        $this->render('login', [
            'pageTitle' => 'Logowanie | KOMOREBI',
            'errors' => [],
            'old' => ['login' => is_string($prefill) ? mb_substr(trim($prefill), 0, 120) : ''],
        ]);
    }

    // Obsługa trasy: POST /login
    public function authenticate(): void
    {
        $this->requireCsrf();

        $this->redirectIfLoggedIn();

        $login = trim((string) ($_POST['login'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($login === '' || $password === '') {
            $this->renderLoginError('Podaj login lub adres e-mail oraz hasło.', $login);
            return;
        }

        $status = $this->auth()->attempt($login, $password);

        if ($status === AuthService::LOGIN_UNVERIFIED) {
            $this->renderLoginError('Najpierw potwierdź adres e-mail: kliknij link z wiadomości, którą wysłaliśmy po rejestracji.', $login, true);
            return;
        }

        if ($status !== AuthService::LOGIN_OK) {
            $this->renderLoginError('Nieprawidłowy login lub hasło.', $login);
            return;
        }

        $this->redirect('/collection');
    }

    // Obsługa trasy: GET /register
    public function register(): void
    {
        $this->redirectIfLoggedIn();

        $this->render('register', [
            'pageTitle' => 'Nowe konto | KOMOREBI',
            'errors' => [],
            'old' => $this->registerDefaults(),
        ]);
    }

    // Obsługa trasy: POST /register
    public function store(): void
    {
        $this->requireCsrf();

        $this->redirectIfLoggedIn();

        $login = mb_strtolower(trim((string) ($_POST['login'] ?? '')));
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $passwordRepeat = (string) ($_POST['password_repeat'] ?? '');
        $level = (string) ($_POST['experience'] ?? '');
        $species = array_values(array_intersect(self::SPECIES, (array) ($_POST['species'] ?? [])));
        $termsAccepted = ($_POST['terms'] ?? '') === '1';

        $errors = [];

        if (preg_match(self::LOGIN_PATTERN, $login) !== 1) {
            $errors['login'] = 'Login musi mieć 3–30 znaków: litery bez polskich znaków, cyfry, kropka, _ lub -, na początku litera.';
        } elseif (in_array($login, self::RESERVED_LOGINS, true) || $this->auth()->loginTaken($login)) {
            $errors['login'] = 'Ten login jest już zajęty. Wybierz inny.';
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 60) {
            $errors['name'] = 'Podaj imię (od 2 do 60 znaków).';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
            $errors['email'] = 'Podaj poprawny adres e-mail.';
        }

        if (mb_strlen($password) < 8 || mb_strlen($password) > 128) {
            $errors['password'] = 'Hasło musi mieć od 8 do 128 znaków.';
        } elseif (!hash_equals($password, $passwordRepeat)) {
            $errors['password_repeat'] = 'Hasła nie są takie same.';
        }

        if (!in_array($level, self::LEVELS, true)) {
            $level = 'beginner';
        }

        if (!$termsAccepted) {
            $errors['terms'] = 'Zaznacz to pole, aby założyć konto.';
        }

        if (!isset($errors['email']) && $this->auth()->emailTaken($email)) {
            $errors['email'] = 'Konto z tym adresem e-mail już istnieje. Zaloguj się albo użyj innego adresu.';
        }

        // Unikalne indeksy w bazie łapią też wyścig dwóch rejestracji tym samym loginem lub adresem.
        $user = null;
        if ($errors === []) {
            try {
                $user = $this->auth()->register($login, $name, $email, $password, $level, $species);
            } catch (DuplicateUserException $duplicate) {
                $errors[$duplicate->field] = $duplicate->field === 'login'
                    ? 'Ten login jest już zajęty. Wybierz inny.'
                    : 'Konto z tym adresem e-mail już istnieje. Zaloguj się albo użyj innego adresu.';
            }
        }

        if ($errors !== [] || $user === null) {
            http_response_code(422);
            $this->render('register', [
                'pageTitle' => 'Nowe konto | KOMOREBI',
                'errors' => $errors,
                'old' => ['login' => $login, 'name' => $name, 'email' => $email, 'experience' => $level, 'species' => $species, 'terms' => $termsAccepted],
            ]);
            return;
        }

        // Konto czeka na potwierdzenie adresu: link przychodzi e-mailem, logowanie dopiero po kliknięciu.
        $this->session()->set(VerificationController::PENDING_EMAIL, $email);
        try {
            $this->emailVerification()->send($user);
        } catch (MailerException $exception) {
            error_log('KOMOREBI: nie wysłano e-maila weryfikacyjnego: ' . $exception->getMessage());
            $this->flash('error', 'Konto zostało założone, ale nie udało się wysłać e-maila. Wyślij link ponownie za chwilę.');
        }

        $this->redirect('/verify-email');
    }

    // Obsługa trasy: POST /logout
    public function logout(): void
    {
        $this->requireCsrf();

        $this->auth()->logout();
        $this->redirect('/');
    }

    private function redirectIfLoggedIn(): void
    {
        if ($this->auth()->currentUser() !== null) {
            $this->redirect('/collection');
        }
    }

    private function renderLoginError(string $message, string $login, bool $unverified = false): void
    {
        http_response_code(401);
        $this->render('login', [
            'pageTitle' => 'Logowanie | KOMOREBI',
            'errors' => ['form' => $message],
            'unverified' => $unverified,
            'old' => ['login' => $login],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function registerDefaults(): array
    {
        return ['login' => '', 'name' => '', 'email' => '', 'experience' => 'beginner', 'species' => [], 'terms' => false];
    }
}

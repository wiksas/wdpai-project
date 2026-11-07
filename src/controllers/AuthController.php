<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repository\UserRepository;

class AuthController extends AppController
{
    private const LEVELS = ['beginner', 'enthusiast', 'master'];
    private const SPECIES = ['pinus', 'acer', 'juniperus', 'ficus', 'ulmus'];

    // Obsługa trasy: GET /login
    public function login(): void
    {
        $this->redirectIfLoggedIn();

        $this->render('login', [
            'pageTitle' => 'Logowanie | KOMOREBI',
            'errors' => [],
            'old' => ['login' => ''],
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

        if (!$this->auth()->attempt($login, $password)) {
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

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $level = (string) ($_POST['experience'] ?? '');
        $species = array_values(array_intersect(self::SPECIES, (array) ($_POST['species'] ?? [])));
        $termsAccepted = ($_POST['terms'] ?? '') === '1';

        $errors = [];

        if (mb_strlen($name) < 2 || mb_strlen($name) > 60) {
            $errors['name'] = 'Podaj imię (od 2 do 60 znaków).';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
            $errors['email'] = 'Podaj poprawny adres e-mail.';
        }

        if (mb_strlen($password) < 8) {
            $errors['password'] = 'Hasło musi mieć co najmniej 8 znaków.';
        }

        if (!in_array($level, self::LEVELS, true)) {
            $level = 'beginner';
        }

        if (!$termsAccepted) {
            $errors['terms'] = 'Zaznacz to pole, aby założyć konto.';
        }

        $users = new UserRepository();
        if (!isset($errors['email']) && $users->findByLogin($email) !== null) {
            $errors['email'] = 'Konto z tym adresem e-mail już istnieje.';
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->render('register', [
                'pageTitle' => 'Nowe konto | KOMOREBI',
                'errors' => $errors,
                'old' => ['name' => $name, 'email' => $email, 'experience' => $level, 'species' => $species, 'terms' => $termsAccepted],
            ]);
            return;
        }

        $user = $users->create($name, $email, password_hash($password, PASSWORD_DEFAULT), $level, $species);
        $this->auth()->signIn($user['id']);

        $this->redirect('/collection');
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

    private function renderLoginError(string $message, string $login): void
    {
        http_response_code(401);
        $this->render('login', [
            'pageTitle' => 'Logowanie | KOMOREBI',
            'errors' => ['form' => $message],
            'old' => ['login' => $login],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function registerDefaults(): array
    {
        return ['name' => '', 'email' => '', 'experience' => 'beginner', 'species' => [], 'terms' => false];
    }
}

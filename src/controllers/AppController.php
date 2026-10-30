<?php

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Service\AuthService;

class AppController
{
    private ?AuthService $auth = null;

    protected function auth(): AuthService
    {
        return $this->auth ??= new AuthService(new UserRepository());
    }

    /**
     * Zwraca zalogowanego użytkownika albo przekierowuje gościa na stronę logowania.
     *
     * @return array<string, mixed>
     */
    protected function requireLogin(): array
    {
        $user = $this->auth()->currentUser();
        if ($user === null) {
            $this->redirect('/login');
        }

        return $user;
    }

    protected function redirect(string $location): never
    {
        header('Location: ' . $location, true, 303);
        exit;
    }

    protected function render(?string $template = null, array $variables = []): void
    {
        $templatePath = __DIR__ . '/../../public/views/' . $template . '.html.php';
        $output = 'File not found';

        // Każdy widok (nagłówek, panel boczny) wie, czy ktoś jest zalogowany.
        $variables += ['currentUser' => $this->auth()->currentUser()];

        if (file_exists($templatePath)) {
            extract($variables, EXTR_SKIP);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        echo $output;
    }
}

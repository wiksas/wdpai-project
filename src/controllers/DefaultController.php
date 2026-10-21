<?php

namespace App\Controllers;

class DefaultController extends AppController
{
    public function index(): void
    {
        // Przekazanie wartości do widoku strony głównej
        $this->render('home', [
            'appName' => 'ProjectFlow',
            'welcomeMessage' => 'Zarządzaj swoimi projektami w jednym miejscu.'
        ]);
    }

    public function login(): void
    {
        $this->render('login', ['pageTitle' => 'Logowanie do systemu']);
    }

    public function register(): void
    {
        $this->render('register', ['pageTitle' => 'Utwórz nowe konto']);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'errorMessage' => 'Strona o podanym adresie nie istnieje.'
        ]);
    }
}
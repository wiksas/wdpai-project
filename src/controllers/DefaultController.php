<?php

namespace App\Controllers;

class DefaultController extends AppController
{
    public function index(): void
    {
        $this->render('home', ['pageTitle' => 'KOMOREBI — Sztuka cierpliwości wsparta precyzyjną botaniką']);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Nie znaleziono strony — KOMOREBI',
            'errorMessage' => 'Strona o podanym adresie nie istnieje.',
        ]);
    }
}

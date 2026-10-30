<?php

declare(strict_types=1);

namespace App\Controllers;

class StudioController extends AppController
{
    // Obsługa trasy: /studio — Studio Formowania, Cięcia i Drutowania
    public function index(): void
    {
        $this->requireLogin();

        $this->render('studio', [
            'pageTitle' => 'Studio Formowania — KOMOREBI',
            'activeNav' => 'studio',
        ]);
    }
}

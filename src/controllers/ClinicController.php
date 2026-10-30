<?php

declare(strict_types=1);

namespace App\Controllers;

class ClinicController extends AppController
{
    // Obsługa trasy: /clinic — Klinika AI i diagnostyka zdrowia drzewek
    public function index(): void
    {
        $this->requireLogin();

        $this->render('clinic', [
            'pageTitle' => 'Klinika AI — KOMOREBI',
            'activeNav' => 'clinic',
        ]);
    }
}

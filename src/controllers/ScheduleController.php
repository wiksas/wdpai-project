<?php

declare(strict_types=1);

namespace App\Controllers;

class ScheduleController extends AppController
{
    // Obsługa trasy: /schedule — Harmonogram zabiegów i zadań
    public function index(): void
    {
        $this->requireLogin();

        $this->render('schedule', [
            'pageTitle' => 'Harmonogram zabiegów — KOMOREBI',
            'activeNav' => 'schedule',
        ]);
    }
}

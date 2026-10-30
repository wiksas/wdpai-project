<?php

namespace App\Controllers;

use App\Repository\TreeRepository;

class ProjectController extends AppController
{
    // Obsługa trasy zwykłej: /projects — panel „Moja Kolekcja”
    public function projects(): void
    {
        $this->requireLogin();

        $trees = (new TreeRepository())->findAll();

        $this->render('projects', [
            'pageTitle' => 'Moja Kolekcja — KOMOREBI',
            'activeNav' => 'collection',
            'trees' => $trees,
            'treeCount' => count($trees),
        ]);
    }

    // Obsługa trasy z parametrem: /project/{id} — karta okazu
    public function project(string $id): void
    {
        $this->requireLogin();

        $tree = ctype_digit($id) ? (new TreeRepository())->findById((int) $id) : null;

        if ($tree === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Nie znaleziono okazu — KOMOREBI',
                'errorMessage' => 'Okaz o podanym identyfikatorze nie istnieje w kolekcji.',
            ]);
            return;
        }

        $this->render('project', [
            'pageTitle' => $tree['name'] . ' — Karta okazu — KOMOREBI',
            'activeNav' => 'collection',
            'tree' => $tree,
        ]);
    }
}

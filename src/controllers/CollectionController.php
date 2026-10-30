<?php

namespace App\Controllers;

use App\Repository\TreeRepository;

class CollectionController extends AppController
{
    // Obsługa trasy zwykłej: GET /collection — panel „Moja Kolekcja”
    public function index(): void
    {
        $this->requireLogin();

        $trees = (new TreeRepository())->findAll();

        $this->render('collection', [
            'pageTitle' => 'Moja Kolekcja — KOMOREBI',
            'activeNav' => 'collection',
            'trees' => $trees,
            'treeCount' => count($trees),
        ]);
    }

    // Obsługa trasy z parametrem: GET /collection/{id} — karta okazu
    public function show(string $id): void
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

        $this->render('specimen', [
            'pageTitle' => $tree['name'] . ' — Karta okazu — KOMOREBI',
            'activeNav' => 'collection',
            'tree' => $tree,
        ]);
    }
}

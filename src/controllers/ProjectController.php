<?php

namespace App\Controllers;

class ProjectController extends AppController
{
    // Obsługa trasy zwykłej: /projects
    public function projects(): void
    {
        $projectsList = [
            ['id' => 1, 'name' => 'Aplikacja Webowa WDPAI', 'status' => 'W trakcie', 'author' => 'Jan Kowalski'],
            ['id' => 2, 'name' => 'Redesign Sklepu UI/UX', 'status' => 'Zakończony', 'author' => 'Anna Nowak'],
            ['id' => 3, 'name' => 'Integracja API Płatności', 'status' => 'Planowany', 'author' => 'Piotr Wiśniewski']
        ];

        // 4. Przekazanie co najmniej dwóch wartości z kontrolera do widoku
        $this->render('projects', [
            'title' => 'Panel główny - Lista Projektów',
            'totalCount' => count($projectsList),
            'projects' => $projectsList
        ]);
    }

    // Obsługa trasy z parametrem: /project/{id}
    public function project(string $id): void
    {
        $this->render('project', [
            'projectId' => $id,
            'projectTitle' => "Szczegóły projektu #" . htmlspecialchars($id),
            'projectOwner' => 'Zespół deweloperski Alpha'
        ]);
    }
}
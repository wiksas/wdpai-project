<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\MediaStorage;

class MediaController extends AppController
{
    // Obsługa trasy: GET /media/{name}, zdjęcie zalogowanego użytkownika (cudze pliki nie istnieją w jego katalogu)
    public function show(string $name): void
    {
        $user = $this->requireLogin();
        $storage = new MediaStorage();
        $path = $storage->path($user['id'], $name);

        if ($path === null) {
            http_response_code(404);
            exit;
        }

        header('Content-Type: ' . $storage->mime($name));
        header('Content-Length: ' . (string) filesize($path));
        header('Cache-Control: private, max-age=86400, immutable');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}

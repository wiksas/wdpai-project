<?php

declare(strict_types=1);

namespace App\Controllers;

class NotificationController extends AppController
{
    // Obsługa trasy: GET /notifications, gotowy HTML dzwonka (odświeżanie panelu bez przeładowania strony)
    public function index(): void
    {
        $user = $this->requireLogin();

        $this->renderPartial('components/notifications', ['notifications' => $this->notifications()->forUser($user['id'])]);
    }

    // Obsługa trasy: POST /notifications/read — oznacz jedno powiadomienie (pole `id`) lub wszystkie jako przeczytane
    public function read(): void
    {
        $user = $this->requireLogin();
        $this->requireCsrf();

        $id = isset($_POST['id']) && is_string($_POST['id']) && $_POST['id'] !== '' ? substr($_POST['id'], 0, 120) : null;
        $this->notifications()->markRead($user['id'], $id);

        $unread = $this->notifications()->forUser($user['id'])['unread'];

        $this->respond($id === null ? 'Oznaczono wszystkie jako przeczytane.' : 'Oznaczono jako przeczytane.', '/collection', ['unread' => $unread]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

class ThemeController extends AppController
{
    // Obsługa trasy: POST /theme — pole `theme`: light|dark zapisuje wybór w ciasteczku na rok,
    // `system` usuwa ciasteczko i strona znów podąża za motywem ustawionym w systemie (prefers-color-scheme).
    public function update(): void
    {
        $this->requireCsrf();

        $theme = $_POST['theme'] ?? '';
        $followSystem = $theme === 'system';
        if (!is_string($theme) || (!$followSystem && !in_array($theme, self::THEMES, true))) {
            $this->respond('Nieznany motyw.', '/', [], false, 422);
        }

        setcookie(self::THEME_COOKIE, $followSystem ? '' : $theme, [
            'expires' => $followSystem ? time() - 3600 : time() + 365 * 24 * 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $message = match ($theme) {
            'system' => 'Motyw zgodny z ustawieniami systemu.',
            'dark' => 'Włączono ciemny motyw.',
            default => 'Włączono jasny motyw.',
        };

        $this->respond($message, '/', ['theme' => $theme]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

class ThemeController extends AppController
{
    // Obsługa trasy: POST /theme — zapisuje wybrany motyw (pole `theme`: light|dark) w ciasteczku na rok
    public function update(): void
    {
        $this->requireCsrf();

        $theme = $_POST['theme'] ?? '';
        if (!is_string($theme) || !in_array($theme, self::THEMES, true)) {
            $this->respond('Nieznany motyw.', '/', [], false, 422);
        }

        setcookie(self::THEME_COOKIE, $theme, [
            'expires' => time() + 365 * 24 * 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $this->respond($theme === 'dark' ? 'Włączono ciemny motyw.' : 'Włączono jasny motyw.', '/', ['theme' => $theme]);
    }
}

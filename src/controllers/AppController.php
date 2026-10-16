<?php

class AppController {

    protected function render(string $template_name = null) {
        // Używamy __DIR__ żeby wyjść z src/controllers do głównego folderu i wejść w public/views
        $template_path = __DIR__ . '/../../public/views/' . $template_name . '.html.php';
        
        // Sprawdzenie czy plik istnieje
        if(!file_exists($template_path)) {
            $template_path = __DIR__ . '/../../public/views/not-found.html.php';
        }
        
        include $template_path;
    }
}
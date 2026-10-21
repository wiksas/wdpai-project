<?php

namespace App;

use App\Controllers\DefaultController;

class Routing
{
    public static array $routes = [];

    public static function get(string $url, string $controller, string $action): void
    {
        self::$routes[$url] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public static function run(string $path): void
    {
        foreach (self::$routes as $route => $target) {
            // Zamiana {param} na wyrażenie regularne dopasowujące parametr w URL
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Usunięcie pełnego dopasowania, zostają same parametry

                $controllerClass = 'App\\Controllers\\' . $target['controller'];
                $action = $target['action'];

                $controllerObj = new $controllerClass();
                $controllerObj->$action(...$matches);
                return;
            }
        }

        // 6. Obsługa 404 dla nieobsługiwanego adresu
        http_response_code(404);
        $notFoundController = new DefaultController();
        $notFoundController->notFound();
    }
}
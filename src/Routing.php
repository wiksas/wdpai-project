<?php

namespace App;

use App\Controllers\DefaultController;

class Routing
{
    /** @var array<string, array<string, array{controller: string, action: string}>> trasy pogrupowane po metodzie HTTP */
    public static array $routes = [];

    public static function get(string $url, string $controller, string $action): void
    {
        self::add('GET', $url, $controller, $action);
    }

    public static function post(string $url, string $controller, string $action): void
    {
        self::add('POST', $url, $controller, $action);
    }

    private static function add(string $method, string $url, string $controller, string $action): void
    {
        self::$routes[$method][$url] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public static function run(string $path, string $method = 'GET'): void
    {
        foreach (self::$routes[$method] ?? [] as $route => $target) {
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

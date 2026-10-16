<?php

namespace App;

require_once __DIR__ . '/controllers/DefaultController.php';
require_once __DIR__ . '/controllers/ProjectController.php';

class Routing {

    public static $routes = [
        "" => [
            "controller" => "DefaultController",
            "action" => "login"
        ],
        "login" => [
            "controller" => "DefaultController",
            "action" => "login"
        ],
        "dashboard" => [
            "controller" => "ProjectController",
            "action" => "dashboard"
        ],
        "specimen" => [
            "controller" => "ProjectController",
            "action" => "specimen"
        ],
        // Aliasy ze starych nazw, żeby żaden link nie wyrzucił błędu 404:
        "home" => [
            "controller" => "DefaultController",
            "action" => "login"
        ],
        "projects" => [
            "controller" => "ProjectController",
            "action" => "dashboard"
        ],
        "project" => [
            "controller" => "ProjectController",
            "action" => "specimen"
        ]
    ];

    public static function run(string $path) {
        $path = trim($path, '/');
        
        if (array_key_exists($path, self::$routes)) {
            $controller = self::$routes[$path]["controller"];
            $action = self::$routes[$path]["action"];

            $controllerObj = new $controller;
            $controllerObj->$action();
        } else {
            $controllerObj = new DefaultController();
            $controllerObj->notFound();
        }
    }
}
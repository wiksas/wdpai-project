<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Routing;

// Sesja logowania: ciasteczko niedostępne dla JS i niewysyłane w żądaniach między witrynami.
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = (string) parse_url($path, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// 1. Trasa główna przez DefaultController
Routing::get('', 'DefaultController', 'index');

// 2. Konto: logowanie, rejestracja i wylogowanie
Routing::get('login', 'AuthController', 'login');
Routing::post('login', 'AuthController', 'authenticate');
Routing::get('register', 'AuthController', 'register');
Routing::post('register', 'AuthController', 'store');
Routing::post('logout', 'AuthController', 'logout');

// 3. Kolekcja: lista okazów i karta pojedynczego okazu ({id})
Routing::get('collection', 'CollectionController', 'index');
Routing::get('collection/{id}', 'CollectionController', 'show');

// 4. Moduły pielęgnacji: studio formowania, klinika AI i harmonogram
Routing::get('studio', 'StudioController', 'index');
Routing::get('clinic', 'ClinicController', 'index');
Routing::get('schedule', 'ScheduleController', 'index');

Routing::run($path, $method);

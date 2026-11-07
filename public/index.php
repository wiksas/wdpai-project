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

// 3. Kolekcja: lista okazów, karta okazu ({id}) oraz zabiegi, zdjęcia i dziennik
Routing::get('collection', 'CollectionController', 'index');
Routing::get('collection/{id}', 'CollectionController', 'show');
Routing::post('collection/{id}/care', 'CollectionController', 'care');
Routing::post('collection/{id}/photos', 'CollectionController', 'addPhoto');
Routing::post('collection/{id}/photos/delete', 'CollectionController', 'deletePhoto');
Routing::get('media/{name}', 'MediaController', 'show');
Routing::post('journal', 'CollectionController', 'journal');

// 4. Moduły pielęgnacji: studio formowania, klinika i harmonogram z zadaniami
Routing::get('studio', 'StudioController', 'index');
Routing::get('clinic', 'ClinicController', 'index');
Routing::post('clinic/scans', 'ClinicController', 'scan');
Routing::post('clinic/ask', 'ClinicController', 'ask');
Routing::post('clinic/protocol', 'ClinicController', 'protocol');
Routing::get('schedule', 'ScheduleController', 'index');
Routing::post('schedule/tasks', 'ScheduleController', 'store');
Routing::post('schedule/tasks/done', 'ScheduleController', 'done');
Routing::post('schedule/tasks/delete', 'ScheduleController', 'delete');

// 5. Powiadomienia (dzwonek)
Routing::get('notifications', 'NotificationController', 'index');
Routing::post('notifications/read', 'NotificationController', 'read');

// 6. Motyw jasny/ciemny (ciasteczko)
Routing::post('theme', 'ThemeController', 'update');

Routing::run($path, $method);

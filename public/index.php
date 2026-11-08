<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AppController;
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

Routing::get('', 'DefaultController', 'index');

// Konto: logowanie, rejestracja, wylogowanie i potwierdzenie adresu e-mail
Routing::get('login', 'AuthController', 'login');
Routing::post('login', 'AuthController', 'authenticate');
Routing::get('register', 'AuthController', 'register');
Routing::post('register', 'AuthController', 'store');
Routing::post('logout', 'AuthController', 'logout');
Routing::get('verify-email', 'VerificationController', 'show');
Routing::post('verify-email/resend', 'VerificationController', 'resend');

Routing::get('collection', 'CollectionController', 'index');
Routing::get('collection/new', 'CollectionController', 'create');
Routing::post('collection', 'CollectionController', 'store');
Routing::get('collection/{id}', 'CollectionController', 'show');
Routing::post('collection/{id}/care', 'CollectionController', 'care');
Routing::post('collection/{id}/delete', 'CollectionController', 'destroy');
Routing::post('collection/{id}/scan', 'CollectionController', 'scan');
Routing::post('collection/{id}/photos', 'CollectionController', 'addPhoto');
Routing::post('collection/{id}/photos/delete', 'CollectionController', 'deletePhoto');
Routing::get('media/{name}', 'MediaController', 'show');
Routing::post('journal', 'CollectionController', 'journal');

Routing::get('studio', 'StudioController', 'index');
Routing::get('clinic', 'ClinicController', 'index');
Routing::post('clinic/analyze', 'ClinicController', 'analyze');
Routing::post('clinic/scans', 'ClinicController', 'scan');
Routing::post('clinic/ask', 'ClinicController', 'ask');
Routing::post('clinic/protocol', 'ClinicController', 'protocol');
Routing::get('schedule', 'ScheduleController', 'index');
Routing::post('schedule/tasks', 'ScheduleController', 'store');
Routing::post('schedule/tasks/done', 'ScheduleController', 'done');
Routing::post('schedule/tasks/delete', 'ScheduleController', 'delete');

Routing::get('notifications', 'NotificationController', 'index');
Routing::post('notifications/read', 'NotificationController', 'read');

Routing::post('theme', 'ThemeController', 'update');

try {
    Routing::run($path, $method);
} catch (PDOException $exception) {
    // Baza nie odpowiada (np. kontener `db` nie działa): czytelna strona 503 zamiast błędu PHP.
    error_log('KOMOREBI: baza danych niedostępna: ' . $exception->getMessage());
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
        http_response_code(503);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'message' => 'Baza danych chwilowo nie odpowiada. Spróbuj ponownie za chwilę.'], JSON_UNESCAPED_UNICODE);
    } else {
        (new AppController())->databaseUnavailable();
    }
} catch (Throwable $exception) {
    // Nieobsłużony błąd: do logu trafia przyczyna, a przeglądarka (także `fetch`) dostaje czytelną odpowiedź.
    error_log('KOMOREBI: ' . $exception::class . ': ' . $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'message' => 'Wystąpił błąd serwera. Spróbuj ponownie za chwilę.'], JSON_UNESCAPED_UNICODE);
    } else {
        (new AppController())->serverError();
    }
}

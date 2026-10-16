<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Routing.php';

use App\Routing;

$path = isset($_GET['page']) ? $_GET['page'] : '';
if (isset($_SERVER['PATH_INFO'])) {
    $path = trim($_SERVER['PATH_INFO'], '/');
} else {
    // Alternatywne pobieranie ścieżki z REQUEST_URI
    $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $path = $requestUri;
}

Routing::run($path);
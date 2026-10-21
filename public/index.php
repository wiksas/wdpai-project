<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Routing;

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

// 1. Trasa główna przez DefaultController
Routing::get('', 'DefaultController', 'index');
Routing::get('login', 'DefaultController', 'login');
Routing::get('register', 'DefaultController', 'register');

// 3. Trasa ze zwykłym adresem i trasa z parametrem {id}
Routing::get('projects', 'ProjectController', 'projects');
Routing::get('project/{id}', 'ProjectController', 'project');

Routing::run($path);
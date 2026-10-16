<?php

require_once __DIR__ . '/AppController.php';

class DefaultController extends AppController {

    public function login() {
        $this->render('login');
    }

    public function notFound() {
        http_response_code(404);
        $this->render('not-found');
    }
}
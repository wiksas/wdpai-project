<?php

require_once __DIR__ . '/AppController.php';

class ProjectController extends AppController {

    public function dashboard() {
        $this->render('dashboard');
    }

    public function specimen() {
        $this->render('specimen');
    }
}
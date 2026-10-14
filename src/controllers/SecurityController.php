<?php

declare(strict_types=1);

namespace App\Controller;

final class SecurityController extends AppController
{
    public function login(): void
    {
        $this->render('login', [
            'pageTitle' => 'Logowanie - MealPlanner',
            'subtitle' => 'Zaloguj się do swojego planu żywieniowego',
        ]);
    }

    public function register(): void
    {
        $this->render('register', [
            'pageTitle' => 'Rejestracja - MealPlanner',
            'subtitle' => 'Załóż darmowe konto i planuj posiłki',
        ]);
    }
}
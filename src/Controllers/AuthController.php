<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class AuthController
{
    public function __construct(private View $view)
    {
    }

    public function login(): void
    {
        $this->view->render('auth/login', ['pageTitle' => 'Воридшавӣ']);
    }

    public function register(): void
    {
        $this->view->render('auth/register', ['pageTitle' => 'Бақайдгирӣ']);
    }
}

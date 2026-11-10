<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Validator;
use App\Core\View;
use App\Repositories\UserRepository;

class AuthController {
    public function loginForm(): void {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        View::render('auth.login', ['title' => 'Воридшавӣ ба низом']);
    }

    public function login(): void {
        Csrf::checkOrAbort();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $userRepo = new UserRepository();
        $user = $userRepo->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Логин ё рамз нодуруст аст.';
            header('Location: /login');
            exit;
        }

        if (empty($user['is_active'])) {
            $_SESSION['flash_error'] = 'Ҳисоби корбарии шумо ғайрифаъол аст.';
            header('Location: /login');
            exit;
        }

        Auth::login($user);
        $_SESSION['flash_success'] = "Хуш омадед, {$user['full_name']}!";

        if ($user['role'] === 'admin') {
            header('Location: /admin');
        } elseif ($user['role'] === 'cashier') {
            header('Location: /cashier');
        } else {
            header('Location: /');
        }
        exit;
    }

    public function registerForm(): void {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        View::render('auth.register', ['title' => 'Бақайдгирии мусофир']);
    }

    public function register(): void {
        Csrf::checkOrAbort();
        $validator = new Validator();
        $isValid = $validator->validate($_POST, [
            'username' => 'required|min:3',
            'password' => 'required|min:4',
            'full_name' => 'required|min:3',
            'email' => 'email'
        ]);

        if (!$isValid) {
            $_SESSION['flash_error'] = $validator->getFirstError();
            header('Location: /register');
            exit;
        }

        $userRepo = new UserRepository();
        if ($userRepo->findByUsername($_POST['username'])) {
            $_SESSION['flash_error'] = 'Ин логин аллакай ба қайд гирифта шудааст.';
            header('Location: /register');
            exit;
        }

        $userId = $userRepo->create([
            'username' => trim($_POST['username']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => 'passenger'
        ]);

        $user = $userRepo->findById($userId);
        Auth::login($user);

        $_SESSION['flash_success'] = 'Бақайдгирӣ бо муваффақият анҷом ёфт!';
        header('Location: /');
        exit;
    }

    public function logout(): void {
        Auth::logout();
        header('Location: /');
        exit;
    }
}

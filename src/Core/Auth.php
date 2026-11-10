<?php
namespace App\Core;

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionDir = __DIR__ . '/../../storage/sessions';
            if (is_dir($sessionDir) && is_writable($sessionDir)) {
                session_save_path($sessionDir);
            }
            @session_start();
        }
    }

    public static function login(array $user): void {
        self::init();
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'] ?? '',
            'phone' => $user['phone'] ?? '',
            'role' => $user['role']
        ];
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function check(): bool {
        self::init();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int {
        self::init();
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): string {
        self::init();
        return $_SESSION['user']['role'] ?? 'guest';
    }

    public static function hasRole(string ...$roles): bool {
        self::init();
        $currentRole = self::role();
        return in_array($currentRole, $roles, true);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Лутфан, аввал ба низом ворид шавед.';
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole(string ...$roles): void {
        self::requireLogin();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            echo "<h1>403 Дастрасӣ маҳдуд аст (Forbidden)</h1><p>Шумо барои дидани ин саҳифа ҳуқуқ надоред.</p><a href='/'>Ба саҳифаи асосӣ</a>";
            exit;
        }
    }
}

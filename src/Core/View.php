<?php
namespace App\Core;

class View {
    public static function render(string $template, array $data = [], string $layout = 'main'): void {
        Auth::init();
        extract($data);

        // Flash паёмҳо
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        $flashWarning = $_SESSION['flash_warning'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_warning']);

        $currentUser = Auth::user();

        // Гирифтани мундариҷаи саҳифа
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($viewPath)) {
            die("Шаблони '{$template}' дар '{$viewPath}' ёфт нашуд.");
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Агар layout лозим бошад
        if ($layout) {
            $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
                return;
            }
        }

        echo $content;
    }

    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

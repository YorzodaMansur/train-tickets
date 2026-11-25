<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public function __construct(private string $viewsPath)
    {
    }

    /**
     * Шаблони саҳифаро дар дохили макети умумӣ мекушояд.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        $file = $this->viewsPath . '/' . $template . '.php';

        if (!is_file($file)) {
            http_response_code(500);
            echo 'Хатои дохилӣ: шаблон ёфт нашуд.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        $content = (string) ob_get_clean();

        require $this->viewsPath . '/layout.php';
    }
}

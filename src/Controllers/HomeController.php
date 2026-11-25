<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class HomeController
{
    public function __construct(private View $view)
    {
    }

    public function index(): void
    {
        $stats = [
            ['label' => 'Самтҳои фаъол', 'value' => 12],
            ['label' => 'Истгоҳҳо', 'value' => 18],
            ['label' => 'Навъи вагонҳо', 'value' => 4],
        ];

        $this->view->render('home/index', [
            'pageTitle' => 'Саҳифаи асосӣ',
            'stats' => $stats,
        ]);
    }
}

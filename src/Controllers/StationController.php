<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class StationController
{
    public function __construct(private View $view)
    {
    }

    public function index(): void
    {
        // Номҳои калидҳо ба сутунҳои ҷадвали stations мутобиқанд.
        $stations = [
            ['id' => 1, 'code' => 'DUS', 'name_tj' => 'Душанбе', 'name_ru' => 'Душанбе', 'city' => 'Душанбе'],
            ['id' => 2, 'code' => 'KHU', 'name_tj' => 'Хуҷанд', 'name_ru' => 'Худжанд', 'city' => 'Хуҷанд'],
            ['id' => 3, 'code' => 'BOK', 'name_tj' => 'Бохтар', 'name_ru' => 'Бохтар', 'city' => 'Бохтар'],
        ];

        $this->view->render('stations/index', [
            'pageTitle' => 'Истгоҳҳо',
            'stations' => $stations,
        ]);
    }
}

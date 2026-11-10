<?php
namespace App\Controllers;

use App\Core\View;
use App\Repositories\StationRepository;
use App\Repositories\RouteRepository;
use App\Repositories\TripRepository;

class HomeController {
    public function index(): void {
        $stationRepo = new StationRepository();
        $routeRepo = new RouteRepository();
        $tripRepo = new TripRepository();

        $stations = $stationRepo->getAll();
        $routes = $routeRepo->getAllWithStations();
        
        // Намунаи қатнавҳои фаъол
        $activeTrips = $tripRepo->getAll();

        View::render('home.index', [
            'title' => 'Асосӣ — Роҳи оҳани Тоҷикистон',
            'stations' => $stations,
            'routes' => $routes,
            'activeTrips' => array_slice($activeTrips, 0, 5)
        ]);
    }
}

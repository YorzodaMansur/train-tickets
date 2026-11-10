<?php
namespace App\Controllers;

use App\Core\View;
use App\Repositories\StationRepository;
use App\Repositories\TripRepository;
use App\Repositories\CarRepository;

class TripController {
    public function search(): void {
        $originId = (int)($_GET['origin_id'] ?? 0);
        $destinationId = (int)($_GET['destination_id'] ?? 0);
        $date = !empty($_GET['date']) ? trim($_GET['date']) : null;

        $stationRepo = new StationRepository();
        $tripRepo = new TripRepository();

        $stations = $stationRepo->getAll();
        $trips = [];

        if ($originId > 0 && $destinationId > 0) {
            $trips = $tripRepo->searchTrips($originId, $destinationId, $date);
        }

        View::render('trips.search', [
            'title' => 'Натиҷаи ҷустуҷӯи қатораҳо',
            'stations' => $stations,
            'originId' => $originId,
            'destinationId' => $destinationId,
            'date' => $date,
            'trips' => $trips
        ]);
    }

    public function show(string $id): void {
        $tripId = (int)$id;
        $tripRepo = new TripRepository();
        $carRepo = new CarRepository();

        $trip = $tripRepo->getById($tripId);
        if (!$trip) {
            http_response_code(404);
            View::render('home.404', ['title' => 'Қатнав ёфт нашуд', 'uri' => "/trips/{$tripId}"]);
            return;
        }

        $cars = $carRepo->getByTripId($tripId);

        View::render('trips.show', [
            'title' => "Қатораи №{$trip['train_number']} — {$trip['route_name']}",
            'trip' => $trip,
            'cars' => $cars
        ]);
    }
}

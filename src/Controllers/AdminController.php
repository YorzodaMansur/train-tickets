<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repositories\StationRepository;
use App\Repositories\RouteRepository;
use App\Repositories\TripRepository;
use App\Repositories\CarRepository;
use App\Repositories\TicketRepository;

class AdminController {
    private StationRepository $stationRepo;
    private RouteRepository $routeRepo;
    private TripRepository $tripRepo;
    private CarRepository $carRepo;
    private TicketRepository $ticketRepo;

    public function __construct() {
        Auth::requireRole('admin');
        $this->stationRepo = new StationRepository();
        $this->routeRepo = new RouteRepository();
        $this->tripRepo = new TripRepository();
        $this->carRepo = new CarRepository();
        $this->ticketRepo = new TicketRepository();
    }

    public function index(): void {
        $kpi = $this->ticketRepo->getDashboardKpi();
        $salesReport = $this->ticketRepo->getSalesReport();
        $trips = $this->tripRepo->getAll();

        View::render('admin.index', [
            'title' => 'Панели идоракунии маъмур (Admin Dashboard)',
            'kpi' => $kpi,
            'salesReport' => $salesReport,
            'trips' => $trips
        ]);
    }

    public function stations(): void {
        $stations = $this->stationRepo->getAll();
        View::render('admin.stations', [
            'title' => 'Идоракунии истгоҳҳо ва вокзалҳо',
            'stations' => $stations
        ]);
    }

    public function addStation(): void {
        Csrf::checkOrAbort();
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $nameTj = trim($_POST['name_tj'] ?? '');
        $nameRu = trim($_POST['name_ru'] ?? '');
        $city = trim($_POST['city'] ?? '');

        if (empty($code) || empty($nameTj) || empty($city)) {
            $_SESSION['flash_error'] = 'Ҳамаи майдонҳоро пур кунед.';
            header('Location: /admin/stations');
            exit;
        }

        $this->stationRepo->create([
            'code' => $code,
            'name_tj' => $nameTj,
            'name_ru' => $nameRu ?: $nameTj,
            'city' => $city
        ]);

        $_SESSION['flash_success'] = "Истгоҳи «{$nameTj}» бомуваффақият илова шуд.";
        header('Location: /admin/stations');
        exit;
    }

    public function routes(): void {
        $routes = $this->routeRepo->getAllWithStations();
        $stations = $this->stationRepo->getAll();

        View::render('admin.routes', [
            'title' => 'Идоракунии хатсайрҳо',
            'routes' => $routes,
            'stations' => $stations
        ]);
    }

    public function addRoute(): void {
        Csrf::checkOrAbort();
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $origId = (int)($_POST['origin_station_id'] ?? 0);
        $destId = (int)($_POST['destination_station_id'] ?? 0);
        $dist = (float)($_POST['distance_km'] ?? 0);
        $mins = (int)($_POST['estimated_minutes'] ?? 0);

        if (empty($code) || empty($name) || $origId <= 0 || $destId <= 0) {
            $_SESSION['flash_error'] = 'Маълумоти хатсайрро пурра ворид кунед.';
            header('Location: /admin/routes');
            exit;
        }

        $this->routeRepo->create([
            'code' => $code,
            'name' => $name,
            'origin_station_id' => $origId,
            'destination_station_id' => $destId,
            'distance_km' => $dist,
            'estimated_minutes' => $mins
        ]);

        $_SESSION['flash_success'] = "Хатсайри «{$name}» илова гардид.";
        header('Location: /admin/routes');
        exit;
    }

    public function trips(): void {
        $trips = $this->tripRepo->getAll();
        $routes = $this->routeRepo->getAllWithStations();

        View::render('admin.trips', [
            'title' => 'Идоракунии қатораҳо ва қатнавҳо',
            'trips' => $trips,
            'routes' => $routes
        ]);
    }

    public function addTrip(): void {
        Csrf::checkOrAbort();
        $trainNumber = trim($_POST['train_number'] ?? '');
        $routeId = (int)($_POST['route_id'] ?? 0);
        $depTime = trim($_POST['departure_time'] ?? '');
        $arrTime = trim($_POST['arrival_time'] ?? '');
        $basePrice = (float)($_POST['base_price'] ?? 50.0);

        if (empty($trainNumber) || $routeId <= 0 || empty($depTime) || empty($arrTime)) {
            $_SESSION['flash_error'] = 'Ҳамаи майдонҳои қатнавро пур кунед.';
            header('Location: /admin/trips');
            exit;
        }

        $tripId = $this->tripRepo->create([
            'train_number' => $trainNumber,
            'route_id' => $routeId,
            'departure_time' => $depTime,
            'arrival_time' => $arrTime,
            'status' => 'scheduled',
            'base_price' => $basePrice
        ]);

        // Худкор илова кардани 1 вагони купе ва 1 вагони плацкарт бо ҷойҳо
        $this->carRepo->create([
            'trip_id' => $tripId,
            'car_number' => 1,
            'car_type' => 'coupe',
            'total_seats' => 36,
            'price_modifier' => 1.50
        ]);

        $this->carRepo->create([
            'trip_id' => $tripId,
            'car_number' => 2,
            'car_type' => 'platzkart',
            'total_seats' => 54,
            'price_modifier' => 1.00
        ]);

        $_SESSION['flash_success'] = "Қатнави қатораи №{$trainNumber} бо вагонҳо ва ҷойҳо бомуваффақият сабт шуд.";
        header('Location: /admin/trips');
        exit;
    }

    public function reports(): void {
        $salesReport = $this->ticketRepo->getSalesReport();
        $recentTickets = $this->ticketRepo->getAll(50);
        $kpi = $this->ticketRepo->getDashboardKpi();

        View::render('admin.reports', [
            'title' => 'Ҳисоботи фурӯш ва гардиши молиявӣ',
            'salesReport' => $salesReport,
            'recentTickets' => $recentTickets,
            'kpi' => $kpi
        ]);
    }
}

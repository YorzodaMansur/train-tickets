<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Validator.php';

require_once __DIR__ . '/../src/Repositories/TripRepository.php';
require_once __DIR__ . '/../src/Repositories/TrainRepository.php';
require_once __DIR__ . '/../src/Repositories/RouteRepository.php';

require_once __DIR__ . '/../src/Controllers/TripController.php';

$configPath = __DIR__ . '/../config/config.php';
$examplePath = __DIR__ . '/../config/config.example.php';

$config = require file_exists($configPath)
    ? $configPath
    : $examplePath;

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $db = Database::connect($config);

    $tripRepository = new TripRepository($db);
    $trainRepository = new TrainRepository($db);
    $routeRepository = new RouteRepository($db);

    $controller = new TripController(
        $tripRepository,
        $trainRepository,
        $routeRepository
    );

    $data = $controller->handle();

    require __DIR__ . '/../views/trips/page.php';
} catch (Throwable $exception) {
    $status = http_response_code();

    if (!$status || $status < 400) {
        $status = 500;
    }

    http_response_code($status);

    $debug = $config['app']['debug'] ?? false;

    require __DIR__ . '/../views/errors/500.php';
}

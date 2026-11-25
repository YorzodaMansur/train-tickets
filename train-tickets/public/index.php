<?php
declare(strict_types=1);

/*
 * Front Controller: ҳамаи дархостҳо аввал ба ҳамин файл меоянд.
 * Дар ин файл HTML ва массивҳои додаҳои саҳифа ҷойгир карда намешаванд.
 */

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
}

// Боркунандаи эҳтиётӣ, агар Composer autoload дастрас набошад.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$config = require BASE_PATH . '/config/config.php';

date_default_timezone_set('Asia/Dushanbe');

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = ($basePath === '.' || $basePath === '/') ? '' : $basePath;

    return $basePath . '/' . ltrim($path, '/');
}

$view = new App\Core\View(BASE_PATH . '/views');
$router = new App\Core\Router();

$homeController = new App\Controllers\HomeController($view);
$tripController = new App\Controllers\TripController($view);
$stationController = new App\Controllers\StationController($view);
$authController = new App\Controllers\AuthController($view);

// Масирҳои GET — мувофиқи сохтори лоиҳаи фурӯши чиптаҳо.
$router->get('/', [$homeController, 'index']);
$router->get('/trips', [$tripController, 'index']);
$router->get('/trips/{id}', [$tripController, 'show']);
$router->get('/stations', [$stationController, 'index']);
$router->get('/login', [$authController, 'login']);
$router->get('/register', [$authController, 'register']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

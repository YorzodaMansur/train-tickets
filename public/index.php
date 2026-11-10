<?php
/**
 * Front Controller: railway-tickets
 */

declare(strict_types=1);

// Гузориши хатогиҳо барои таҳия ва омӯзиш
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Танзими ҷузвдони сессияҳо барои пешгирии хатогии дастрасӣ
$sessionDir = __DIR__ . '/../storage/sessions';
if (!is_dir($sessionDir)) {
    @mkdir($sessionDir, 0777, true);
}
if (is_dir($sessionDir) && is_writable($sessionDir)) {
    session_save_path($sessionDir);
}

// Оғози сессия
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Автоборкунии PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\Database;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\TripController;
use App\Controllers\BookingController;
use App\Controllers\CashierController;
use App\Controllers\AdminController;

// Инициализатсияи МБДД (MySQL ё SQLite fallback)
try {
    Database::getInstance();
} catch (\Throwable $e) {
    die("Хатогӣ ҳангоми пайвастшавӣ ба пойгоҳи додаҳо: " . $e->getMessage());
}

$router = new Router();

// ==========================================
// МАРШРУТҲОИ САҲИФАИ АСОСӢ ВА АУТЕНТИФИКАТСИЯ
// ==========================================
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// ==========================================
// МАРШРУТҲОИ ҶУСТУҶӮ ВА ҚАТНАВҲО
// ==========================================
$router->get('/search', [TripController::class, 'search']);
$router->get('/trips/{id}', [TripController::class, 'show']);

// ==========================================
// МАРШРУТҲОИ БАНД, ИНТИХОБИ ҶОЙ ВА ПАРДОХТ
// ==========================================
$router->get('/booking/seat-map/{trip_id}/{car_id}', [BookingController::class, 'seatMap']);
$router->get('/booking/seat-map/{trip_id}', [BookingController::class, 'seatMap']);
$router->post('/booking/hold', [BookingController::class, 'hold']);
$router->post('/booking/cancel-hold', [BookingController::class, 'cancelHold']);
$router->get('/booking/checkout/{seat_id}', [BookingController::class, 'checkout']);
$router->post('/booking/pay', [BookingController::class, 'processPayment']);
$router->get('/tickets/view/{id}', [BookingController::class, 'viewTicket']);
$router->get('/my-tickets', [BookingController::class, 'myTickets']);
$router->post('/tickets/refund/{id}', [BookingController::class, 'refund']);

// ==========================================
// МАРШРУТҲОИ КАССИР (POS TERMINAL)
// ==========================================
$router->get('/cashier', [CashierController::class, 'index']);
$router->get('/cashier/lookup', [CashierController::class, 'lookup']);
$router->post('/cashier/fast-sale', [CashierController::class, 'fastSale']);
$router->post('/cashier/refund', [CashierController::class, 'processRefund']);

// ==========================================
// МАРШРУТҲОИ МАЪМУРИЯТ (ADMIN DASHBOARD)
// ==========================================
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/stations', [AdminController::class, 'stations']);
$router->post('/admin/stations/add', [AdminController::class, 'addStation']);
$router->get('/admin/routes', [AdminController::class, 'routes']);
$router->post('/admin/routes/add', [AdminController::class, 'addRoute']);
$router->get('/admin/trips', [AdminController::class, 'trips']);
$router->post('/admin/trips/add', [AdminController::class, 'addTrip']);
$router->get('/admin/reports', [AdminController::class, 'reports']);

// Иҷрои диспетчер (агар дар веб-сервер иҷро шуда истода бошад)
if (php_sapi_name() !== 'cli' || isset($_SERVER['REQUEST_METHOD'])) {
    $router->dispatch();
}

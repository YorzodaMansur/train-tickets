<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;
    private string $driver;

    private function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $dbConfig = $config['db'];

        $connected = false;

        // 1. Кӯшиши пайвастшавӣ ба MySQL (XAMPP / MariaDB)
        if ($dbConfig['driver'] === 'mysql') {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                    $dbConfig['host'],
                    $dbConfig['port'],
                    $dbConfig['database'],
                    $dbConfig['charset']
                );
                $this->pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                $this->driver = 'mysql';
                $connected = true;
            } catch (PDOException $e) {
                // Агар MySQL пайваст нашавад, ба SQLite мегузарем
                $connected = false;
            }
        }

        // 2. Fallback ба SQLite барои санҷиши фаврии мустақил
        if (!$connected) {
            $sqlitePath = $dbConfig['sqlite_path'];
            $dir = dirname($sqlitePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $isNew = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;
            $this->pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $this->driver = 'sqlite';

            // Ба қайд гирифтани функсияҳои MySQL барои мутобиқати 100% дар SQLite
            $this->registerSqliteCompatibilityFunctions();

            if ($isNew) {
                $this->initSqliteDatabase();
            }
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    public function getDriver(): string {
        return $this->driver;
    }

    public function query(string $sql, array $params = []): \PDOStatement {
        // Барои мутобиқати DATE_ADD ва INTERVAL дар SQLite
        if ($this->driver === 'sqlite') {
            $sql = $this->adaptQueryForSqlite($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function lastInsertId(): int {
        return (int)$this->pdo->lastInsertId();
    }

    private function registerSqliteCompatibilityFunctions(): void {
        $this->pdo->sqliteCreateFunction('NOW', function () {
            return date('Y-m-d H:i:s');
        });
        $this->pdo->sqliteCreateFunction('CURRENT_DATE', function () {
            return date('Y-m-d');
        });
        $this->pdo->sqliteCreateFunction('DATE', function ($dateTime) {
            return substr($dateTime, 0, 10);
        });
    }

    private function adaptQueryForSqlite(string $sql): string {
        // Мутобиқсозии NOW() + INTERVAL 15 MINUTE
        $sql = preg_replace('/NOW\(\)\s*\+\s*INTERVAL\s+(\d+)\s+MINUTE/i', "datetime('now', '+\$1 minutes')", $sql);
        $sql = preg_replace('/DATE_ADD\(NOW\(\),\s*INTERVAL\s+(\d+)\s+MINUTE\)/i', "datetime('now', '+\$1 minutes')", $sql);
        $sql = preg_replace('/DATE_ADD\(CURRENT_DATE\(\),\s*INTERVAL\s+(\d+)\s+DAY\)\s*\+\s*INTERVAL\s+(\d+)\s+HOUR/i', "datetime('now', '+\$1 days', '+\$2 hours')", $sql);
        $sql = preg_replace('/CURRENT_DATE\(\)\s*\+\s*INTERVAL\s+(\d+)\s+DAY/i', "date('now', '+\$1 days')", $sql);
        return $sql;
    }

    private function initSqliteDatabase(): void {
        // Сохтани ҷадвалҳо барои SQLite
        $schema = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            full_name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            role TEXT NOT NULL DEFAULT 'passenger',
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS stations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name_tj TEXT NOT NULL,
            name_ru TEXT NOT NULL,
            city TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS routes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            origin_station_id INTEGER NOT NULL REFERENCES stations(id),
            destination_station_id INTEGER NOT NULL REFERENCES stations(id),
            distance_km REAL NOT NULL DEFAULT 0.00,
            estimated_minutes INTEGER NOT NULL DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS trips (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            train_number TEXT NOT NULL,
            route_id INTEGER NOT NULL REFERENCES routes(id),
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            status TEXT NOT NULL DEFAULT 'scheduled',
            base_price REAL NOT NULL DEFAULT 50.00
        );

        CREATE TABLE IF NOT EXISTS cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL REFERENCES trips(id),
            car_number INTEGER NOT NULL,
            car_type TEXT NOT NULL,
            total_seats INTEGER NOT NULL DEFAULT 36,
            price_modifier REAL NOT NULL DEFAULT 1.00
        );

        CREATE TABLE IF NOT EXISTS seats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            car_id INTEGER NOT NULL REFERENCES cars(id),
            seat_number INTEGER NOT NULL,
            seat_type TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'available',
            reserved_until DATETIME,
            reserved_by_user_id INTEGER REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_number TEXT NOT NULL UNIQUE,
            trip_id INTEGER NOT NULL REFERENCES trips(id),
            car_id INTEGER NOT NULL REFERENCES cars(id),
            seat_id INTEGER NOT NULL REFERENCES seats(id),
            user_id INTEGER REFERENCES users(id),
            passenger_name TEXT NOT NULL,
            passenger_doc TEXT NOT NULL,
            price REAL NOT NULL,
            status TEXT NOT NULL DEFAULT 'paid',
            purchase_channel TEXT NOT NULL DEFAULT 'online',
            paid_at DATETIME,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            refunded_at DATETIME,
            refunded_by_user_id INTEGER REFERENCES users(id)
        );
        ";

        $this->pdo->exec($schema);
        $this->seedInitialData();
    }

    private function seedInitialData(): void {
        // Сабти корбарон
        $passHashAdmin = password_hash('admin123', PASSWORD_BCRYPT);
        $passHashCashier = password_hash('cashier123', PASSWORD_BCRYPT);
        $passHashPass = password_hash('pass123', PASSWORD_BCRYPT);

        $this->query("INSERT OR IGNORE INTO users (id, username, password_hash, full_name, email, phone, role) VALUES 
            (1, 'admin', ?, 'Раҳимов Фарҳод (Маъмур)', 'admin@railway.tj', '+992927001122', 'admin'),
            (2, 'cashier1', ?, 'Саидова Нигина (Кассири Вокзал)', 'cashier@railway.tj', '+992928883344', 'cashier'),
            (3, 'passenger1', ?, 'Қосимов Ҷамшед', 'passenger@example.tj', '+992935556677', 'passenger')",
            [$passHashAdmin, $passHashCashier, $passHashPass]
        );

        // Сабти истгоҳҳо
        $this->query("INSERT OR IGNORE INTO stations (id, code, name_tj, name_ru, city) VALUES 
            (1, 'KHJ', 'Хуҷанд', 'Худжанд', 'Хуҷанд'),
            (2, 'DSH', 'Душанбе-1', 'Душанбе-1', 'Душанбе'),
            (3, 'BOK', 'Бохтар', 'Бохтар', 'Бохтар'),
            (4, 'KLB', 'Кӯлоб', 'Куляб', 'Кӯлоб'),
            (5, 'KNB', 'Конибодом', 'Канибадам', 'Конибодом')");

        // Хатсайрҳо
        $this->query("INSERT OR IGNORE INTO routes (id, code, name, origin_station_id, destination_station_id, distance_km, estimated_minutes) VALUES 
            (1, 'RT-01', 'Хуҷанд — Душанбе-1', 1, 2, 340.00, 360),
            (2, 'RT-02', 'Душанбе-1 — Хуҷанд', 2, 1, 340.00, 360),
            (3, 'RT-03', 'Душанбе-1 — Кӯлоб', 2, 4, 202.00, 240),
            (4, 'RT-04', 'Душанбе-1 — Бохтар', 2, 3, 115.00, 120)");

        // Қатнавҳо
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $dayAfter = date('Y-m-d', strtotime('+2 days'));
        $this->query("INSERT OR IGNORE INTO trips (id, train_number, route_id, departure_time, arrival_time, status, base_price) VALUES 
            (1, '027-ХД', 1, ?, ?, 'scheduled', 80.00),
            (2, '028-ДХ', 2, ?, ?, 'scheduled', 80.00),
            (3, '031-ДК', 3, ?, ?, 'scheduled', 50.00)",
            [
                "$tomorrow 08:00:00", "$tomorrow 14:00:00",
                "$tomorrow 15:30:00", "$tomorrow 21:30:00",
                "$dayAfter 09:00:00", "$dayAfter 13:00:00"
            ]
        );

        // Вагонҳо барои Қатнави 1 (Хуҷанд - Душанбе)
        $this->query("INSERT OR IGNORE INTO cars (id, trip_id, car_number, car_type, total_seats, price_modifier) VALUES 
            (1, 1, 1, 'coupe', 36, 1.50),
            (2, 1, 2, 'platzkart', 54, 1.00),
            (3, 1, 3, 'platzkart', 54, 1.00),
            (4, 2, 1, 'coupe', 36, 1.50),
            (5, 2, 2, 'platzkart', 54, 1.00)");

        // Ҷойҳои Вагони 1 (Купе, 36 ҷой)
        for ($s = 1; $s <= 36; $s++) {
            $type = ($s % 2 === 1) ? 'lower' : 'upper';
            $status = 'available';
            $until = null;
            $userId = null;
            if ($s === 1 || $s === 2) {
                $status = 'sold';
            } elseif ($s === 5) {
                // Намунаи банди 15-дақиқагӣ
                $status = 'reserved';
                $until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $userId = 3;
            }
            $this->query("INSERT OR IGNORE INTO seats (car_id, seat_number, seat_type, status, reserved_until, reserved_by_user_id) VALUES (?, ?, ?, ?, ?, ?)",
                [1, $s, $type, $status, $until, $userId]
            );
        }

        // Ҷойҳои Вагони 2 (Плацкарт, 54 ҷой)
        for ($s = 1; $s <= 54; $s++) {
            if ($s <= 36) {
                $type = ($s % 2 === 1) ? 'lower' : 'upper';
            } else {
                $type = ($s % 2 === 1) ? 'side_lower' : 'side_upper';
            }
            $status = ($s === 10) ? 'sold' : 'available';
            $this->query("INSERT OR IGNORE INTO seats (car_id, seat_number, seat_type, status, reserved_until, reserved_by_user_id) VALUES (?, ?, ?, ?, NULL, NULL)",
                [2, $s, $type, $status]
            );
        }

        // Намунаи чипта
        $this->query("INSERT OR IGNORE INTO tickets (id, ticket_number, trip_id, car_id, seat_id, user_id, passenger_name, passenger_doc, price, status, purchase_channel, paid_at) VALUES 
            (1, 'TKT-2026-0001', 1, 1, 1, 3, 'Қосимов Ҷамшед', 'А2458991', 120.00, 'paid', 'online', ?),
            (2, 'TKT-2026-0002', 1, 1, 2, NULL, 'Шарипов Алишер', 'А9823101', 120.00, 'paid', 'cashier', ?)",
            [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
        );
    }
}

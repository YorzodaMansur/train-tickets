-- ============================================================
-- Пойгоҳи додаҳо: railway_tickets (Фурӯши чиптаҳои қатора)
-- Сохтори ҷадвалҳо барои MySQL / MariaDB ва SQLite
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'passenger', -- 'passenger', 'cashier', 'admin'
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name_tj VARCHAR(100) NOT NULL,
    name_ru VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    origin_station_id INT NOT NULL,
    destination_station_id INT NOT NULL,
    distance_km DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    estimated_minutes INT NOT NULL DEFAULT 0,
    FOREIGN KEY (origin_station_id) REFERENCES stations(id) ON DELETE RESTRICT,
    FOREIGN KEY (destination_station_id) REFERENCES stations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    train_number VARCHAR(20) NOT NULL,
    route_id INT NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled', -- 'scheduled', 'active', 'completed', 'cancelled'
    base_price DECIMAL(10,2) NOT NULL DEFAULT 50.00,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    car_number INT NOT NULL,
    car_type VARCHAR(20) NOT NULL, -- 'coupe', 'platzkart', 'lux'
    total_seats INT NOT NULL DEFAULT 36,
    price_modifier DECIMAL(4,2) NOT NULL DEFAULT 1.00,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    seat_number INT NOT NULL,
    seat_type VARCHAR(20) NOT NULL, -- 'lower', 'upper', 'side_lower', 'side_upper'
    status VARCHAR(20) NOT NULL DEFAULT 'available', -- 'available', 'reserved', 'sold'
    reserved_until DATETIME NULL,
    reserved_by_user_id INT NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (reserved_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(30) NOT NULL UNIQUE,
    trip_id INT NOT NULL,
    car_id INT NOT NULL,
    seat_id INT NOT NULL,
    user_id INT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    passenger_doc VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'paid', -- 'booked', 'paid', 'refunded', 'cancelled'
    purchase_channel VARCHAR(20) NOT NULL DEFAULT 'online', -- 'online', 'cashier'
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    refunded_at DATETIME NULL,
    refunded_by_user_id INT NULL,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE RESTRICT,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE RESTRICT,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (refunded_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Индексҳо барои суръатбахшии ҷустуҷӯ
CREATE INDEX idx_trips_route_date ON trips(route_id, departure_time);
CREATE INDEX idx_seats_car_status ON seats(car_id, status);
CREATE INDEX idx_tickets_user ON tickets(user_id);
CREATE INDEX idx_tickets_number ON tickets(ticket_number);

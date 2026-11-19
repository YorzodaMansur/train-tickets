DROP DATABASE IF EXISTS railway_tickets;
CREATE DATABASE railway_tickets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE railway_tickets;
SET NAMES utf8mb4;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NULL UNIQUE,
 phone VARCHAR(30) NULL,
 role ENUM('passenger','dispatcher','admin') NOT NULL DEFAULT 'passenger',
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_users_role_active(role,is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stations (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(10) NOT NULL UNIQUE,
 name_tj VARCHAR(100) NOT NULL,
 name_ru VARCHAR(100) NULL,
 city VARCHAR(100) NOT NULL,
 KEY idx_stations_city(city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trains (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 train_number VARCHAR(20) NOT NULL UNIQUE,
 train_name VARCHAR(100) NOT NULL,
 train_type VARCHAR(50) NOT NULL,
 status ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE routes (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(20) NOT NULL UNIQUE,
 name VARCHAR(150) NOT NULL,
 origin_station_id INT UNSIGNED NOT NULL,
 destination_station_id INT UNSIGNED NOT NULL,
 distance_km DECIMAL(8,2) NULL,
 estimated_minutes INT UNSIGNED NULL,
 CONSTRAINT fk_routes_origin FOREIGN KEY(origin_station_id) REFERENCES stations(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_routes_destination FOREIGN KEY(destination_station_id) REFERENCES stations(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT chk_routes_stations CHECK(origin_station_id <> destination_station_id),
 CONSTRAINT chk_routes_distance CHECK(distance_km IS NULL OR distance_km > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE route_stations (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 route_id INT UNSIGNED NOT NULL,
 station_id INT UNSIGNED NOT NULL,
 stop_order INT UNSIGNED NOT NULL,
 arrival_offset_minutes INT UNSIGNED NULL,
 departure_offset_minutes INT UNSIGNED NULL,
 UNIQUE KEY uq_route_station(route_id,station_id),
 UNIQUE KEY uq_route_stop(route_id,stop_order),
 CONSTRAINT fk_rs_route FOREIGN KEY(route_id) REFERENCES routes(id) ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_rs_station FOREIGN KEY(station_id) REFERENCES stations(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trips (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 train_id INT UNSIGNED NOT NULL,
 route_id INT UNSIGNED NOT NULL,
 departure_time DATETIME NOT NULL,
 arrival_time DATETIME NOT NULL,
 status ENUM('scheduled','boarding','completed','cancelled') NOT NULL DEFAULT 'scheduled',
 base_price DECIMAL(10,2) NOT NULL,
 KEY idx_trips_route_departure(route_id,departure_time),
 KEY idx_trips_status_departure(status,departure_time),
 CONSTRAINT fk_trips_train FOREIGN KEY(train_id) REFERENCES trains(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_trips_route FOREIGN KEY(route_id) REFERENCES routes(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT chk_trip_time CHECK(arrival_time > departure_time),
 CONSTRAINT chk_trip_price CHECK(base_price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cars (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 train_id INT UNSIGNED NOT NULL,
 car_number INT UNSIGNED NOT NULL,
 car_type ENUM('standard','coupe','luxury','sitting') NOT NULL,
 total_seats INT UNSIGNED NOT NULL,
 price_modifier DECIMAL(4,2) NOT NULL DEFAULT 1.00,
 UNIQUE KEY uq_car_number(train_id,car_number),
 CONSTRAINT fk_cars_train FOREIGN KEY(train_id) REFERENCES trains(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT chk_car_seats CHECK(total_seats > 0),
 CONSTRAINT chk_car_modifier CHECK(price_modifier > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seats (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 car_id INT UNSIGNED NOT NULL,
 seat_number INT UNSIGNED NOT NULL,
 seat_type ENUM('lower','upper','standard','window','aisle') NOT NULL DEFAULT 'standard',
 status ENUM('available','unavailable') NOT NULL DEFAULT 'available',
 UNIQUE KEY uq_seat_number(car_id,seat_number),
 KEY idx_seats_status(status),
 CONSTRAINT fk_seats_car FOREIGN KEY(car_id) REFERENCES cars(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tickets (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 ticket_number VARCHAR(30) NOT NULL UNIQUE,
 trip_id INT UNSIGNED NOT NULL,
 seat_id INT UNSIGNED NOT NULL,
 user_id INT UNSIGNED NULL,
 passenger_name VARCHAR(100) NOT NULL,
 passenger_doc VARCHAR(50) NULL,
 price DECIMAL(10,2) NOT NULL,
 status ENUM('reserved','paid','cancelled','refunded') NOT NULL DEFAULT 'reserved',
 purchase_channel ENUM('website','ticket_office','mobile') NOT NULL DEFAULT 'website',
 paid_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 refunded_at DATETIME NULL,
 refunded_by_user_id INT UNSIGNED NULL,
 UNIQUE KEY uq_ticket_trip_seat(trip_id,seat_id),
 KEY idx_tickets_status_created(status,created_at),
 CONSTRAINT fk_ticket_trip FOREIGN KEY(trip_id) REFERENCES trips(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_ticket_seat FOREIGN KEY(seat_id) REFERENCES seats(id) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_ticket_user FOREIGN KEY(user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
 CONSTRAINT fk_ticket_refunder FOREIGN KEY(refunded_by_user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
 CONSTRAINT chk_ticket_price CHECK(price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

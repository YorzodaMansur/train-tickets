<?php
namespace App\Repositories;

use App\Core\Database;

class CarRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByTripId(int $tripId): array {
        $sql = "SELECT c.*, 
                       COUNT(s.id) AS total_seats_count,
                       COUNT(CASE WHEN s.status = 'available' THEN 1 END) AS available_seats
                FROM cars c
                LEFT JOIN seats s ON s.car_id = c.id
                WHERE c.trip_id = ?
                GROUP BY c.id
                ORDER BY c.car_number ASC";
        return $this->db->fetchAll($sql, [$tripId]);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT c.*, t.train_number, t.base_price, t.departure_time, t.arrival_time,
                       r.name AS route_name
                FROM cars c
                JOIN trips t ON c.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                WHERE c.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create(array $data): int {
        $this->db->query(
            "INSERT INTO cars (trip_id, car_number, car_type, total_seats, price_modifier) 
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['trip_id'],
                $data['car_number'],
                $data['car_type'],
                $data['total_seats'] ?? 36,
                $data['price_modifier'] ?? 1.00
            ]
        );
        $carId = $this->db->lastInsertId();

        // Худкор сохтани ҷойҳо барои ин вагон
        $totalSeats = (int)($data['total_seats'] ?? 36);
        $carType = $data['car_type'];
        $seatRepo = new SeatRepository();
        $seatRepo->generateSeatsForCar($carId, $carType, $totalSeats);

        return $carId;
    }
}

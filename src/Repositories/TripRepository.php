<?php
namespace App\Repositories;

use App\Core\Database;

class TripRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function searchTrips(int $originId, int $destinationId, ?string $date = null): array {
        // Озод кардани ҷойҳои мӯҳлаташон гузашта пеш аз ҷустуҷӯ
        (new SeatRepository())->releaseExpiredLocks();

        $params = [$originId, $destinationId];
        $dateFilter = "";
        if (!empty($date)) {
            $dateFilter = " AND DATE(t.departure_time) = ? ";
            $params[] = $date;
        }

        $sql = "SELECT 
                    t.id AS trip_id,
                    t.train_number,
                    t.departure_time,
                    t.arrival_time,
                    t.status,
                    t.base_price,
                    r.name AS route_name,
                    r.distance_km,
                    r.estimated_minutes,
                    s_orig.name_tj AS origin_name,
                    s_orig.city AS origin_city,
                    s_dest.name_tj AS dest_name,
                    s_dest.city AS dest_city,
                    COUNT(DISTINCT c.id) AS cars_count,
                    COUNT(CASE WHEN s.status = 'available' THEN 1 END) AS available_seats,
                    COUNT(s.id) AS total_seats
                FROM trips t
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                LEFT JOIN cars c ON c.trip_id = t.id
                LEFT JOIN seats s ON s.car_id = c.id
                WHERE r.origin_station_id = ? 
                  AND r.destination_station_id = ?
                  {$dateFilter}
                GROUP BY t.id, t.train_number, t.departure_time, t.arrival_time, t.status, t.base_price,
                         r.name, r.distance_km, r.estimated_minutes, s_orig.name_tj, s_orig.city, s_dest.name_tj, s_dest.city
                ORDER BY t.departure_time ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getAll(): array {
        $sql = "SELECT t.*, r.name AS route_name,
                       s_orig.name_tj AS origin_name, s_dest.name_tj AS dest_name,
                       COUNT(DISTINCT c.id) AS cars_count
                FROM trips t
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                LEFT JOIN cars c ON c.trip_id = t.id
                GROUP BY t.id
                ORDER BY t.departure_time DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT t.*, r.name AS route_name, r.distance_km,
                       s_orig.name_tj AS origin_name, s_dest.name_tj AS dest_name
                FROM trips t
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                WHERE t.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create(array $data): int {
        $this->db->query(
            "INSERT INTO trips (train_number, route_id, departure_time, arrival_time, status, base_price) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['train_number'],
                $data['route_id'],
                $data['departure_time'],
                $data['arrival_time'],
                $data['status'] ?? 'scheduled',
                $data['base_price']
            ]
        );
        return $this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $this->db->query("DELETE FROM trips WHERE id = ?", [$id]);
        return true;
    }
}

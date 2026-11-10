<?php
namespace App\Repositories;

use App\Core\Database;

class RouteRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllWithStations(): array {
        $sql = "SELECT r.*, 
                       s_orig.name_tj AS origin_name, s_orig.city AS origin_city,
                       s_dest.name_tj AS dest_name, s_dest.city AS dest_city
                FROM routes r
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                ORDER BY r.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT r.*, 
                       s_orig.name_tj AS origin_name, s_dest.name_tj AS dest_name
                FROM routes r
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                WHERE r.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create(array $data): int {
        $this->db->query(
            "INSERT INTO routes (code, name, origin_station_id, destination_station_id, distance_km, estimated_minutes) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['code'],
                $data['name'],
                $data['origin_station_id'],
                $data['destination_station_id'],
                $data['distance_km'] ?? 0,
                $data['estimated_minutes'] ?? 0
            ]
        );
        return $this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $this->db->query("DELETE FROM routes WHERE id = ?", [$id]);
        return true;
    }
}

<?php
namespace App\Repositories;

use App\Core\Database;

class StationRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM stations ORDER BY city ASC, name_tj ASC");
    }

    public function getById(int $id): ?array {
        return $this->db->fetchOne("SELECT * FROM stations WHERE id = ?", [$id]);
    }

    public function create(array $data): int {
        $this->db->query(
            "INSERT INTO stations (code, name_tj, name_ru, city) VALUES (?, ?, ?, ?)",
            [$data['code'], $data['name_tj'], $data['name_ru'], $data['city']]
        );
        return $this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $this->db->query("DELETE FROM stations WHERE id = ?", [$id]);
        return true;
    }
}

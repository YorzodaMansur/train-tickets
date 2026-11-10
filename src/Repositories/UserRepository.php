<?php
namespace App\Repositories;

use App\Core\Database;

class UserRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUsername(string $username): ?array {
        return $this->db->fetchOne("SELECT * FROM users WHERE username = ?", [trim($username)]);
    }

    public function findById(int $id): ?array {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function create(array $data): int {
        $this->db->query(
            "INSERT INTO users (username, password_hash, full_name, email, phone, role) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['username'],
                $data['password_hash'],
                $data['full_name'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['role'] ?? 'passenger'
            ]
        );
        return $this->db->lastInsertId();
    }
}

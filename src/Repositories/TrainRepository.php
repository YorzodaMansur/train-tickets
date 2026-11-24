<?php

declare(strict_types=1);

class TrainRepository
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        $sql = "
            SELECT id, train_number, train_name, status
            FROM trains
            WHERE status = 'active'
            ORDER BY train_number
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM trains WHERE id = :id LIMIT 1"
        );

        $stmt->execute(['id' => $id]);

        return (bool)$stmt->fetchColumn();
    }
}

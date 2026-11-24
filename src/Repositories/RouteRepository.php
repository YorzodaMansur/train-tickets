<?php

declare(strict_types=1);

class RouteRepository
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        $sql = "
            SELECT
                r.id,
                r.code,
                r.name,
                s1.name_tj AS origin_name,
                s2.name_tj AS destination_name
            FROM routes r
            INNER JOIN stations s1
                ON s1.id = r.origin_station_id
            INNER JOIN stations s2
                ON s2.id = r.destination_station_id
            ORDER BY r.name
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM routes WHERE id = :id LIMIT 1"
        );

        $stmt->execute(['id' => $id]);

        return (bool)$stmt->fetchColumn();
    }
}

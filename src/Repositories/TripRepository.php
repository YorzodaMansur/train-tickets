<?php

declare(strict_types=1);

class TripRepository
{
    public function __construct(private PDO $db) {}

    public function paginate(
        string $search,
        string $status,
        string $dateFrom,
        string $dateTo,
        int $page,
        int $perPage
    ): array {
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(
                t.train_number LIKE :search ESCAPE '!'
                OR t.train_name LIKE :search ESCAPE '!'
                OR r.name LIKE :search ESCAPE '!'
                OR s1.name_tj LIKE :search ESCAPE '!'
                OR s2.name_tj LIKE :search ESCAPE '!'
            )";

            $escaped = str_replace(
                ['!', '%', '_'],
                ['!!', '!%', '!_'],
                $search
            );

            $params['search'] = '%' . $escaped . '%';
        }

        if ($status !== '') {
            $where[] = 'tr.status = :status';
            $params['status'] = $status;
        }

        if ($dateFrom !== '') {
            $where[] = 'DATE(tr.departure_time) >= :date_from';
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo !== '') {
            $where[] = 'DATE(tr.departure_time) <= :date_to';
            $params['date_to'] = $dateTo;
        }

        $whereSql = $where
            ? 'WHERE ' . implode(' AND ', $where)
            : '';

        $countSql = "
            SELECT COUNT(*)
            FROM trips tr
            INNER JOIN trains t ON t.id = tr.train_id
            INNER JOIN routes r ON r.id = tr.route_id
            INNER JOIN stations s1 ON s1.id = r.origin_station_id
            INNER JOIN stations s2 ON s2.id = r.destination_station_id
            $whereSql
        ";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                tr.id,
                tr.train_id,
                tr.route_id,
                tr.departure_time,
                tr.arrival_time,
                tr.status,
                tr.base_price,
                t.train_number,
                t.train_name,
                r.name AS route_name,
                s1.name_tj AS origin_name,
                s2.name_tj AS destination_name
            FROM trips tr
            INNER JOIN trains t ON t.id = tr.train_id
            INNER JOIN routes r ON r.id = tr.route_id
            INNER JOIN stations s1 ON s1.id = r.origin_station_id
            INNER JOIN stations s2 ON s2.id = r.destination_station_id
            $whereSql
            ORDER BY tr.departure_time DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM trips WHERE id = :id LIMIT 1"
        );

        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO trips (
                train_id,
                route_id,
                departure_time,
                arrival_time,
                status,
                base_price
            ) VALUES (
                :train_id,
                :route_id,
                :departure_time,
                :arrival_time,
                :status,
                :base_price
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'train_id' => (int)$data['train_id'],
            'route_id' => (int)$data['route_id'],
            'departure_time' => str_replace(
                'T',
                ' ',
                $data['departure_time']
            ) . ':00',
            'arrival_time' => str_replace(
                'T',
                ' ',
                $data['arrival_time']
            ) . ':00',
            'status' => $data['status'],
            'base_price' => $data['base_price'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE trips SET
                train_id = :train_id,
                route_id = :route_id,
                departure_time = :departure_time,
                arrival_time = :arrival_time,
                status = :status,
                base_price = :base_price
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'train_id' => (int)$data['train_id'],
            'route_id' => (int)$data['route_id'],
            'departure_time' => str_replace(
                'T',
                ' ',
                $data['departure_time']
            ) . ':00',
            'arrival_time' => str_replace(
                'T',
                ' ',
                $data['arrival_time']
            ) . ':00',
            'status' => $data['status'],
            'base_price' => $data['base_price'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM trips WHERE id = :id"
        );

        return $stmt->execute(['id' => $id]);
    }

    public function existsDuplicateSchedule(
        int $trainId,
        string $departureTime,
        ?int $ignoreId = null
    ): bool {
        $sql = "
            SELECT 1
            FROM trips
            WHERE train_id = :train_id
              AND departure_time = :departure_time
        ";

        if ($ignoreId !== null) {
            $sql .= " AND id <> :ignore_id";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $params = [
            'train_id' => $trainId,
            'departure_time' => $departureTime,
        ];

        if ($ignoreId !== null) {
            $params['ignore_id'] = $ignoreId;
        }

        $stmt->execute($params);

        return (bool)$stmt->fetchColumn();
    }

    public function reportByRoute(): array
    {
        $sql = "
            SELECT
                r.name AS route_name,
                COUNT(tr.id) AS trip_count,
                COALESCE(SUM(tr.base_price), 0) AS total_price
            FROM routes r
            LEFT JOIN trips tr ON tr.route_id = r.id
            GROUP BY r.id, r.name
            ORDER BY trip_count DESC, r.name
        ";

        return $this->db->query($sql)->fetchAll();
    }
}

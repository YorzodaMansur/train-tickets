<?php
namespace App\Repositories;

use App\Core\Database;

class SeatRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Талаботи ҳатмӣ: Озод кардани ҷойҳои бандашон гузашта (15 дақиқа)
     */
    public function releaseExpiredLocks(): int {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->query(
            "UPDATE seats 
             SET status = 'available', 
                 reserved_until = NULL, 
                 reserved_by_user_id = NULL 
             WHERE status = 'reserved' 
               AND reserved_until < ?",
            [$now]
        );
        return $stmt->rowCount();
    }

    public function getSeatsByCar(int $carId): array {
        // Ҳатман пеш аз гирифтани ҷойҳо, ҷойҳои бандашон гузаштаро озод мекунем
        $this->releaseExpiredLocks();

        $sql = "SELECT s.*, 
                       c.car_type, c.car_number,
                       t.base_price, c.price_modifier,
                       ROUND(t.base_price * c.price_modifier, 2) AS seat_price
                FROM seats s
                JOIN cars c ON s.car_id = c.id
                JOIN trips t ON c.trip_id = t.id
                WHERE s.car_id = ?
                ORDER BY s.seat_number ASC";
        return $this->db->fetchAll($sql, [$carId]);
    }

    public function getById(int $id): ?array {
        $this->releaseExpiredLocks();
        $sql = "SELECT s.*, 
                       c.car_type, c.car_number, c.trip_id,
                       t.train_number, t.base_price, c.price_modifier,
                       ROUND(t.base_price * c.price_modifier, 2) AS seat_price,
                       r.name AS route_name,
                       s_orig.name_tj AS origin_name,
                       s_dest.name_tj AS dest_name,
                       t.departure_time, t.arrival_time
                FROM seats s
                JOIN cars c ON s.car_id = c.id
                JOIN trips t ON c.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                WHERE s.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Талаботи ҳатмӣ: Банди ҷой ба муддати 15 дақиқа
     */
    public function lockSeatTemporary(int $seatId, ?int $userId = null, int $minutes = 15): bool {
        $this->releaseExpiredLocks();
        $now = date('Y-m-d H:i:s');
        $until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));

        // Санҷиши атомӣ: агар ҷой холӣ бошад ё мӯҳлаташ гузашта бошад
        $sql = "UPDATE seats 
                SET status = 'reserved', 
                    reserved_until = ?, 
                    reserved_by_user_id = ? 
                WHERE id = ? 
                  AND (status = 'available' OR (status = 'reserved' AND reserved_until < ?))";

        $stmt = $this->db->query($sql, [$until, $userId, $seatId, $now]);
        return $stmt->rowCount() > 0;
    }

    public function cancelSeatHold(int $seatId, ?int $userId = null): bool {
        $sql = "UPDATE seats 
                SET status = 'available', 
                    reserved_until = NULL, 
                    reserved_by_user_id = NULL 
                WHERE id = ?";
        $params = [$seatId];
        if ($userId !== null) {
            $sql .= " AND reserved_by_user_id = ?";
            $params[] = $userId;
        }
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    public function confirmPurchase(int $seatId): bool {
        $sql = "UPDATE seats 
                SET status = 'sold', 
                    reserved_until = NULL 
                WHERE id = ?";
        $stmt = $this->db->query($sql, [$seatId]);
        return $stmt->rowCount() > 0;
    }

    public function freeSeat(int $seatId): bool {
        $sql = "UPDATE seats 
                SET status = 'available', 
                    reserved_until = NULL, 
                    reserved_by_user_id = NULL 
                WHERE id = ?";
        $stmt = $this->db->query($sql, [$seatId]);
        return $stmt->rowCount() > 0;
    }

    public function generateSeatsForCar(int $carId, string $carType, int $totalSeats): void {
        for ($s = 1; $s <= $totalSeats; $s++) {
            if ($carType === 'coupe') {
                $type = ($s % 2 === 1) ? 'lower' : 'upper';
            } elseif ($carType === 'platzkart') {
                if ($s <= 36) {
                    $type = ($s % 2 === 1) ? 'lower' : 'upper';
                } else {
                    $type = ($s % 2 === 1) ? 'side_lower' : 'side_upper';
                }
            } else {
                $type = 'standard';
            }

            $this->db->query(
                "INSERT INTO seats (car_id, seat_number, seat_type, status, reserved_until, reserved_by_user_id) 
                 VALUES (?, ?, ?, 'available', NULL, NULL)",
                [$carId, $s, $type]
            );
        }
    }
}

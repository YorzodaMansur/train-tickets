<?php
namespace App\Repositories;

use App\Core\Database;

class TicketRepository {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createTicket(array $data): int {
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        
        $this->db->query(
            "INSERT INTO tickets (ticket_number, trip_id, car_id, seat_id, user_id, 
                                 passenger_name, passenger_doc, price, status, purchase_channel, paid_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $ticketNumber,
                $data['trip_id'],
                $data['car_id'],
                $data['seat_id'],
                $data['user_id'] ?? null,
                $data['passenger_name'],
                $data['passenger_doc'],
                $data['price'],
                $data['status'] ?? 'paid',
                $data['purchase_channel'] ?? 'online',
                date('Y-m-d H:i:s')
            ]
        );

        $ticketId = $this->db->lastInsertId();

        // Ҷойро ба 'sold' мегузаронем
        (new SeatRepository())->confirmPurchase($data['seat_id']);

        return $ticketId;
    }

    public function getById(int $id): ?array {
        $sql = "SELECT tkt.*, 
                       t.train_number, t.departure_time, t.arrival_time,
                       r.name AS route_name, r.distance_km,
                       s_orig.name_tj AS origin_station, s_orig.city AS origin_city,
                       s_dest.name_tj AS destination_station, s_dest.city AS dest_city,
                       c.car_number, c.car_type,
                       s.seat_number, s.seat_type
                FROM tickets tkt
                JOIN trips t ON tkt.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                JOIN cars c ON tkt.car_id = c.id
                JOIN seats s ON tkt.seat_id = s.id
                WHERE tkt.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function getByNumber(string $ticketNumber): ?array {
        $sql = "SELECT tkt.*, 
                       t.train_number, t.departure_time, t.arrival_time,
                       r.name AS route_name,
                       s_orig.name_tj AS origin_station, s_dest.name_tj AS destination_station,
                       c.car_number, c.car_type,
                       s.seat_number, s.seat_type
                FROM tickets tkt
                JOIN trips t ON tkt.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                JOIN cars c ON tkt.car_id = c.id
                JOIN seats s ON tkt.seat_id = s.id
                WHERE tkt.ticket_number = ?";
        return $this->db->fetchOne($sql, [trim($ticketNumber)]);
    }

    public function getByUserId(int $userId): array {
        $sql = "SELECT tkt.*, 
                       t.train_number, t.departure_time, t.arrival_time,
                       r.name AS route_name,
                       s_orig.name_tj AS origin_station, s_dest.name_tj AS destination_station,
                       c.car_number, c.car_type,
                       s.seat_number, s.seat_type
                FROM tickets tkt
                JOIN trips t ON tkt.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                JOIN stations s_orig ON r.origin_station_id = s_orig.id
                JOIN stations s_dest ON r.destination_station_id = s_dest.id
                JOIN cars c ON tkt.car_id = c.id
                JOIN seats s ON tkt.seat_id = s.id
                WHERE tkt.user_id = ?
                ORDER BY tkt.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getAll(int $limit = 50): array {
        $sql = "SELECT tkt.*, 
                       t.train_number, t.departure_time,
                       r.name AS route_name,
                       c.car_number, s.seat_number
                FROM tickets tkt
                JOIN trips t ON tkt.trip_id = t.id
                JOIN routes r ON t.route_id = r.id
                JOIN cars c ON tkt.car_id = c.id
                JOIN seats s ON tkt.seat_id = s.id
                ORDER BY tkt.created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Талаботи функсионалӣ: Баргардонидани чипта ва озод кардани ҷой
     */
    public function refundTicket(int $ticketId, ?int $refundedBy = null): bool {
        $ticket = $this->getById($ticketId);
        if (!$ticket || $ticket['status'] === 'refunded') {
            return false;
        }

        // 1. Ивази ҳолати чипта ба 'refunded'
        $this->db->query(
            "UPDATE tickets 
             SET status = 'refunded', 
                 refunded_at = ?, 
                 refunded_by_user_id = ? 
             WHERE id = ?",
            [date('Y-m-d H:i:s'), $refundedBy, $ticketId]
        );

        // 2. Фавран озод кардани ҷой дар вагон
        (new SeatRepository())->freeSeat($ticket['seat_id']);

        return true;
    }

    public function getSalesReport(): array {
        $sql = "SELECT 
                    r.name AS route_name,
                    COUNT(tkt.id) AS total_tickets,
                    COALESCE(SUM(CASE WHEN tkt.status = 'paid' THEN tkt.price ELSE 0 END), 0) AS total_revenue,
                    COUNT(CASE WHEN tkt.status = 'paid' AND tkt.purchase_channel = 'online' THEN 1 END) AS online_sales,
                    COUNT(CASE WHEN tkt.status = 'paid' AND tkt.purchase_channel = 'cashier' THEN 1 END) AS cashier_sales,
                    COUNT(CASE WHEN tkt.status = 'refunded' THEN 1 END) AS refunds_count
                FROM routes r
                JOIN trips tr ON tr.route_id = r.id
                LEFT JOIN tickets tkt ON tkt.trip_id = tr.id
                GROUP BY r.id, r.name
                ORDER BY total_revenue DESC";
        return $this->db->fetchAll($sql);
    }

    public function getDashboardKpi(): array {
        $totalPaid = $this->db->fetchOne("SELECT COUNT(*) AS cnt, COALESCE(SUM(price), 0) AS rev FROM tickets WHERE status = 'paid'");
        $totalRefunded = $this->db->fetchOne("SELECT COUNT(*) AS cnt, COALESCE(SUM(price), 0) AS rev FROM tickets WHERE status = 'refunded'");
        $activeTrips = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM trips WHERE departure_time >= ?", [date('Y-m-d H:i:s')]);
        $totalUsers = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM users");

        return [
            'tickets_sold' => (int)($totalPaid['cnt'] ?? 0),
            'total_revenue' => (float)($totalPaid['rev'] ?? 0.0),
            'refunds_count' => (int)($totalRefunded['cnt'] ?? 0),
            'refunded_amount' => (float)($totalRefunded['rev'] ?? 0.0),
            'active_trips' => (int)($activeTrips['cnt'] ?? 0),
            'total_users' => (int)($totalUsers['cnt'] ?? 0)
        ];
    }
}

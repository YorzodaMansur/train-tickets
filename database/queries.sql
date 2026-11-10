-- ============================================================
-- Пойгоҳи додаҳо: railway_tickets
-- Дархостҳои калидии SQL (queries.sql)
-- ============================================================

-- 1. Худкор озод кардани ҷойҳои бандашон гузашта (Таймери 15 дақиқа)
-- Ин дархост пеш аз ҳар як амалиёти ҷустуҷӯ ё банд иҷро мешавад:
UPDATE seats
SET status = 'available',
    reserved_until = NULL,
    reserved_by_user_id = NULL
WHERE status = 'reserved'
  AND reserved_until < NOW();

-- 2. Ҷустуҷӯи қатнавҳо аз рӯи истгоҳи аввал, охир ва сана
SELECT 
    t.id AS trip_id,
    t.train_number,
    r.name AS route_name,
    s_orig.name_tj AS origin_station,
    s_dest.name_tj AS destination_station,
    t.departure_time,
    t.arrival_time,
    t.base_price,
    COUNT(CASE WHEN s.status = 'available' THEN 1 END) AS available_seats_count,
    COUNT(s.id) AS total_seats_count
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN stations s_orig ON r.origin_station_id = s_orig.id
JOIN stations s_dest ON r.destination_station_id = s_dest.id
LEFT JOIN cars c ON c.trip_id = t.id
LEFT JOIN seats s ON s.car_id = c.id
WHERE r.origin_station_id = 1 
  AND r.destination_station_id = 2
  AND DATE(t.departure_time) = CURRENT_DATE() + INTERVAL 1 DAY
GROUP BY t.id, t.train_number, r.name, s_orig.name_tj, s_dest.name_tj, t.departure_time, t.arrival_time, t.base_price
ORDER BY t.departure_time ASC;

-- 3. Гирифтани нақшаи вагон бо рангҳо ва ҷойҳо (Seat Map)
SELECT 
    s.id AS seat_id,
    s.seat_number,
    s.seat_type,
    s.status,
    s.reserved_until,
    s.reserved_by_user_id,
    CASE 
        WHEN s.status = 'sold' THEN 'danger'       -- Сурх (фурӯхта шудааст)
        WHEN s.status = 'reserved' AND s.reserved_until > NOW() THEN 'warning' -- Зард (дар банди 15 дақ.)
        ELSE 'success'                             -- Сабз (холӣ барои харид)
    END AS color_class,
    ROUND(t.base_price * c.price_modifier, 2) AS seat_price
FROM seats s
JOIN cars c ON s.car_id = c.id
JOIN trips t ON c.trip_id = t.id
WHERE c.id = 1
ORDER BY s.seat_number ASC;

-- 4. Ҳисоботи фурӯш ва даромад аз рӯи хатсайрҳо (Барои Маъмур)
SELECT 
    r.name AS route_name,
    COUNT(tkt.id) AS tickets_sold,
    COALESCE(SUM(tkt.price), 0.00) AS total_revenue,
    COUNT(CASE WHEN tkt.purchase_channel = 'online' THEN 1 END) AS online_sales,
    COUNT(CASE WHEN tkt.purchase_channel = 'cashier' THEN 1 END) AS cashier_sales,
    COUNT(CASE WHEN tkt.status = 'refunded' THEN 1 END) AS refunds_count
FROM routes r
LEFT JOIN trips tr ON tr.route_id = r.id
LEFT JOIN tickets tkt ON tkt.trip_id = tr.id AND tkt.status IN ('paid', 'refunded')
GROUP BY r.id, r.name
ORDER BY total_revenue DESC;

-- 5. Баргардонидани чипта (Refund) аз ҷониби кассир
UPDATE tickets
SET status = 'refunded',
    refunded_at = NOW(),
    refunded_by_user_id = 2
WHERE id = 3;

-- Фавран озод кардани ҷойи вобаста пас аз баргардонидан:
UPDATE seats
SET status = 'available',
    reserved_until = NULL,
    reserved_by_user_id = NULL
WHERE id = (SELECT seat_id FROM tickets WHERE id = 3);

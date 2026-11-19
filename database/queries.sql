USE railway_tickets;
SET NAMES utf8mb4;

-- 1. ТФ: дидани чипта бо мусофир, қатора ва хатсайр (JOIN).
SELECT tk.ticket_number, tk.passenger_name, tr.train_number, r.name AS route_name,
       tp.departure_time, tk.price, tk.status
FROM tickets tk
JOIN trips tp ON tp.id=tk.trip_id
JOIN trains tr ON tr.id=tp.train_id
JOIN routes r ON r.id=tp.route_id
ORDER BY tp.departure_time;

-- 2. ТФ: ҷустуҷӯи истгоҳ аз рӯи қисми ном (LIKE).
SELECT id, code, name_tj, city FROM stations
WHERE name_tj LIKE '%шаҳ%' OR city LIKE '%шаҳ%'
ORDER BY name_tj;

-- 3. ТФ: шумораи ҳаракатҳо барои ҳар хатсайр (GROUP BY + COUNT).
SELECT r.id, r.name, COUNT(tp.id) AS trips_count
FROM routes r LEFT JOIN trips tp ON tp.route_id=r.id
GROUP BY r.id, r.name
ORDER BY trips_count DESC;

-- 4. ТФ: ҳамаи истгоҳҳо, ҳатто агар ба хатсайр пайваст набошанд (LEFT JOIN).
SELECT s.id, s.name_tj, s.city, COUNT(rs.id) AS route_usage_count
FROM stations s LEFT JOIN route_stations rs ON rs.station_id=s.id
GROUP BY s.id, s.name_tj, s.city
ORDER BY s.name_tj;

-- 5. ТФ: филтр кардани чиптаҳои пардохтшуда аз рӯи ҳолат.
SELECT ticket_number, passenger_name, price, status, purchase_channel
FROM tickets WHERE status='paid'
ORDER BY created_at DESC;

-- 6. ТФ: рӯйхати ҳаракатҳо бо саҳифабандӣ (LIMIT/OFFSET).
SELECT tp.id, tr.train_number, r.name AS route_name, tp.departure_time,
       tp.arrival_time, tp.base_price, tp.status
FROM trips tp
JOIN trains tr ON tr.id=tp.train_id
JOIN routes r ON r.id=tp.route_id
ORDER BY tp.departure_time
LIMIT 5 OFFSET 0;

-- Барои ҳимоя: санҷидани истифодаи индекс.
EXPLAIN SELECT id,status,departure_time FROM trips
WHERE status='scheduled' AND departure_time >= '2026-12-01 00:00:00';

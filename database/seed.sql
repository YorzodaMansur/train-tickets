USE railway_tickets;

SET NAMES utf8mb4;

-- Истгоҳҳои намунавӣ
INSERT IGNORE INTO stations
    (code, name_tj, name_ru, city)
VALUES
    ('DUS', 'Душанбе', 'Душанбе', 'Душанбе'),
    ('KHU', 'Хуҷанд', 'Худжанд', 'Хуҷанд');

-- Қатораҳои намунавӣ
INSERT IGNORE INTO trains
    (train_number, train_name, train_type, status)
VALUES
    ('001', 'Душанбе — Хуҷанд', 'intercity', 'active'),
    ('002', 'Хуҷанд — Душанбе', 'intercity', 'active'),
    ('003', 'Қатораи байнишаҳрӣ', 'intercity', 'active');

-- Хатсайрҳо
INSERT IGNORE INTO routes
    (
        code,
        name,
        origin_station_id,
        destination_station_id,
        distance_km,
        estimated_minutes
    )
SELECT
    'DUS-KHU',
    'Душанбе — Хуҷанд',
    s1.id,
    s2.id,
    300,
    360
FROM stations s1
JOIN stations s2
WHERE s1.code = 'DUS'
  AND s2.code = 'KHU';

INSERT IGNORE INTO routes
    (
        code,
        name,
        origin_station_id,
        destination_station_id,
        distance_km,
        estimated_minutes
    )
SELECT
    'KHU-DUS',
    'Хуҷанд — Душанбе',
    s1.id,
    s2.id,
    300,
    360
FROM stations s1
JOIN stations s2
WHERE s1.code = 'KHU'
  AND s2.code = 'DUS';

-- 12 рейси намунавӣ
INSERT INTO trips
    (
        train_id,
        route_id,
        departure_time,
        arrival_time,
        status,
        base_price
    )
SELECT
    t.id,
    r.id,
    sample.departure_time,
    sample.arrival_time,
    sample.status,
    sample.base_price
FROM (
    SELECT
        '001' AS train_number,
        'DUS-KHU' AS route_code,
        '2026-12-01 08:00:00' AS departure_time,
        '2026-12-01 14:00:00' AS arrival_time,
        'scheduled' AS status,
        180.00 AS base_price

    UNION ALL SELECT
        '001', 'DUS-KHU',
        '2026-12-02 08:00:00',
        '2026-12-02 14:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '001', 'DUS-KHU',
        '2026-12-03 08:00:00',
        '2026-12-03 14:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '001', 'DUS-KHU',
        '2026-12-04 08:00:00',
        '2026-12-04 14:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '001', 'DUS-KHU',
        '2026-12-05 08:00:00',
        '2026-12-05 14:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '001', 'DUS-KHU',
        '2026-12-06 08:00:00',
        '2026-12-06 14:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '002', 'KHU-DUS',
        '2026-12-01 09:00:00',
        '2026-12-01 15:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '002', 'KHU-DUS',
        '2026-12-02 09:00:00',
        '2026-12-02 15:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '002', 'KHU-DUS',
        '2026-12-03 09:00:00',
        '2026-12-03 15:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '002', 'KHU-DUS',
        '2026-12-04 09:00:00',
        '2026-12-04 15:00:00',
        'scheduled', 180.00

    UNION ALL SELECT
        '003', 'DUS-KHU',
        '2026-12-07 07:00:00',
        '2026-12-07 13:00:00',
        'scheduled', 200.00

    UNION ALL SELECT
        '003', 'KHU-DUS',
        '2026-12-08 07:00:00',
        '2026-12-08 13:00:00',
        'scheduled', 200.00
) AS sample

INNER JOIN trains t
    ON t.train_number = sample.train_number

INNER JOIN routes r
    ON r.code = sample.route_code

WHERE NOT EXISTS (
    SELECT 1
    FROM trips existing
    WHERE existing.train_id = t.id
      AND existing.departure_time = sample.departure_time
);
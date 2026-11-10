-- ============================================================
-- Пойгоҳи додаҳо: railway_tickets
-- Маълумоти ибтидоӣ (Seeds)
-- ============================================================

-- 1. Корбарон
INSERT INTO users (id, username, password_hash, full_name, email, phone, role, is_active) VALUES
(1, 'admin', '$2y$10$Hjv9M2cjK/XVIBNG7f/AH.OkszaLug0G93sKA1I6yyTouvnS0RJUm', 'Раҳимов Фарҳод (Маъмур)', 'admin@railway.tj', '+992927001122', 'admin', 1),
(2, 'cashier1', '$2y$10$po.8.uhxYWs7OpE8GDs13umGSlVITaK9.1rHghobheK6j7FNrnmly', 'Саидова Нигина (Кассири Вокзал)', 'cashier@railway.tj', '+992928883344', 'cashier', 1),
(3, 'passenger1', '$2y$10$W7.GTr956rDRCM5UpCcoIuVtyIDHdZ5ShcPabTMlop991RpYxVfhS', 'Қосимов Ҷамшед', 'passenger@example.tj', '+992935556677', 'passenger', 1);

-- 2. Истгоҳҳо
INSERT INTO stations (id, code, name_tj, name_ru, city) VALUES
(1, 'KHJ', 'Хуҷанд', 'Худжанд', 'Хуҷанд'),
(2, 'DSH', 'Душанбе-1', 'Душанбе-1', 'Душанбе'),
(3, 'BOK', 'Бохтар', 'Бохтар', 'Бохтар'),
(4, 'KLB', 'Кӯлоб', 'Куляб', 'Кӯлоб'),
(5, 'KNB', 'Конибодом', 'Канибадам', 'Конибодом'),
(6, 'IST', 'Истиқлол', 'Истиклол', 'Истиқлол');

-- 3. Хатсайрҳо
INSERT INTO routes (id, code, name, origin_station_id, destination_station_id, distance_km, estimated_minutes) VALUES
(1, 'RT-01', 'Хуҷанд — Душанбе-1', 1, 2, 340.00, 360),
(2, 'RT-02', 'Душанбе-1 — Хуҷанд', 2, 1, 340.00, 360),
(3, 'RT-03', 'Душанбе-1 — Кӯлоб', 2, 4, 202.00, 240),
(4, 'RT-04', 'Душанбе-1 — Бохтар', 2, 3, 115.00, 120),
(5, 'RT-05', 'Хуҷанд — Конибодом', 1, 5, 80.00, 90);

-- 4. Қатнавҳо (Санаҳои оянда барои санҷиши бемамониат)
INSERT INTO trips (id, train_number, route_id, departure_time, arrival_time, status, base_price) VALUES
(1, '027-ХД', 1, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) + INTERVAL 14 HOUR, 'scheduled', 80.00),
(2, '028-ДХ', 2, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) + INTERVAL 15 HOUR, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) + INTERVAL 21 HOUR, 'scheduled', 80.00),
(3, '031-ДК', 3, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY) + INTERVAL 9 HOUR, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY) + INTERVAL 13 HOUR, 'scheduled', 50.00),
(4, '035-ДБ', 4, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY) + INTERVAL 7 HOUR, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY) + INTERVAL 9 HOUR, 'scheduled', 35.00);

-- 5. Вагонҳо
INSERT INTO cars (id, trip_id, car_number, car_type, total_seats, price_modifier) VALUES
(1, 1, 1, 'coupe', 36, 1.50),       -- 80 * 1.5 = 120 TJS
(2, 1, 2, 'platzkart', 54, 1.00),   -- 80 * 1.0 = 80 TJS
(3, 1, 3, 'platzkart', 54, 1.00),
(4, 2, 1, 'coupe', 36, 1.50),
(5, 2, 2, 'platzkart', 54, 1.00),
(6, 3, 1, 'platzkart', 54, 1.00);

-- 6. Ҷойҳо барои Вагони 1 (Купе, 36 ҷой)
-- Ҷойҳои тоқ поёнӣ (lower), ҷойҳои ҷуфт болоӣ (upper)
INSERT INTO seats (car_id, seat_number, seat_type, status, reserved_until, reserved_by_user_id) VALUES
(1, 1, 'lower', 'sold', NULL, NULL),
(1, 2, 'upper', 'sold', NULL, NULL),
(1, 3, 'lower', 'available', NULL, NULL),
(1, 4, 'upper', 'available', NULL, NULL),
(1, 5, 'lower', 'reserved', DATE_ADD(NOW(), INTERVAL 15 MINUTE), 3), -- Банд ба 15 дақиқа барои намуна!
(1, 6, 'upper', 'available', NULL, NULL),
(1, 7, 'lower', 'available', NULL, NULL),
(1, 8, 'upper', 'available', NULL, NULL),
(1, 9, 'lower', 'available', NULL, NULL),
(1, 10, 'upper', 'available', NULL, NULL),
(1, 11, 'lower', 'available', NULL, NULL),
(1, 12, 'upper', 'available', NULL, NULL),
(1, 13, 'lower', 'available', NULL, NULL),
(1, 14, 'upper', 'available', NULL, NULL),
(1, 15, 'lower', 'available', NULL, NULL),
(1, 16, 'upper', 'available', NULL, NULL),
(1, 17, 'lower', 'available', NULL, NULL),
(1, 18, 'upper', 'available', NULL, NULL),
(1, 19, 'lower', 'available', NULL, NULL),
(1, 20, 'upper', 'available', NULL, NULL),
(1, 21, 'lower', 'available', NULL, NULL),
(1, 22, 'upper', 'available', NULL, NULL),
(1, 23, 'lower', 'available', NULL, NULL),
(1, 24, 'upper', 'available', NULL, NULL),
(1, 25, 'lower', 'available', NULL, NULL),
(1, 26, 'upper', 'available', NULL, NULL),
(1, 27, 'lower', 'available', NULL, NULL),
(1, 28, 'upper', 'available', NULL, NULL),
(1, 29, 'lower', 'available', NULL, NULL),
(1, 30, 'upper', 'available', NULL, NULL),
(1, 31, 'lower', 'available', NULL, NULL),
(1, 32, 'upper', 'available', NULL, NULL),
(1, 33, 'lower', 'available', NULL, NULL),
(1, 34, 'upper', 'available', NULL, NULL),
(1, 35, 'lower', 'available', NULL, NULL),
(1, 36, 'upper', 'available', NULL, NULL);

-- Ҷойҳо барои Вагони 2 (Плацкарт, 54 ҷой: 1..36 купеи кушод, 37..54 паҳлуӣ)
INSERT INTO seats (car_id, seat_number, seat_type, status, reserved_until, reserved_by_user_id) VALUES
(2, 1, 'lower', 'available', NULL, NULL),
(2, 2, 'upper', 'available', NULL, NULL),
(2, 3, 'lower', 'available', NULL, NULL),
(2, 4, 'upper', 'available', NULL, NULL),
(2, 5, 'lower', 'sold', NULL, NULL),
(2, 6, 'upper', 'available', NULL, NULL),
(2, 7, 'lower', 'available', NULL, NULL),
(2, 8, 'upper', 'available', NULL, NULL),
(2, 9, 'lower', 'available', NULL, NULL),
(2, 10, 'upper', 'available', NULL, NULL),
(2, 11, 'lower', 'available', NULL, NULL),
(2, 12, 'upper', 'available', NULL, NULL),
(2, 13, 'lower', 'available', NULL, NULL),
(2, 14, 'upper', 'available', NULL, NULL),
(2, 15, 'lower', 'available', NULL, NULL),
(2, 16, 'upper', 'available', NULL, NULL),
(2, 17, 'lower', 'available', NULL, NULL),
(2, 18, 'upper', 'available', NULL, NULL),
(2, 19, 'lower', 'available', NULL, NULL),
(2, 20, 'upper', 'available', NULL, NULL),
(2, 21, 'lower', 'available', NULL, NULL),
(2, 22, 'upper', 'available', NULL, NULL),
(2, 23, 'lower', 'available', NULL, NULL),
(2, 24, 'upper', 'available', NULL, NULL),
(2, 25, 'lower', 'available', NULL, NULL),
(2, 26, 'upper', 'available', NULL, NULL),
(2, 27, 'lower', 'available', NULL, NULL),
(2, 28, 'upper', 'available', NULL, NULL),
(2, 29, 'lower', 'available', NULL, NULL),
(2, 30, 'upper', 'available', NULL, NULL),
(2, 31, 'lower', 'available', NULL, NULL),
(2, 32, 'upper', 'available', NULL, NULL),
(2, 33, 'lower', 'available', NULL, NULL),
(2, 34, 'upper', 'available', NULL, NULL),
(2, 35, 'lower', 'available', NULL, NULL),
(2, 36, 'upper', 'available', NULL, NULL),
-- Ҷойҳои паҳлуӣ (боковые)
(2, 37, 'side_lower', 'available', NULL, NULL),
(2, 38, 'side_upper', 'available', NULL, NULL),
(2, 39, 'side_lower', 'available', NULL, NULL),
(2, 40, 'side_upper', 'available', NULL, NULL),
(2, 41, 'side_lower', 'available', NULL, NULL),
(2, 42, 'side_upper', 'available', NULL, NULL),
(2, 43, 'side_lower', 'available', NULL, NULL),
(2, 44, 'side_upper', 'available', NULL, NULL),
(2, 45, 'side_lower', 'available', NULL, NULL),
(2, 46, 'side_upper', 'available', NULL, NULL),
(2, 47, 'side_lower', 'available', NULL, NULL),
(2, 48, 'side_upper', 'available', NULL, NULL),
(2, 49, 'side_lower', 'available', NULL, NULL),
(2, 50, 'side_upper', 'available', NULL, NULL),
(2, 51, 'side_lower', 'available', NULL, NULL),
(2, 52, 'side_upper', 'available', NULL, NULL),
(2, 53, 'side_lower', 'available', NULL, NULL),
(2, 54, 'side_upper', 'available', NULL, NULL);

-- 7. Намунаи чиптаҳои фурӯхташуда
INSERT INTO tickets (id, ticket_number, trip_id, car_id, seat_id, user_id, passenger_name, passenger_doc, price, status, purchase_channel, paid_at) VALUES
(1, 'TKT-2026-0001', 1, 1, 1, 3, 'Қосимов Ҷамшед', 'А2458991', 120.00, 'paid', 'online', NOW()),
(2, 'TKT-2026-0002', 1, 1, 2, NULL, 'Шарипов Алишер', 'А9823101', 120.00, 'paid', 'cashier', NOW()),
(3, 'TKT-2026-0003', 1, 2, 41, NULL, 'Зокиров Баҳодур', 'В1102934', 80.00, 'paid', 'cashier', NOW());

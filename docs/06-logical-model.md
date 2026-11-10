# Логическая модель реляционной базы данных (railway_tickets)

## 1. Таблицы и ключи

### 1.1. Таблица `users` (Пользователи системы)
- `id` (INT, PK, AUTO_INCREMENT) — суррогатный первичный ключ.
- `username` (VARCHAR(50), UNIQUE, NOT NULL) — уникальный логин пользователя.
- `password_hash` (VARCHAR(255), NOT NULL) — хеш пароля BCRYPT.
- `full_name` (VARCHAR(100), NOT NULL) — ФИО пользователя.
- `email` (VARCHAR(100), NULL) — адрес электронной почты.
- `phone` (VARCHAR(30), NULL) — номер телефона.
- `role` (VARCHAR(20), NOT NULL, DEFAULT 'passenger') — роль: `passenger`, `cashier`, `admin`.
- `is_active` (TINYINT(1), NOT NULL, DEFAULT 1) — флаг активности.
- `created_at` (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP).

### 1.2. Таблица `stations` (Станции и вокзалы)
- `id` (INT, PK, AUTO_INCREMENT).
- `code` (VARCHAR(10), UNIQUE, NOT NULL) — код станции (KHJ, DSH, BOK, KLB).
- `name_tj` (VARCHAR(100), NOT NULL) — название на таджикском (Хуҷанд, Душанбе-1).
- `name_ru` (VARCHAR(100), NOT NULL) — название на русском.
- `city` (VARCHAR(100), NOT NULL) — город.

### 1.3. Таблица `routes` (Маршруты поездов)
- `id` (INT, PK, AUTO_INCREMENT).
- `code` (VARCHAR(20), UNIQUE, NOT NULL) — шифр маршрута (RT-01).
- `name` (VARCHAR(150), NOT NULL) — наименование маршрута.
- `origin_station_id` (INT, FK -> stations.id, ON DELETE RESTRICT).
- `destination_station_id` (INT, FK -> stations.id, ON DELETE RESTRICT).
- `distance_km` (DECIMAL(8,2), NOT NULL) — расстояние в километрах.
- `estimated_minutes` (INT, NOT NULL) — расчётное время в пути (минуты).

### 1.4. Таблица `trips` (Конкретные рейсы)
- `id` (INT, PK, AUTO_INCREMENT).
- `train_number` (VARCHAR(20), NOT NULL) — номер поезда (напр., '027-ХД').
- `route_id` (INT, FK -> routes.id, ON DELETE RESTRICT).
- `departure_time` (DATETIME, NOT NULL) — дата и время отправления.
- `arrival_time` (DATETIME, NOT NULL) — дата и время прибытия.
- `status` (VARCHAR(20), NOT NULL, DEFAULT 'scheduled') — `scheduled`, `active`, `completed`, `cancelled`.
- `base_price` (DECIMAL(10,2), NOT NULL) — базовая стоимость билета (сомони).

### 1.5. Таблица `cars` (Вагоны поезда)
- `id` (INT, PK, AUTO_INCREMENT).
- `trip_id` (INT, FK -> trips.id, ON DELETE CASCADE).
- `car_number` (INT, NOT NULL) — порядковый номер вагона в составе поезда (1, 2, 3...).
- `car_type` (VARCHAR(20), NOT NULL) — тип вагона: `coupe` (купе), `platzkart` (плацкарт), `lux` (СВ).
- `total_seats` (INT, NOT NULL, DEFAULT 36) — общее число мест.
- `price_modifier` (DECIMAL(4,2), NOT NULL, DEFAULT 1.00) — коэффициент цены (1.5 для купе, 1.0 для плацкарта).

### 1.6. Таблица `seats` (Места в вагоне)
- `id` (INT, PK, AUTO_INCREMENT).
- `car_id` (INT, FK -> cars.id, ON DELETE CASCADE).
- `seat_number` (INT, NOT NULL) — номер места (1..36 в купе, 1..54 в плацкарте).
- `seat_type` (VARCHAR(20), NOT NULL) — `lower` (нижнее), `upper` (верхнее), `side_lower` (боковое нижнее), `side_upper` (боковое верхнее).
- `status` (VARCHAR(20), NOT NULL, DEFAULT 'available') — `available`, `reserved`, `sold`.
- `reserved_until` (DATETIME, NULL) — метка времени окончания 15-минутного бронирования.
- `reserved_by_user_id` (INT, NULL, FK -> users.id, ON DELETE SET NULL).

### 1.7. Таблица `tickets` (Проездные документы / Билеты)
- `id` (INT, PK, AUTO_INCREMENT).
- `ticket_number` (VARCHAR(30), UNIQUE, NOT NULL) — уникальный номер билета (напр., 'TKT-20261110-A8F21').
- `trip_id` (INT, FK -> trips.id, ON DELETE RESTRICT).
- `car_id` (INT, FK -> cars.id, ON DELETE RESTRICT).
- `seat_id` (INT, FK -> seats.id, ON DELETE RESTRICT).
- `user_id` (INT, NULL, FK -> users.id, ON DELETE SET NULL) — покупатель (если онлайн).
- `passenger_name` (VARCHAR(100), NOT NULL) — ФИО пассажира.
- `passenger_doc` (VARCHAR(50), NOT NULL) — номер паспорта/ID.
- `price` (DECIMAL(10,2), NOT NULL) — фактическая стоимость билета.
- `status` (VARCHAR(20), NOT NULL, DEFAULT 'paid') — `paid`, `refunded`, `cancelled`.
- `purchase_channel` (VARCHAR(20), NOT NULL, DEFAULT 'online') — `online`, `cashier`.
- `paid_at` (DATETIME, NULL).
- `created_at` (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP).
- `refunded_at` (DATETIME, NULL).
- `refunded_by_user_id` (INT, NULL, FK -> users.id, ON DELETE SET NULL).

## 2. Нормализация данных
Все таблицы находятся в **3-й нормальной форме (3НФ)**:
- Каждый атрибут атомарен (1НФ).
- Все неключевые атрибуты функционально полно зависят от первичного ключа (2НФ).
- Отсутствуют транзитивные зависимости неключевых атрибутов от первичного ключа (3НФ). Цены и коэффициенты хранятся в соответствующих сущностях, исключая дублирование данных.

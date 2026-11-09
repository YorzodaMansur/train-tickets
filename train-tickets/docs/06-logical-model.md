# Лаб. 6. Модели мантиқӣ (3NF)

| Ҷадвал | Тавсиф | Калидҳои беруна (ON DELETE) |
|---|---|---|
| roles | нақшҳо | — |
| users | корбарон | role_id → roles (RESTRICT) |
| stations | истгоҳҳо | — |
| trains | қатораҳо | — |
| routes | масирҳо | — |
| route_stops | истгоҳҳои масир | route_id (CASCADE), station_id (RESTRICT) |
| trips | рейсҳо | route_id, train_id (RESTRICT) |
| carriages | вагонҳо | train_id (CASCADE) |
| seats | ҷойҳо | carriage_id (CASCADE) |
| bookings | бронҳо | user_id, trip_id, seat_id (RESTRICT) |
| tickets | чиптаҳо | booking_id (CASCADE) |
| payments | пардохтҳо | booking_id (RESTRICT) |

## Асосноккунии 3NF
...

## Индексҳо
- UNIQUE(trip_id, seat_id) дар bookings — ҷой дубора фурӯхта намешавад
- INDEX(departure_at) дар trips

<?php
/**
 * Танзимоти асосии лоиҳаи railway-tickets
 */

return [
    'app_name' => 'Роҳи оҳани Тоҷикистон — Фурӯши онлайни чиптаҳо',
    'app_url'  => 'http://localhost:8000',
    'lang'     => 'tj', // tj ё ru

    // Танзимоти пойгоҳи додаҳо (MySQL / MariaDB)
    'db' => [
        'driver'   => 'mysql', // 'mysql' ё 'sqlite'
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 'railway_tickets',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
        
        // Роҳ ба файли SQLite ҳангоми fallback
        'sqlite_path' => __DIR__ . '/../database/railway_tickets.sqlite'
    ],

    // Танзимоти банди 15-дақиқагӣ
    'reservation' => [
        'hold_minutes' => 15 // Ҳатмӣ: 15 дақиқа
    ]
];

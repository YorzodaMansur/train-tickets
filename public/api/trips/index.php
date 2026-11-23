<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$trips = [
    [
        'id' => 1,
        'train_number' => '007/008',
        'route_name' => 'Душанбе → Хуҷанд',
        'from' => 'Душанбе',
        'to' => 'Хуҷанд',
        'departure' => '08:00',
        'price' => 120
    ],
    [
        'id' => 2,
        'train_number' => '009/010',
        'route_name' => 'Хуҷанд → Душанбе',
        'from' => 'Хуҷанд',
        'to' => 'Душанбе',
        'departure' => '09:30',
        'price' => 120
    ],
    [
        'id' => 3,
        'train_number' => '011/012',
        'route_name' => 'Душанбе → Бохтар',
        'from' => 'Душанбе',
        'to' => 'Бохтар',
        'departure' => '07:15',
        'price' => 85
    ],
    [
        'id' => 4,
        'train_number' => '013/014',
        'route_name' => 'Душанбе → Кӯлоб',
        'from' => 'Душанбе',
        'to' => 'Кӯлоб',
        'departure' => '10:00',
        'price' => 95
    ],
    [
        'id' => 5,
        'train_number' => '015/016',
        'route_name' => 'Хуҷанд → Истаравшан',
        'from' => 'Хуҷанд',
        'to' => 'Истаравшан',
        'departure' => '11:20',
        'price' => 65
    ],
    [
        'id' => 6,
        'train_number' => '017/018',
        'route_name' => '<img src=x onerror=alert(1)> Душанбе → Хуҷанд',
        'from' => 'Душанбе',
        'to' => 'Хуҷанд',
        'departure' => '14:00',
        'price' => 120
    ]
];

$query = trim((string)($_GET['q'] ?? ''));

$normalize = static function (string $value): string {
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
};

if ($query !== '') {
    $search = $normalize($query);

    $trips = array_values(array_filter(
        $trips,
        static function (array $trip) use ($search, $normalize): bool {
            $text = $normalize(
                $trip['train_number'] . ' ' .
                    $trip['route_name'] . ' ' .
                    $trip['from'] . ' ' .
                    $trip['to']
            );

            return strpos($text, $search) !== false;
        }
    ));
}

echo json_encode([
    'success' => true,
    'count' => count($trips),
    'data' => $trips
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

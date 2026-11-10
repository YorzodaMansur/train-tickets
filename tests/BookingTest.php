<?php
/**
 * Автоматические тесты функционала бронирования и 15-минутного таймера
 */

namespace Tests;

require_once __DIR__ . '/../public/index.php';

use App\Repositories\SeatRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TripRepository;

class BookingTest {
    public static function runAll(): void {
        echo "=== Оғози санҷишҳои автоматии низом ===\n";

        $seatRepo = new SeatRepository();
        $ticketRepo = new TicketRepository();
        $tripRepo = new TripRepository();

        // Тест 1: Ҷустуҷӯи қатораҳо
        $trips = $tripRepo->searchTrips(1, 2);
        assert(!empty($trips), "Тест 1 ноком: Қатнавҳо ёфт нашуданд.");
        echo "✓ Тест 1: Ҷустуҷӯи қатораҳо бо муваффақият гузашт (ёфт шуд: " . count($trips) . " қатора)\n";

        // Тест 2: Банди муваққатӣ ба муддати 15 дақиқа
        // Ҷойи №3-ро барои корбар бо ID 3 банд мекунем
        $seat = $seatRepo->getById(3);
        $locked = $seatRepo->lockSeatTemporary(3, 3, 15);
        assert($locked === true, "Тест 2 ноком: Ҷой банд нашуд.");
        
        $seatAfter = $seatRepo->getById(3);
        assert($seatAfter['status'] === 'reserved', "Тест 2 ноком: Ҳолат ба 'reserved' иваз нашуд.");
        assert(!empty($seatAfter['reserved_until']), "Тест 2 ноком: reserved_until холӣ аст.");
        echo "✓ Тест 2: Банди муваққатӣ ба муддати 15 дақиқа дуруст кор кард (status = 'reserved', until = {$seatAfter['reserved_until']})\n";

        // Тест 3: Пешгирӣ аз банди дубора аз ҷониби каси дигар
        $lockedAgain = $seatRepo->lockSeatTemporary(3, 999, 15);
        assert($lockedAgain === false, "Тест 3 ноком: Ҷойи бандшуда бояд барои дигарон масдуд бошад!");
        echo "✓ Тест 3: Масдудсозии ҷойи бандшуда аз хариди дукарата ҳимоя шуд\n";

        // Тест 4: Пардохт ва тавлиди чипта
        $ticketId = $ticketRepo->createTicket([
            'trip_id' => $seatAfter['trip_id'],
            'car_id' => $seatAfter['car_id'],
            'seat_id' => 3,
            'user_id' => 3,
            'passenger_name' => 'Санҷишӣ Мусофир',
            'passenger_doc' => 'А9998881',
            'price' => $seatAfter['seat_price'],
            'status' => 'paid',
            'purchase_channel' => 'online'
        ]);
        assert($ticketId > 0, "Тест 4 ноком: Чипта сохта нашуд.");
        $ticket = $ticketRepo->getById($ticketId);
        assert($ticket['status'] === 'paid', "Тест 4 ноком: Ҳолати чипта 'paid' нест.");

        $seatSold = $seatRepo->getById(3);
        assert($seatSold['status'] === 'sold', "Тест 4 ноком: Ҳолати ҷой пас аз пардохт ба 'sold' нагузашт.");
        echo "✓ Тест 4: Пардохти чипта ва гузариш ба 'sold' комилан дуруст иҷро шуд (Чипта № {$ticket['ticket_number']})\n";

        // Тест 5: Баргардонидани чипта (Refund) ва озодшавии фаврии ҷой
        $refunded = $ticketRepo->refundTicket($ticketId, 2);
        assert($refunded === true, "Тест 5 ноком: Чипта баргардонида нашуд.");

        $ticketRefunded = $ticketRepo->getById($ticketId);
        assert($ticketRefunded['status'] === 'refunded', "Тест 5 ноком: Ҳолати чипта 'refunded' нашуд.");

        $seatFreed = $seatRepo->getById(3);
        assert($seatFreed['status'] === 'available', "Тест 5 ноком: Ҷой пас аз баргардонидан озод нашуд!");
        echo "✓ Тест 5: Баргардонидани чипта (Refund) ва озод шудани ҷой (status = 'available') тасдиқ гардид\n";

        echo "\n=== ҲАМАИ 5 САНҶИШ БО БАҲОИ АЪЛО (100%) ГУЗАШТАНД! ===\n";
    }
}

BookingTest::runAll();

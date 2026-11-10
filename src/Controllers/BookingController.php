<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Pdf;
use App\Core\View;
use App\Repositories\CarRepository;
use App\Repositories\SeatRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TripRepository;

class BookingController {
    private SeatRepository $seatRepo;
    private TicketRepository $ticketRepo;
    private TripRepository $tripRepo;
    private CarRepository $carRepo;

    public function __construct() {
        $this->seatRepo = new SeatRepository();
        $this->ticketRepo = new TicketRepository();
        $this->tripRepo = new TripRepository();
        $this->carRepo = new CarRepository();
    }

    /**
     * Талаботи ҳатмӣ: Нақшаи интерактивии вагон бо рангҳо
     */
    public function seatMap(string $tripId, ?string $carId = null): void {
        $tId = (int)$tripId;
        $trip = $this->tripRepo->getById($tId);
        if (!$trip) {
            header('Location: /');
            exit;
        }

        $cars = $this->carRepo->getByTripId($tId);
        if (empty($cars)) {
            $_SESSION['flash_error'] = 'Дар ин қатора ҳоло вагонҳо вуҷуд надоранд.';
            header('Location: /');
            exit;
        }

        // Агар вагон интихоб нашуда бошад, вагони аввалро мегирем
        $currentCarId = $carId ? (int)$carId : (int)$cars[0]['id'];
        $currentCar = $this->carRepo->getById($currentCarId);
        $seats = $this->seatRepo->getSeatsByCar($currentCarId);

        $userId = Auth::id();

        View::render('booking.seat_map', [
            'title' => "Интихоби ҷой — Қатораи №{$trip['train_number']}, Вагони №{$currentCar['car_number']}",
            'trip' => $trip,
            'cars' => $cars,
            'currentCar' => $currentCar,
            'seats' => $seats,
            'userId' => $userId
        ]);
    }

    /**
     * Талаботи ҳатмӣ: Банди муваққатӣ ба муддати 15 дақиқа (AJAX / JSON)
     */
    public function hold(): void {
        header('Content-Type: application/json; charset=utf-8');

        $seatId = (int)($_POST['seat_id'] ?? 0);
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!Csrf::validate($token)) {
            echo json_encode(['success' => false, 'message' => 'CSRF токен нодуруст аст.']);
            exit;
        }

        if ($seatId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Рақами ҷой нодуруст аст.']);
            exit;
        }

        $userId = Auth::id() ?? 0;
        $locked = $this->seatRepo->lockSeatTemporary($seatId, $userId, 15);

        if ($locked) {
            $seat = $this->seatRepo->getById($seatId);
            $expiresAt = strtotime($seat['reserved_until']);
            $now = time();
            $expiresInSeconds = max(0, $expiresAt - $now);

            echo json_encode([
                'success' => true,
                'seat_id' => $seatId,
                'seat_number' => $seat['seat_number'],
                'price' => $seat['seat_price'],
                'expires_at' => $seat['reserved_until'],
                'expires_in' => $expiresInSeconds,
                'message' => 'Ҷой ба муддати 15 дақиқа бо муваффақият банд шуд.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Мутаассифона, ин ҷой аллакай банд ё фурӯхта шудааст. Лутфан ҷойи дигарро интихоб намоед.'
            ]);
        }
        exit;
    }

    /**
     * Бекор кардани банди 15-дақиқагӣ бо хоҳиши корбар (AJAX)
     */
    public function cancelHold(): void {
        header('Content-Type: application/json; charset=utf-8');
        $seatId = (int)($_POST['seat_id'] ?? 0);
        $userId = Auth::id();

        $this->seatRepo->cancelSeatHold($seatId, $userId);
        echo json_encode(['success' => true, 'message' => 'Банди ҷой бекор карда шуд.']);
        exit;
    }

    /**
     * Саҳифаи расмиёти пардохт бо таймери 15:00
     */
    public function checkout(string $seatId): void {
        $sId = (int)$seatId;
        $seat = $this->seatRepo->getById($sId);

        if (!$seat) {
            $_SESSION['flash_error'] = 'Ҷой ёфт нашуд.';
            header('Location: /');
            exit;
        }

        $userId = Auth::id();

        // Санҷиш: оё ҷой барои ин корбар банд аст?
        $now = time();
        $reservedUntil = $seat['reserved_until'] ? strtotime($seat['reserved_until']) : 0;

        if ($seat['status'] === 'sold') {
            $_SESSION['flash_error'] = 'Ин ҷой аллакай фурӯхта шудааст.';
            header("Location: /booking/seat-map/{$seat['trip_id']}/{$seat['car_id']}");
            exit;
        }

        if ($seat['status'] !== 'reserved' || $reservedUntil <= $now) {
            // Агар ҷой банд набошад, кӯшиш мекунем ҳоло онро ба муддати 15 дақиқа банд кунем
            $locked = $this->seatRepo->lockSeatTemporary($sId, $userId, 15);
            if (!$locked) {
                $_SESSION['flash_error'] = 'Мӯҳлати банди ҷой ба охир расид ё аз ҷониби каси дигар гирифта шуд.';
                header("Location: /booking/seat-map/{$seat['trip_id']}/{$seat['car_id']}");
                exit;
            }
            $seat = $this->seatRepo->getById($sId);
            $reservedUntil = strtotime($seat['reserved_until']);
        }

        $remainingSeconds = max(0, $reservedUntil - $now);

        View::render('booking.checkout', [
            'title' => "Пардохти чипта — Ҷойи №{$seat['seat_number']}",
            'seat' => $seat,
            'remainingSeconds' => $remainingSeconds,
            'user' => Auth::user()
        ]);
    }

    /**
     * Раванди пардохт ва тавлиди чипта
     */
    public function processPayment(): void {
        Csrf::checkOrAbort();

        $seatId = (int)($_POST['seat_id'] ?? 0);
        $passengerName = trim($_POST['passenger_name'] ?? '');
        $passengerDoc = trim($_POST['passenger_doc'] ?? '');

        if ($seatId <= 0 || empty($passengerName) || empty($passengerDoc)) {
            $_SESSION['flash_error'] = 'Ҳамаи майдонҳои ному насаб ва рақами шиноснома ҳатмианд.';
            header("Location: /booking/checkout/{$seatId}");
            exit;
        }

        $seat = $this->seatRepo->getById($seatId);
        if (!$seat) {
            $_SESSION['flash_error'] = 'Маълумоти ҷой ёфт нашуд.';
            header('Location: /');
            exit;
        }

        // Санҷиши қатъии мӯҳлати 15 дақиқа
        $now = time();
        $reservedUntil = $seat['reserved_until'] ? strtotime($seat['reserved_until']) : 0;
        if ($seat['status'] !== 'reserved' || $reservedUntil < $now) {
            $this->seatRepo->freeSeat($seatId);
            $_SESSION['flash_error'] = 'Мӯҳлати 15-дақиқагии банди ҷой гузашт! Ҷой озод шуд. Лутфан аз нав интихоб кунед.';
            header("Location: /booking/seat-map/{$seat['trip_id']}/{$seat['car_id']}");
            exit;
        }

        // Сабти расмии чипта ва гузариши ҷой ба ҳолати 'sold'
        $ticketId = $this->ticketRepo->createTicket([
            'trip_id' => $seat['trip_id'],
            'car_id' => $seat['car_id'],
            'seat_id' => $seatId,
            'user_id' => Auth::id(),
            'passenger_name' => $passengerName,
            'passenger_doc' => $passengerDoc,
            'price' => $seat['seat_price'],
            'status' => 'paid',
            'purchase_channel' => Auth::hasRole('cashier') ? 'cashier' : 'online'
        ]);

        $_SESSION['flash_success'] = 'Табрик! Чипта бомуваффақият харидорӣ шуд. Шумо метавонед онро чоп ё дар PDF сабт намоед.';
        header("Location: /tickets/view/{$ticketId}");
        exit;
    }

    /**
     * Талаботи функсионалӣ: Чопи чипта ва намоиши PDF
     */
    public function viewTicket(string $id): void {
        $ticketId = (int)$id;
        $ticket = $this->ticketRepo->getById($ticketId);

        if (!$ticket) {
            http_response_code(404);
            View::render('home.404', ['title' => 'Чипта ёфт нашуд', 'uri' => "/tickets/view/{$ticketId}"]);
            return;
        }

        // Агар параметр `format=pdf` ё `print=1` бошад
        if (isset($_GET['print'])) {
            echo Pdf::renderTicketHtml($ticket);
            exit;
        }

        View::render('booking.ticket_view', [
            'title' => "Чиптаи №{$ticket['ticket_number']}",
            'ticket' => $ticket
        ]);
    }

    /**
     * Рӯйхати чиптаҳои мусофир
     */
    public function myTickets(): void {
        Auth::requireLogin();
        $userId = Auth::id();
        $tickets = $this->ticketRepo->getByUserId($userId);

        View::render('booking.my_tickets', [
            'title' => 'Чиптаҳои харидаи ман',
            'tickets' => $tickets
        ]);
    }

    /**
     * Баргардонидани чипта аз ҷониби мусофир
     */
    public function refund(string $id): void {
        Auth::requireLogin();
        Csrf::checkOrAbort();

        $ticketId = (int)$id;
        $ticket = $this->ticketRepo->getById($ticketId);

        if (!$ticket) {
            $_SESSION['flash_error'] = 'Чипта ёфт нашуд.';
            header('Location: /my-tickets');
            exit;
        }

        // Танҳо соҳиби чипта ё кассир/админ баргардонда метавонад
        if ($ticket['user_id'] !== Auth::id() && !Auth::hasRole('cashier', 'admin')) {
            $_SESSION['flash_error'] = 'Шумо танҳо чиптаҳои шахсии худро баргардонда метавонед.';
            header('Location: /my-tickets');
            exit;
        }

        $success = $this->ticketRepo->refundTicket($ticketId, Auth::id());
        if ($success) {
            $_SESSION['flash_success'] = 'Чипта бомуваффақият баргардонида шуд (Refund). Ҷой фавран барои дигар мусофирон озод гардид.';
        } else {
            $_SESSION['flash_error'] = 'Хатогӣ ҳангоми баргардонидани чипта.';
        }

        header('Location: /my-tickets');
        exit;
    }
}

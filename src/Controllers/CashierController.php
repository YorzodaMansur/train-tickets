<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repositories\StationRepository;
use App\Repositories\TripRepository;
use App\Repositories\CarRepository;
use App\Repositories\SeatRepository;
use App\Repositories\TicketRepository;

class CashierController {
    private TicketRepository $ticketRepo;
    private TripRepository $tripRepo;
    private StationRepository $stationRepo;

    public function __construct() {
        Auth::requireRole('cashier', 'admin');
        $this->ticketRepo = new TicketRepository();
        $this->tripRepo = new TripRepository();
        $this->stationRepo = new StationRepository();
    }

    public function index(): void {
        $stations = $this->stationRepo->getAll();
        $recentTickets = $this->ticketRepo->getAll(15);
        $trips = $this->tripRepo->getAll();

        View::render('cashier.index', [
            'title' => 'Терминали кории кассир — Роҳи оҳан',
            'stations' => $stations,
            'recentTickets' => $recentTickets,
            'trips' => $trips
        ]);
    }

    public function lookup(): void {
        $number = trim($_GET['ticket_number'] ?? '');
        $ticket = null;
        if (!empty($number)) {
            $ticket = $this->ticketRepo->getByNumber($number);
        }

        View::render('cashier.lookup', [
            'title' => 'Ҷустуҷӯ ва баргардонидани чипта',
            'ticketNumber' => $number,
            'ticket' => $ticket
        ]);
    }

    public function fastSale(): void {
        Csrf::checkOrAbort();
        $seatId = (int)($_POST['seat_id'] ?? 0);
        $passengerName = trim($_POST['passenger_name'] ?? '');
        $passengerDoc = trim($_POST['passenger_doc'] ?? '');

        if ($seatId <= 0 || empty($passengerName) || empty($passengerDoc)) {
            $_SESSION['flash_error'] = 'Ҳамаи майдонҳоро пур кунед.';
            header('Location: /cashier');
            exit;
        }

        $seatRepo = new SeatRepository();
        $seat = $seatRepo->getById($seatId);

        if (!$seat || $seat['status'] === 'sold') {
            $_SESSION['flash_error'] = 'Ин ҷой аллакай фурӯхта шудааст.';
            header('Location: /cashier');
            exit;
        }

        // Кассир бе банди 15-дақиқагӣ фавран чиптаро мефурӯшад
        $ticketId = $this->ticketRepo->createTicket([
            'trip_id' => $seat['trip_id'],
            'car_id' => $seat['car_id'],
            'seat_id' => $seatId,
            'user_id' => null,
            'passenger_name' => $passengerName,
            'passenger_doc' => $passengerDoc,
            'price' => $seat['seat_price'],
            'status' => 'paid',
            'purchase_channel' => 'cashier'
        ]);

        $_SESSION['flash_success'] = "Чипта дар касса бомуваффақият дода шуд! Маблағ: {$seat['seat_price']} сомонӣ.";
        header("Location: /tickets/view/{$ticketId}");
        exit;
    }

    public function processRefund(): void {
        Csrf::checkOrAbort();
        $ticketId = (int)($_POST['ticket_id'] ?? 0);

        $success = $this->ticketRepo->refundTicket($ticketId, Auth::id());
        if ($success) {
            $_SESSION['flash_success'] = 'Чипта бомуваффақият баргардонида шуд (Refund). Маблағи мусофир бозпас гардонида шуд ва ҷой озод шуд.';
        } else {
            $_SESSION['flash_error'] = 'Баргардонидани ин чипта номумкин аст ё аллакай баргардонида шудааст.';
        }

        header('Location: /cashier/lookup');
        exit;
    }
}

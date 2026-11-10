<?php
use App\Core\Csrf;
?>
<div class="container my-4">
    <!-- Сарлавҳаи сафар ва интихоби вагонҳо -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <span class="badge bg-warning text-dark fs-6 px-3 py-1 fw-bold mb-1">
                    Қатораи № <?= htmlspecialchars($trip['train_number']) ?>
                </span>
                <h4 class="fw-bold mb-0"><?= htmlspecialchars($trip['route_name']) ?></h4>
                <div class="small text-muted">
                    <i class="bi bi-clock me-1"></i> Равонашавӣ: <?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?>
                </div>
            </div>
            <div>
                <span class="text-muted small d-block">Вагони интихобшуда:</span>
                <span class="fs-4 fw-bold text-primary">№ <?= $currentCar['car_number'] ?> (<?= ($currentCar['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)</span>
            </div>
        </div>

        <!-- Тугмаҳои гузариш байни вагонҳо -->
        <div class="mt-3 pt-3 border-top">
            <span class="small fw-bold text-muted me-2">Гузариш ба вагони дигар:</span>
            <div class="btn-group btn-group-sm flex-wrap" role="group">
                <?php foreach ($cars as $c): ?>
                    <a href="/booking/seat-map/<?= $trip['id'] ?>/<?= $c['id'] ?>" 
                       class="btn <?= ($c['id'] == $currentCar['id']) ? 'btn-primary active fw-bold' : 'btn-outline-primary' ?>">
                        Вагони <?= $c['car_number'] ?> (<?= ($c['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Легендаи рангҳо (Талаботи махсус) -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="row text-center g-2 align-items-center">
            <div class="col-6 col-md-3">
                <span class="badge-seat-sample bg-success text-white">1</span>
                <span class="ms-1 small fw-bold">Холӣ (сабз)</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="badge-seat-sample bg-warning text-dark">5</span>
                <span class="ms-1 small fw-bold">Банд ба 15 дақ. (зард)</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="badge-seat-sample bg-danger text-white">2</span>
                <span class="ms-1 small fw-bold">Фурӯхташуда (сурх)</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="badge-seat-sample bg-primary text-white">✓</span>
                <span class="ms-1 small fw-bold">Интихоби шумо (кабуд)</span>
            </div>
        </div>
    </div>

    <!-- Схемаи интерактивии вагон -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-grid-3x3 me-2 text-primary"></i> Нақшаи дохили вагон
                    </h5>
                    <span class="badge bg-secondary-subtle text-dark border">
                        ← Самти ҳаракати қатора (Локомотив)
                    </span>
                </div>

                <!-- Контейнери графикӣ барои вагон -->
                <div class="train-car-wrapper overflow-auto p-3 bg-light rounded-4 border border-2 border-secondary-subtle">
                    <div class="train-car-roof text-center small text-muted py-1 border-bottom">
                        <i class="bi bi-chevron-double-left me-1"></i> САРШАВИИ ВАГОН (ТАМБУРИ 1) — ВАГОНИ № <?= $currentCar['car_number'] ?> — ТАМБУРИ 2 <i class="bi bi-chevron-double-right ms-1"></i>
                    </div>

                    <?php if ($currentCar['car_type'] === 'coupe'): ?>
                        <!-- Схемаи Купе (9 ҳуҷраи 4-ҷоя: 36 ҷой) -->
                        <div class="coupe-corridor-layout mt-3">
                            <div class="d-flex gap-3 justify-content-start pb-2">
                                <?php for ($cp = 0; $cp < 9; $cp++): ?>
                                    <div class="coupe-compartment border rounded-3 p-2 bg-white shadow-sm text-center">
                                        <div class="small text-muted fw-bold mb-1">Купе <?= $cp + 1 ?></div>
                                        <div class="d-flex justify-content-between gap-2 mb-2">
                                            <!-- Ҷойҳои болоӣ: ҷуфт (2, 4, ...) -->
                                            <?php
                                                $sUp1 = $cp * 4 + 2;
                                                $sUp2 = $cp * 4 + 4;
                                                $seatUp1 = $seats[$sUp1 - 1] ?? null;
                                                $seatUp2 = $seats[$sUp2 - 1] ?? null;
                                            ?>
                                            <?= renderSeatButton($seatUp1, $userId) ?>
                                            <?= renderSeatButton($seatUp2, $userId) ?>
                                        </div>
                                        <div class="compartment-table py-1 bg-light border rounded small text-muted mb-2">Мизи купе</div>
                                        <div class="d-flex justify-content-between gap-2">
                                            <!-- Ҷойҳои поёнӣ: тоқ (1, 3, ...) -->
                                            <?php
                                                $sLow1 = $cp * 4 + 1;
                                                $sLow2 = $cp * 4 + 3;
                                                $seatLow1 = $seats[$sLow1 - 1] ?? null;
                                                $seatLow2 = $seats[$sLow2 - 1] ?? null;
                                            ?>
                                            <?= renderSeatButton($seatLow1, $userId) ?>
                                            <?= renderSeatButton($seatLow2, $userId) ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <!-- Роҳрави вагон (Corridor) -->
                            <div class="train-aisle py-2 text-center text-muted small border-top border-bottom mt-2 bg-secondary-subtle">
                                <i class="bi bi-arrow-left-right me-1"></i> РОҲРАВИ ВАГОНИ КУПЕ (ТИРЕЗАҲО) <i class="bi bi-arrow-left-right ms-1"></i>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Схемаи Плацкарт (36 ҷойи купеӣ + 18 ҷойи паҳлуӣ = 54 ҷой) -->
                        <div class="platzkart-layout mt-3">
                            <!-- Қисми ҷойҳои асосӣ (1..36) -->
                            <div class="d-flex gap-3 justify-content-start pb-2">
                                <?php for ($cp = 0; $cp < 9; $cp++): ?>
                                    <div class="coupe-compartment border rounded-3 p-2 bg-white shadow-sm text-center">
                                        <div class="small text-muted fw-bold mb-1">Бахши <?= $cp + 1 ?></div>
                                        <div class="d-flex justify-content-between gap-2 mb-2">
                                            <?php
                                                $sUp1 = $cp * 4 + 2;
                                                $sUp2 = $cp * 4 + 4;
                                                $seatUp1 = $seats[$sUp1 - 1] ?? null;
                                                $seatUp2 = $seats[$sUp2 - 1] ?? null;
                                            ?>
                                            <?= renderSeatButton($seatUp1, $userId) ?>
                                            <?= renderSeatButton($seatUp2, $userId) ?>
                                        </div>
                                        <div class="compartment-table py-1 bg-light border rounded small text-muted mb-2">Миз</div>
                                        <div class="d-flex justify-content-between gap-2">
                                            <?php
                                                $sLow1 = $cp * 4 + 1;
                                                $sLow2 = $cp * 4 + 3;
                                                $seatLow1 = $seats[$sLow1 - 1] ?? null;
                                                $seatLow2 = $seats[$sLow2 - 1] ?? null;
                                            ?>
                                            <?= renderSeatButton($seatLow1, $userId) ?>
                                            <?= renderSeatButton($seatLow2, $userId) ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <!-- Роҳрави байни ҷойҳо -->
                            <div class="train-aisle py-2 text-center text-muted small border-top border-bottom my-2 bg-secondary-subtle">
                                <i class="bi bi-arrow-left-right me-1"></i> РОҲРАВИ МИЁНАИ ПЛАЦКАРТ <i class="bi bi-arrow-left-right ms-1"></i>
                            </div>

                            <!-- Ҷойҳои паҳлуӣ (боковые: 37..54) -->
                            <div class="d-flex gap-3 justify-content-start pt-1">
                                <?php for ($cp = 0; $cp < 9; $cp++): ?>
                                    <div class="side-compartment border rounded-3 p-2 bg-white shadow-sm text-center" style="width: 140px;">
                                        <div class="small text-muted fw-bold mb-1">Паҳлуӣ <?= $cp + 1 ?></div>
                                        <div class="d-flex justify-content-between gap-2">
                                            <?php
                                                $sSideUp = 54 - ($cp * 2);
                                                $sSideLow = 54 - ($cp * 2 + 1);
                                                $seatSideUp = $seats[$sSideUp - 1] ?? null;
                                                $seatSideLow = $seats[$sSideLow - 1] ?? null;
                                            ?>
                                            <?= renderSeatButton($seatSideLow, $userId) ?>
                                            <?= renderSeatButton($seatSideUp, $userId) ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Панели маълумоти ҷойи интихобшуда ва тугмаи банди 15-дақиқагӣ -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-white" style="top: 80px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i> Маълумоти ҷой
                </h5>

                <div id="seatSelectedPanel" style="display: none;">
                    <div class="bg-primary-subtle text-primary p-3 rounded-4 mb-3 text-center">
                        <div class="small text-uppercase fw-bold">Ҷойи интихобшуда:</div>
                        <div class="display-5 fw-bold" id="panelSeatNumber">-</div>
                        <div class="fw-semibold" id="panelSeatType">-</div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Вагон:</span>
                        <strong class="text-dark">№ <?= $currentCar['car_number'] ?> (<?= ($currentCar['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Нархи чипта:</span>
                        <strong class="fs-4 text-success"><span id="panelSeatPrice">0.00</span> сомонӣ</strong>
                    </div>

                    <!-- Огоҳнома дар бораи 15 дақиқа -->
                    <div class="alert alert-warning py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-clock-history me-1"></i>
                        Ҷой ба муддати <strong>15 дақиқа</strong> барои шумо банд карда мешавад. Агар дар 15 дақиқа пардохт нашавад, худкор озод мегардад.
                    </div>

                    <!-- Формаи банд кардан ва гузариш ба пардохт -->
                    <form id="holdSeatForm" action="/booking/hold" method="POST">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="seat_id" id="hiddenSeatId" value="">
                        <button type="button" id="btnHoldAndPay" class="btn btn-warning w-100 py-3 fw-bold fs-5 shadow-sm">
                            <i class="bi bi-shield-lock-fill me-1"></i> Банд кардан ва пардохт
                        </button>
                    </form>
                </div>

                <div id="seatEmptyPrompt" class="text-center py-4 text-muted">
                    <i class="bi bi-cursor-fill display-4 text-secondary mb-2 d-block"></i>
                    <p class="mb-0">Барои харидорӣ яке аз ҷойҳои <strong class="text-success">сабзро</strong> дар нақшаи вагон клик намоед.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Функсияи ёрирасон барои намоиши тугмаи ҷой
function renderSeatButton(?array $seat, ?int $userId): string {
    if (!$seat) return '';

    $sNum = $seat['seat_number'];
    $sId = $seat['id'];
    $status = $seat['status'];
    $type = $seat['seat_type'];
    $price = $seat['seat_price'];

    $typeName = match($type) {
        'lower' => 'Поёнӣ',
        'upper' => 'Болоӣ',
        'side_lower' => 'Паҳлуӣ поёнӣ',
        'side_upper' => 'Паҳлуӣ болоӣ',
        default => 'Ҷой'
    };

    if ($status === 'sold') {
        return "<button type='button' class='btn btn-danger btn-seat-item disabled' title='Ҷойи №{$sNum} фурӯхта шудааст' disabled>{$sNum}</button>";
    }

    if ($status === 'reserved') {
        $isMyHold = ($userId && $seat['reserved_by_user_id'] == $userId);
        $cls = $isMyHold ? 'btn-primary' : 'btn-warning';
        $title = $isMyHold ? "Ҷойи №{$sNum} аз ҷониби шумо банд аст" : "Ҷойи №{$sNum} дар банди 15-дақиқагӣ қарор дорад";
        return "<button type='button' class='btn {$cls} btn-seat-item' data-seat-id='{$sId}' data-seat-num='{$sNum}' data-seat-type='{$typeName}' data-seat-price='{$price}' onclick='selectSeat(this)' title='{$title}'>{$sNum}</button>";
    }

    // Available
    return "<button type='button' class='btn btn-outline-success btn-seat-item' data-seat-id='{$sId}' data-seat-num='{$sNum}' data-seat-type='{$typeName}' data-seat-price='{$price}' onclick='selectSeat(this)' title='Ҷойи №{$sNum} ({$typeName}) — {$price} сом.'>{$sNum}</button>";
}
?>

<script>
let selectedButton = null;

function selectSeat(btn) {
    if (selectedButton) {
        // Баргардонидани тугмаи пешина ба ранги аслӣ
        selectedButton.classList.remove('btn-primary', 'shadow-lg');
        selectedButton.classList.add('btn-outline-success');
    }

    selectedButton = btn;
    btn.classList.remove('btn-outline-success');
    btn.classList.add('btn-primary', 'shadow-lg');

    const sId = btn.getAttribute('data-seat-id');
    const sNum = btn.getAttribute('data-seat-num');
    const sType = btn.getAttribute('data-seat-type');
    const sPrice = btn.getAttribute('data-seat-price');

    document.getElementById('hiddenSeatId').value = sId;
    document.getElementById('panelSeatNumber').innerText = sNum;
    document.getElementById('panelSeatType').innerText = sType;
    document.getElementById('panelSeatPrice').innerText = sPrice;

    document.getElementById('seatEmptyPrompt').style.display = 'none';
    document.getElementById('seatSelectedPanel').style.display = 'block';
}

document.getElementById('btnHoldAndPay').addEventListener('click', function() {
    const seatId = document.getElementById('hiddenSeatId').value;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Банд карда мешавад...';

    // AJAX банд кардан ба муддати 15 дақиқа
    const formData = new FormData();
    formData.append('seat_id', seatId);
    formData.append('csrf_token', csrfToken);

    fetch('/booking/hold', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Гузариш ба саҳифаи пардохт бо таймер
            window.location.href = '/booking/checkout/' + seatId;
        } else {
            alert(data.message || 'Хатогӣ ҳангоми банд кардан.');
            location.reload();
        }
    })
    .catch(err => {
        alert('Хатогии шабака. Лутфан дубора кӯшиш кунед.');
        location.reload();
    });
});
</script>

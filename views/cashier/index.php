<?php
use App\Core\Csrf;
?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-warning text-dark fs-6 px-3 py-1 mb-1 fw-bold">РЕЖИМИ КАССИР</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Терминали кассири вокзал</h3>
        </div>
        <div>
            <a href="/cashier/lookup" class="btn btn-outline-danger fw-bold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Баргардонидани чипта (Refund)
            </a>
        </div>
    </div>

    <!-- Корти фурӯши фаврӣ дар касса -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cart-plus-fill me-2 text-success"></i> Фурӯши чипта дар касса (Пули нақдӣ)
                </h5>

                <form action="/cashier/fast-sale" method="POST">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Қатнавро интихоб кунед:</label>
                        <select id="cashierTripSelect" class="form-select form-select-lg" onchange="onCashierTripChange(this.value)">
                            <option value="">Интихоби қатора...</option>
                            <?php foreach ($trips as $tr): ?>
                                <option value="<?= $tr['id'] ?>">
                                    Қатораи №<?= htmlspecialchars($tr['train_number']) ?>: <?= htmlspecialchars($tr['route_name']) ?> (<?= date('d.m.Y H:i', strtotime($tr['departure_time'])) ?>) — Асосӣ: <?= $tr['base_price'] ?> сом.
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Рақами ID-и ҷой (Seat ID):</label>
                            <input type="number" name="seat_id" id="cashierSeatId" class="form-control" required placeholder="Рақами ID-и ҷой">
                            <div class="form-text">Ё аз нақшаи вагон интихоб кунед.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <a id="btnOpenSeatMap" href="#" target="_blank" class="btn btn-outline-primary w-100 disabled">
                                <i class="bi bi-grid-3x3 me-1"></i> Кушодани нақшаи вагон
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ФИО-и мусофир:</label>
                        <input type="text" name="passenger_name" class="form-control" required placeholder="Ному насаби мусофир">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Шиноснома / Ҳуҷҷат:</label>
                        <input type="text" name="passenger_doc" class="form-control" required placeholder="Рақами шиноснома ё шаҳодатнома">
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow">
                        <i class="bi bi-printer-fill me-2"></i> Фурӯши фаврӣ ва чопи чипта
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-clock-history me-2 text-primary"></i> Охирин чиптаҳои додашуда
                </h5>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentTickets as $t): ?>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-primary"><?= htmlspecialchars($t['ticket_number']) ?></span>
                                <div class="small text-muted"><?= htmlspecialchars($t['passenger_name']) ?> | Ҷойи <?= $t['seat_number'] ?></div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success"><?= $t['price'] ?> сом.</span>
                                <a href="/tickets/view/<?= $t['id'] ?>?print=1" target="_blank" class="btn btn-sm btn-link p-0 d-block">
                                    <i class="bi bi-printer"></i> чоп
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function onCashierTripChange(tripId) {
    const btn = document.getElementById('btnOpenSeatMap');
    if (tripId) {
        btn.classList.remove('disabled');
        btn.href = '/trips/' + tripId;
    } else {
        btn.classList.add('disabled');
        btn.href = '#';
    }
}
</script>

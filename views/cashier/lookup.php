<?php
use App\Core\Csrf;
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-search me-2 text-primary"></i> Ҷустуҷӯ ва баргардонидани чипта
                </h3>
                <a href="/cashier" class="btn btn-outline-secondary">← Бозгашт ба касса</a>
            </div>

            <!-- Шакли ҷустуҷӯи рақами чипта -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <form action="/cashier/lookup" method="GET" class="row g-2">
                    <div class="col-md-9">
                        <label class="form-label fw-bold">Рақами чипта ё рамзи QR:</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-qr-code-scan"></i></span>
                            <input type="text" name="ticket_number" class="form-control" required 
                                   placeholder="Масалан: TKT-2026-0001" 
                                   value="<?= htmlspecialchars($ticketNumber ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-search me-1"></i> Ёфтан
                        </button>
                    </div>
                </form>
            </div>

            <?php if (!empty($ticketNumber) && !$ticket): ?>
                <div class="alert alert-danger rounded-4 p-4 text-center">
                    <i class="bi bi-exclamation-octagon display-4 d-block mb-2"></i>
                    <h5 class="fw-bold">Чипта бо рақами «<?= htmlspecialchars($ticketNumber) ?>» ёфт нашуд!</h5>
                    <p class="mb-0">Лутфан рақамро дуруст тафтиш кунед.</p>
                </div>
            <?php elseif ($ticket): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <span class="badge bg-danger fs-6 px-3 py-1">№ <?= htmlspecialchars($ticket['ticket_number']) ?></span>
                            <h4 class="fw-bold mt-2 mb-0"><?= htmlspecialchars($ticket['route_name']) ?></h4>
                        </div>
                        <div class="text-end">
                            <?php if ($ticket['status'] === 'refunded'): ?>
                                <span class="badge bg-secondary fs-6 px-3 py-2">АЛЛАКАЙ БАРГАРДОНИДА ШУД</span>
                            <?php else: ?>
                                <span class="badge bg-success fs-6 px-3 py-2">ЧИПТАИ ФАЪОЛ</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="small text-muted">Мусофир:</div>
                            <strong class="fs-5"><?= htmlspecialchars($ticket['passenger_name']) ?></strong>
                            <div class="small text-muted">Шиноснома: <?= htmlspecialchars($ticket['passenger_doc']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Қатора ва ҷой:</div>
                            <div>Қатора: <strong>№ <?= htmlspecialchars($ticket['train_number']) ?></strong></div>
                            <div>Вагон: <strong>№ <?= htmlspecialchars($ticket['car_number']) ?> (<?= ($ticket['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)</strong></div>
                            <div>Ҷой: <strong>№ <?= htmlspecialchars($ticket['seat_number']) ?></strong></div>
                        </div>
                        <div class="col-12 border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 text-muted">Маблағи пардохтшуда:</span>
                                <span class="fs-3 fw-bold text-success"><?= htmlspecialchars($ticket['price']) ?> сомонӣ</span>
                            </div>
                        </div>
                    </div>

                    <?php if ($ticket['status'] !== 'refunded'): ?>
                        <form action="/cashier/refund" method="POST" onsubmit="return confirm('Шумо мутмаин ҳастед, ки ин чиптаро баргардонидан мехоҳед? Маблағ баргардонда шуда, ҷой озод мегардад.');">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-lg w-100 fw-bold py-3 shadow">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Баргардонидани чипта (Refund) ва озод кардани ҷой
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center mb-0">
                            Ин чипта дар санаи <?= date('d.m.Y H:i', strtotime($ticket['refunded_at'] ?? 'now')) ?> баргардонида шудааст.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

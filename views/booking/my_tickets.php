<?php
use App\Core\Csrf;
?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-ticket-detailed me-2 text-primary"></i>Чиптаҳои ман</h3>
            <p class="text-muted small mb-0">Таърихи хариди чиптаҳо ва имкони чопи PDF ё баргардонидани онҳо</p>
        </div>
        <a href="/" class="btn btn-warning fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Хариди чиптаи нав
        </a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
            <div class="card-body">
                <i class="bi bi-ticket-perforated text-muted display-3 mb-3"></i>
                <h4 class="fw-bold">Шумо ҳоло ягон чипта нахаридаед</h4>
                <p class="text-muted">Барои хариди чипта ба саҳифаи асосӣ гузаред ва қатораи дилхоҳатонро интихоб намоед.</p>
                <a href="/" class="btn btn-primary mt-2">Ҷустуҷӯи қатора</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($tickets as $tkt): ?>
                <?php
                    $isRefunded = ($tkt['status'] === 'refunded');
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white <?= $isRefunded ? 'opacity-75' : '' ?>">
                        <div class="card-body p-2">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <span class="badge <?= $isRefunded ? 'bg-secondary' : 'bg-danger' ?> fs-6 mb-2">
                                        № <?= htmlspecialchars($tkt['ticket_number']) ?>
                                    </span>
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($tkt['route_name']) ?></h5>
                                    <div class="small text-muted">
                                        Қатора: № <?= htmlspecialchars($tkt['train_number']) ?> | Вагон: <?= htmlspecialchars($tkt['car_number']) ?> | Ҷой: <?= htmlspecialchars($tkt['seat_number']) ?>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="small text-muted">Равонашавӣ:</div>
                                    <div class="fw-bold"><?= date('d.m.Y H:i', strtotime($tkt['departure_time'])) ?></div>
                                    <div class="small text-primary"><?= htmlspecialchars($tkt['origin_station']) ?></div>
                                </div>

                                <div class="col-md-2">
                                    <div class="small text-muted">Мусофир:</div>
                                    <div class="fw-bold"><?= htmlspecialchars($tkt['passenger_name']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($tkt['passenger_doc']) ?></div>
                                </div>

                                <div class="col-md-2 text-md-center">
                                    <div class="small text-muted">Нарх:</div>
                                    <div class="fs-4 fw-bold text-success"><?= htmlspecialchars($tkt['price']) ?> сом.</div>
                                    <div>
                                        <?php if ($isRefunded): ?>
                                            <span class="badge bg-secondary">Баргардонида шуд</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Фаъол</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                    <div class="d-flex flex-column gap-2">
                                        <a href="/tickets/view/<?= $tkt['id'] ?>?print=1" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-printer me-1"></i> Чопи PDF
                                        </a>

                                        <?php if (!$isRefunded): ?>
                                            <form action="/tickets/refund/<?= $tkt['id'] ?>" method="POST" onsubmit="return confirm('Оё шумо дар ҳақиқат мехоҳед ин чиптаро баргардонед? Ҷойи шумо дар вагон озод мешавад.');">
                                                <?= Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Баргардонидан
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

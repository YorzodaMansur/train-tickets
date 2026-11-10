<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-bold mb-2">
                    Қатораи № <?= htmlspecialchars($trip['train_number']) ?>
                </span>
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($trip['route_name']) ?></h3>
                <div class="text-muted">
                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($trip['origin_name']) ?> → <?= htmlspecialchars($trip['dest_name']) ?>
                </div>
            </div>
            <div class="text-end">
                <div class="small text-muted">Санаи равонашавӣ:</div>
                <div class="fs-4 fw-bold text-primary"><?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?></div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-truck-front me-2"></i>Вагонҳои ин қатораро интихоб кунед:</h5>

        <div class="row g-3">
            <?php foreach ($cars as $car): ?>
                <?php
                    $isCoupe = ($car['car_type'] === 'coupe');
                    $typeName = $isCoupe ? 'Купе (Бароҳат)' : 'Плацкарт (Умумӣ)';
                    $typeIcon = $isCoupe ? 'bi-door-closed-fill text-warning' : 'bi-people-fill text-info';
                    $carPrice = round($trip['base_price'] * $car['price_modifier'], 2);
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary fs-6 px-3 py-1">Вагони № <?= $car['car_number'] ?></span>
                            <span class="fw-bold text-success fs-5"><?= $carPrice ?> сомонӣ</span>
                        </div>
                        <h5 class="fw-bold mb-2">
                            <i class="bi <?= $typeIcon ?> me-1"></i> <?= $typeName ?>
                        </h5>
                        <p class="text-muted small mb-3">
                            <?= $isCoupe 
                                ? 'Ҳуҷраҳои чорҷояи пӯшида бо дари қулфшаванда, кондитсионер ва рӯйҷойи тоза.' 
                                : 'Вагони кушодаи дорои 54 ҷойи хоб (36 ҷойи купеӣ ва 18 ҷойи паҳлуӣ).' ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle me-1"></i> <?= $car['available_seats'] ?> ҷойи холӣ
                            </span>
                            <a href="/booking/seat-map/<?= $trip['id'] ?>/<?= $car['id'] ?>" class="btn btn-warning fw-bold px-3">
                                Дидани нақшаи ҷойҳо <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

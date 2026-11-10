<div class="container my-4">
    <!-- Сатри ҷустуҷӯи дубора -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form action="/search" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Аз истгоҳи:</label>
                <select name="origin_id" class="form-select" required>
                    <?php foreach ($stations as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= ($st['id'] == $originId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['name_tj']) ?> (<?= htmlspecialchars($st['city']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Ба истгоҳи:</label>
                <select name="destination_id" class="form-select" required>
                    <?php foreach ($stations as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= ($st['id'] == $destinationId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['name_tj']) ?> (<?= htmlspecialchars($st['city']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Сана:</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date ?? date('Y-m-d', strtotime('+1 day'))) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-search me-1"></i> Ҷустуҷӯ
                </button>
            </div>
        </form>
    </div>

    <!-- Натиҷаҳои ҷустуҷӯ -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-train-front text-primary me-2"></i>Қатнавҳои ёфтшуда:
        </h4>
        <span class="badge bg-primary px-3 py-2"><?= count($trips) ?> қатора дастрас аст</span>
    </div>

    <?php if (empty($trips)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-calendar-x text-muted display-3 mb-3"></i>
                <h4 class="fw-bold">Барои ин самт ва сана қатора ёфт нашуд</h4>
                <p class="text-muted">Лутфан санаи дигар ё самтҳои дигарро интихоб кунед (масалан, Хуҷанд → Душанбе-1).</p>
                <a href="/" class="btn btn-outline-primary mt-2">Ба саҳифаи асосӣ</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($trips as $trip): ?>
                <?php
                    $depTime = strtotime($trip['departure_time']);
                    $arrTime = strtotime($trip['arrival_time']);
                    $durationMins = round(($arrTime - $depTime) / 60);
                    $durHours = floor($durationMins / 60);
                    $durRemMins = $durationMins % 60;
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden trip-card hover-shadow transition">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- Рақами қатора ва хатсайр -->
                                <div class="col-md-3 mb-3 mb-md-0 border-end-md">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-bold me-2">
                                            № <?= htmlspecialchars($trip['train_number']) ?>
                                        </span>
                                        <span class="text-muted small">Қатораи баландсуръат</span>
                                    </div>
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($trip['route_name']) ?></h5>
                                    <div class="small text-muted">
                                        <i class="bi bi-speedometer me-1"></i> Масофа: <?= htmlspecialchars($trip['distance_km']) ?> км
                                    </div>
                                </div>

                                <!-- Вақти сафар ва график -->
                                <div class="col-md-5 mb-3 mb-md-0">
                                    <div class="d-flex justify-content-between align-items-center text-center">
                                        <div class="text-start">
                                            <div class="fs-4 fw-bold text-dark"><?= date('H:i', $depTime) ?></div>
                                            <div class="fw-semibold text-primary"><?= htmlspecialchars($trip['origin_name']) ?></div>
                                            <div class="small text-muted"><?= date('d.m.Y', $depTime) ?></div>
                                        </div>

                                        <div class="flex-grow-1 px-3">
                                            <div class="small text-muted mb-1"><?= $durHours ?> соат <?= $durRemMins ?> дақ.</div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                            </div>
                                            <div class="small text-muted mt-1"><i class="bi bi-arrow-right"></i> Бе таваққуф</div>
                                        </div>

                                        <div class="text-end">
                                            <div class="fs-4 fw-bold text-dark"><?= date('H:i', $arrTime) ?></div>
                                            <div class="fw-semibold text-success"><?= htmlspecialchars($trip['dest_name']) ?></div>
                                            <div class="small text-muted"><?= date('d.m.Y', $arrTime) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Нарх ва ҷойҳои холӣ -->
                                <div class="col-md-4 text-md-end">
                                    <div class="mb-2">
                                        <span class="text-muted small">Нархи як ҷой аз:</span>
                                        <div class="fs-3 fw-bold text-primary">
                                            <?= htmlspecialchars($trip['base_price']) ?> <span class="fs-6 text-muted">сомонӣ</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <?php if ($trip['available_seats'] > 0): ?>
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="bi bi-check2-circle me-1"></i> <?= $trip['available_seats'] ?> ҷойи холӣ
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i> Ҷойҳо тамом шуданд
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/trips/<?= $trip['trip_id'] ?>" class="btn btn-warning fw-bold px-4 py-2 shadow-sm">
                                        Интихоби ҷой дар вагон <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

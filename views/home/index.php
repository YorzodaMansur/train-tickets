<div class="container my-4">
    <!-- Hero Banner бо ҷустуҷӯи қатора -->
    <div class="card border-0 shadow-lg text-white mb-5 hero-card overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Хизматрасонии расмии онлайн
                    </span>
                    <h1 class="display-5 fw-bold mb-3">Фурӯши онлайни чиптаҳо барои қатораҳои байнишаҳрӣ</h1>
                    <p class="lead text-light mb-4">
                        Ҷойи дилхоҳи худро дар нақшаи вагон интихоб кунед, ба муддати <strong>15 дақиқа</strong> банд намоед ва чиптаро дар PDF бо рамзи QR чоп кунед!
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="text-white-50 small me-2 align-self-center">Самтҳои маъмул:</span>
                        <a href="/search?origin_id=1&destination_id=2" class="btn btn-sm btn-outline-light rounded-pill">Хуҷанд → Душанбе</a>
                        <a href="/search?origin_id=2&destination_id=1" class="btn btn-sm btn-outline-light rounded-pill">Душанбе → Хуҷанд</a>
                        <a href="/search?origin_id=2&destination_id=4" class="btn btn-sm btn-outline-light rounded-pill">Душанбе → Кӯлоб</a>
                    </div>
                </div>

                <!-- Шакли ҷустуҷӯи қатнавҳо -->
                <div class="col-lg-6">
                    <div class="card bg-white text-dark shadow-sm border-0 rounded-4 p-3 p-md-4">
                        <h4 class="fw-bold mb-3 text-primary d-flex align-items-center">
                            <i class="bi bi-search me-2"></i> Ҷустуҷӯи қатора
                        </h4>
                        <form action="/search" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Аз куҷо (Истгоҳи оғоз):
                                    </label>
                                    <select name="origin_id" class="form-select form-select-lg" required>
                                        <option value="">Интихоб кунед...</option>
                                        <?php foreach ($stations as $st): ?>
                                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 1) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($st['name_tj']) ?> (<?= htmlspecialchars($st['city']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">
                                        <i class="bi bi-geo-fill text-success me-1"></i> Ба куҷо (Истгоҳи анҷом):
                                    </label>
                                    <select name="destination_id" class="form-select form-select-lg" required>
                                        <option value="">Интихоб кунед...</option>
                                        <?php foreach ($stations as $st): ?>
                                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 2) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($st['name_tj']) ?> (<?= htmlspecialchars($st['city']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">
                                        <i class="bi bi-calendar3 me-1"></i> Санаи сафар:
                                    </label>
                                    <input type="date" name="date" class="form-control form-control-lg" 
                                           value="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                           min="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm">
                                        <i class="bi bi-train-front me-2"></i> Ёфтани қатораҳо
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Афзалиятҳои низом (Талаботи техникӣ) -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 feature-box">
                <div class="feature-icon bg-primary text-white mx-auto mb-3">
                    <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Нақшаи вагон</h5>
                <p class="text-muted small">Интихоби ҷойҳои болоӣ ва поёнӣ дар купе ва плацкарт бо рангҳои аён.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 feature-box">
                <div class="feature-icon bg-warning text-dark mx-auto mb-3">
                    <i class="bi bi-stopwatch-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Банди 15-дақиқагӣ</h5>
                <p class="text-muted small">Ҷойи шумо барои 15 дақиқа масдуд мешавад то ки каси дигар онро нахарад.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 feature-box">
                <div class="feature-icon bg-success text-white mx-auto mb-3">
                    <i class="bi bi-file-earmark-pdf-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Чиптаи PDF бо QR</h5>
                <p class="text-muted small">Дарҳол чиптаи расмиро дар формати PDF гиред ва чоп кунед.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 feature-box">
                <div class="feature-icon bg-info text-white mx-auto mb-3">
                    <i class="bi bi-arrow-repeat fs-3"></i>
                </div>
                <h5 class="fw-bold">Баргардонидани чипта</h5>
                <p class="text-muted small">Имкони баргардонидани осони чипта дар касса ё онлайн бо озод шудани фаврии ҷой.</p>
            </div>
        </div>
    </div>

    <!-- Ҷадвали қатораҳои фаъол -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-clock-history me-2 text-primary"></i> Қатнавҳои наздиктарин
            </h5>
            <span class="badge bg-light text-dark border">Ҳамагӣ: <?= count($activeTrips) ?> қатнав</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Қатора №</th>
                        <th>Хатсайр</th>
                        <th>Вақти равонашавӣ</th>
                        <th>Вақти расидан</th>
                        <th>Нарх аз</th>
                        <th>Ҳолат</th>
                        <th>Амал</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activeTrips)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Қатнавҳои фаъол ёфт нашуданд.</td></tr>
                    <?php else: ?>
                        <?php foreach ($activeTrips as $trip): ?>
                            <tr>
                                <td class="fw-bold text-primary">
                                    <i class="bi bi-train-front me-1"></i> <?= htmlspecialchars($trip['train_number']) ?>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= htmlspecialchars($trip['route_name']) ?></span>
                                    <div class="small text-muted"><?= htmlspecialchars($trip['origin_name']) ?> → <?= htmlspecialchars($trip['dest_name']) ?></div>
                                </td>
                                <td>
                                    <strong><?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars($trip['origin_name']) ?></div>
                                </td>
                                <td>
                                    <strong><?= date('d.m.Y H:i', strtotime($trip['arrival_time'])) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars($trip['dest_name']) ?></div>
                                </td>
                                <td class="fw-bold text-success">
                                    <?= htmlspecialchars($trip['base_price']) ?> сомонӣ
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        Ба нақша гирифта шудааст
                                    </span>
                                </td>
                                <td>
                                    <a href="/trips/<?= $trip['id'] ?>" class="btn btn-sm btn-primary">
                                        Интихоби вагон <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

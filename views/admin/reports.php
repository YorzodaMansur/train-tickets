<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1 mb-1 fw-bold">ТАҲЛИЛИ МОЛИЯВӢ</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph-fill me-2 text-success"></i>Ҳисобот оид ба фурӯш ва даромад</h3>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Чопи ҳисобот
            </button>
            <a href="/admin" class="btn btn-primary">← Бозгашт ба панел</a>
        </div>
    </div>

    <!-- Сводкаи умумӣ -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="small text-muted text-uppercase fw-bold">Маблағи умумии воридшуда</div>
                <div class="display-6 fw-bold text-success my-2"><?= number_format($kpi['total_revenue'], 2) ?> сомонӣ</div>
                <div class="small text-muted">Аз фурӯши <?= $kpi['tickets_sold'] ?> чипта</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="small text-muted text-uppercase fw-bold">Маблағи баргардонидашуда (Refund)</div>
                <div class="display-6 fw-bold text-danger my-2"><?= number_format($kpi['refunded_amount'], 2) ?> сомонӣ</div>
                <div class="small text-muted"><?= $kpi['refunds_count'] ?> чипта бекор карда шуд</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="small text-muted text-uppercase fw-bold">Даромади холис (Net Revenue)</div>
                <div class="display-6 fw-bold text-primary my-2"><?= number_format($kpi['total_revenue'] - $kpi['refunded_amount'], 2) ?> сомонӣ</div>
                <div class="small text-muted">Бо дарназардошти баргардониҳо</div>
            </div>
        </div>
    </div>

    <!-- Ҷадвали муфассали фурӯш аз рӯи хатсайрҳо -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Ҳисоботи тақсимоти фурӯш аз рӯи самтҳо ва каналҳо</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Хатсайр</th>
                        <th class="text-center">Чиптаҳои фурӯхташуда</th>
                        <th class="text-center">Онлайн (вебсайт)</th>
                        <th class="text-center">Кассаи вокзал</th>
                        <th class="text-center">Баргардонида шуд</th>
                        <th class="text-end">Даромад (сомонӣ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $totalTickets = 0;
                        $totalRev = 0;
                        foreach ($salesReport as $row): 
                            $totalTickets += $row['total_tickets'];
                            $totalRev += $row['total_revenue'];
                    ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row['route_name']) ?></td>
                            <td class="text-center"><span class="badge bg-primary fs-6"><?= $row['total_tickets'] ?></span></td>
                            <td class="text-center text-primary fw-bold"><?= $row['online_sales'] ?></td>
                            <td class="text-center text-success fw-bold"><?= $row['cashier_sales'] ?></td>
                            <td class="text-center text-danger fw-bold"><?= $row['refunds_count'] ?></td>
                            <td class="text-end fw-bold text-success fs-5"><?= number_format($row['total_revenue'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>ҶАМЪБАСТ:</td>
                        <td class="text-center"><?= $totalTickets ?></td>
                        <td colspan="3"></td>
                        <td class="text-end text-success fs-5"><?= number_format($totalRev, 2) ?> сомонӣ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Таърихи охирин амалиётҳои чиптафурӯшӣ -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Охирин 50 амалиёти бақайдгирифташуда</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>№ Чипта</th>
                        <th>Қатора ва самт</th>
                        <th>Мусофир</th>
                        <th>Вагон / Ҷой</th>
                        <th>Нарх</th>
                        <th>Канал</th>
                        <th>Ҳолат</th>
                        <th>Санаи сабт</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTickets as $t): ?>
                        <tr>
                            <td class="fw-bold"><a href="/tickets/view/<?= $t['id'] ?>" target="_blank"><?= htmlspecialchars($t['ticket_number']) ?></a></td>
                            <td><?= htmlspecialchars($t['train_number']) ?> (<?= htmlspecialchars($t['route_name']) ?>)</td>
                            <td><?= htmlspecialchars($t['passenger_name']) ?></td>
                            <td>Вагони <?= $t['car_number'] ?>, Ҷойи <?= $t['seat_number'] ?></td>
                            <td class="fw-bold"><?= $t['price'] ?> сом.</td>
                            <td>
                                <span class="badge <?= ($t['purchase_channel'] === 'online') ? 'bg-info text-dark' : 'bg-warning text-dark' ?>">
                                    <?= htmlspecialchars($t['purchase_channel']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= ($t['status'] === 'paid') ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= htmlspecialchars($t['status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('d.m.Y H:i', strtotime($t['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1 mb-1 fw-bold">ПАНЕЛИ МАЪМУР</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Панели идоракунӣ ва омори фурӯш</h3>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/trips" class="btn btn-warning fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Қатнави нав
            </a>
            <a href="/admin/reports" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Ҳисоботи пурра
            </a>
        </div>
    </div>

    <!-- Кортҳои нишондиҳандаҳои асосӣ (KPI) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Маблағи фурӯш (Даромад)</div>
                        <div class="fs-3 fw-bold text-success"><?= number_format($kpi['total_revenue'], 2) ?> сом.</div>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Чиптаҳои фурӯхташуда</div>
                        <div class="fs-3 fw-bold text-primary"><?= $kpi['tickets_sold'] ?> адад</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-ticket-perforated-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Баргардонида шуд (Refund)</div>
                        <div class="fs-3 fw-bold text-danger"><?= $kpi['refunds_count'] ?> адад</div>
                    </div>
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                        <i class="bi bi-arrow-counterclockwise fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Қатнавҳои фаъол</div>
                        <div class="fs-3 fw-bold text-warning"><?= $kpi['active_trips'] ?> қатора</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-train-front fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ҷадвали омори фурӯш аз рӯи хатсайрҳо -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">
            <i class="bi bi-graph-up me-2 text-primary"></i> Фурӯш ва даромад аз рӯи хатсайрҳо
        </h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Номи хатсайр</th>
                        <th>Чиптаҳои фурӯхташуда</th>
                        <th>Фурӯши онлайнӣ</th>
                        <th>Фурӯши кассавӣ</th>
                        <th>Баргардонида шуд</th>
                        <th>Маблағи умумӣ (сомонӣ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salesReport)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">Маълумоти фурӯш ёфт нашуд.</td></tr>
                    <?php else: ?>
                        <?php foreach ($salesReport as $row): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($row['route_name']) ?></td>
                                <td><span class="badge bg-primary fs-6"><?= $row['total_tickets'] ?></span></td>
                                <td><?= $row['online_sales'] ?></td>
                                <td><?= $row['cashier_sales'] ?></td>
                                <td><span class="text-danger fw-bold"><?= $row['refunds_count'] ?></span></td>
                                <td class="fw-bold text-success fs-5"><?= number_format($row['total_revenue'], 2) ?> сом.</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

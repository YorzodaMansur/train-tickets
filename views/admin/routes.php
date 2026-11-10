<?php use App\Core\Csrf; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1 mb-1 fw-bold">СПРАВОЧНИК</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-signpost-split-fill me-2 text-primary"></i>Хатсайрҳои байнишаҳрӣ</h3>
        </div>
        <a href="/admin" class="btn btn-outline-secondary">← Бозгашт ба панел</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Иловаи хатсайри нав</h5>
                <form action="/admin/routes/add" method="POST">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Рамзи хатсайр:</label>
                        <input type="text" name="code" class="form-control" required placeholder="Масалан: RT-06">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Номи хатсайр:</label>
                        <input type="text" name="name" class="form-control" required placeholder="Масалан: Душанбе — Истиқлол">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Истгоҳи оғоз:</label>
                        <select name="origin_station_id" class="form-select" required>
                            <?php foreach ($stations as $st): ?>
                                <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name_tj']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Истгоҳи анҷом:</label>
                        <select name="destination_station_id" class="form-select" required>
                            <?php foreach ($stations as $st): ?>
                                <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name_tj']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Масофа (км):</label>
                            <input type="number" step="0.1" name="distance_km" class="form-control" value="100">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Вақт (дақиқа):</label>
                            <input type="number" name="estimated_minutes" class="form-control" value="120">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Сабти хатсайр
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Рӯйхати хатсайрҳо</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Рамз</th>
                                <th>Ном</th>
                                <th>Самт</th>
                                <th>Масофа</th>
                                <th>Вақт</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routes as $r): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($r['code']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($r['name']) ?></td>
                                    <td><?= htmlspecialchars($r['origin_name']) ?> → <?= htmlspecialchars($r['dest_name']) ?></td>
                                    <td><?= htmlspecialchars($r['distance_km']) ?> км</td>
                                    <td><?= htmlspecialchars($r['estimated_minutes']) ?> дақ.</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

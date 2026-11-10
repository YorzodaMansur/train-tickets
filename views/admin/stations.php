<?php use App\Core\Csrf; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1 mb-1 fw-bold">СПРАВОЧНИК</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Истгоҳҳо ва вокзалҳо</h3>
        </div>
        <a href="/admin" class="btn btn-outline-secondary">← Бозгашт ба панел</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Иловаи истгоҳи нав</h5>
                <form action="/admin/stations/add" method="POST">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Рамзи кӯтоҳ (Code):</label>
                        <input type="text" name="code" class="form-control" required placeholder="Масалан: DSH, KHJ" maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ном (Тоҷикӣ):</label>
                        <input type="text" name="name_tj" class="form-control" required placeholder="Масалан: Душанбе-1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ном (Русӣ):</label>
                        <input type="text" name="name_ru" class="form-control" placeholder="Душанбе-1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Шаҳр:</label>
                        <input type="text" name="city" class="form-control" required placeholder="Душанбе">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Сабт кардан
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Рӯйхати истгоҳҳои мавҷуда</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Рамз</th>
                                <th>Номи истгоҳ</th>
                                <th>Шаҳр</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stations as $st): ?>
                                <tr>
                                    <td><?= $st['id'] ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($st['code']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($st['name_tj']) ?></td>
                                    <td><?= htmlspecialchars($st['city']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

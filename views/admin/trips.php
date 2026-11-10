<?php use App\Core\Csrf; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1 mb-1 fw-bold">ҶАДВАЛИ ҲАРАКАТ</span>
            <h3 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2 text-warning"></i>Қатнавҳо ва қатораҳо</h3>
        </div>
        <a href="/admin" class="btn btn-outline-secondary">← Бозгашт ба панел</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Иловаи қатнави нав</h5>
                <form action="/admin/trips/add" method="POST">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Рақами қатора:</label>
                        <input type="text" name="train_number" class="form-control" required placeholder="Масалан: 029-ХД">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Хатсайр:</label>
                        <select name="route_id" class="form-select" required>
                            <?php foreach ($routes as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Вақти равонашавӣ:</label>
                        <input type="datetime-local" name="departure_time" class="form-control" required 
                               value="<?= date('Y-m-d\T08:00', strtotime('+3 days')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Вақти расидан:</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" required 
                               value="<?= date('Y-m-d\T14:00', strtotime('+3 days')) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Нархи асосӣ (сомонӣ):</label>
                        <input type="number" step="0.5" name="base_price" class="form-control" required value="70.00">
                        <div class="form-text small">Низом ба таври худкор 1 вагони купе ва 1 вагони плацкартро бо ҷойҳо месозад.</div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Сабти қатнав бо вагонҳо
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Рӯйхати қатораҳо дар ҷадвал</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Қатора</th>
                                <th>Хатсайр</th>
                                <th>Равонашавӣ</th>
                                <th>Расидан</th>
                                <th>Нархи асосӣ</th>
                                <th>Вагонҳо</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trips as $t): ?>
                                <tr>
                                    <td><span class="badge bg-primary fs-6">№ <?= htmlspecialchars($t['train_number']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($t['route_name']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($t['departure_time'])) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($t['arrival_time'])) ?></td>
                                    <td class="fw-bold text-success"><?= htmlspecialchars($t['base_price']) ?> сом.</td>
                                    <td>
                                        <a href="/trips/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <?= $t['cars_count'] ?> вагон
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

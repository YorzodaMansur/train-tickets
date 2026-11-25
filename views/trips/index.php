<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h2 mb-1">Ҷадвали сафарҳо</h1>
        <p class="text-secondary mb-0">Рӯйхати сафарҳои намунавӣ</p>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($trips as $trip): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between">
                    <span class="badge text-bg-primary"><?= e($trip['status']) ?></span>
                    <strong><?= e(number_format((float)$trip['base_price'], 2)) ?> сомонӣ</strong>
                </div>
                <h2 class="h5 mt-3"><?= e($trip['origin_station']) ?> → <?= e($trip['destination_station']) ?></h2>
                <p class="small text-secondary mb-1">Қатора: <?= e($trip['train_number']) ?></p>
                <p class="mb-1">Рафтан: <?= e($trip['departure_time']) ?></p>
                <p class="mb-3">Омадан: <?= e($trip['arrival_time']) ?></p>
                <a class="btn btn-outline-primary mt-auto" href="<?= e(url('/trips/' . $trip['id'])) ?>">Тафсилот</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

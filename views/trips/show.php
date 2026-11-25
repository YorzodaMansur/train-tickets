<a href="<?= e(url('/trips')) ?>" class="btn btn-outline-secondary mb-3">← Бозгашт ба сафарҳо</a>
<div class="card shadow-sm p-4">
    <h1 class="h3">Маълумоти сафар №<?= e($trip['id']) ?></h1>
    <h2 class="h4 mt-3"><?= e($trip['origin_station']) ?> → <?= e($trip['destination_station']) ?></h2>
    <table class="table table-bordered mt-3">
        <tbody>
            <tr><th>Рақами қатор</th><td><?= e($trip['train_number']) ?></td></tr>
            <tr><th>Вақти рафтан</th><td><?= e($trip['departure_time']) ?></td></tr>
            <tr><th>Вақти омадан</th><td><?= e($trip['arrival_time']) ?></td></tr>
            <tr><th>Нарх</th><td><?= e(number_format((float)$trip['base_price'], 2)) ?> сомонӣ</td></tr>
            <tr><th>Ҳолат</th><td><?= e($trip['status']) ?></td></tr>
        </tbody>
    </table>
</div>

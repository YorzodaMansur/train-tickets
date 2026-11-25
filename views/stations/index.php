<h1 class="h2 mb-3">Истгоҳҳои роҳи оҳан</h1>
<div class="card shadow-sm p-3">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead><tr><th>ID</th><th>Рамз</th><th>Ном (тоҷикӣ)</th><th>Ном (русӣ)</th><th>Шаҳр</th></tr></thead>
            <tbody>
            <?php foreach ($stations as $station): ?>
                <tr>
                    <td><?= e($station['id']) ?></td>
                    <td><?= e($station['code']) ?></td>
                    <td><?= e($station['name_tj']) ?></td>
                    <td><?= e($station['name_ru']) ?></td>
                    <td><?= e($station['city']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

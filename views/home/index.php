<section class="hero p-4 p-md-5 mb-4">
    <h1 class="display-6 fw-bold">Чиптаи қатораро онлайн интихоб кунед</h1>
    <p class="lead mb-0">Ҷустуҷӯи сафар, интихоби самт ва гирифтани маълумот дар бораи ҳаракати қатораҳо.</p>
    <a class="btn btn-light mt-3" href="<?= e(url('/trips')) ?>">Дидани сафарҳо</a>
</section>

<div class="row g-3">
    <?php foreach ($stats as $stat): ?>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 h-100">
                <div class="text-secondary"><?= e($stat['label']) ?></div>
                <div class="display-5 fw-bold"><?= e($stat['value']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm p-4 mt-4">
    <h2 class="h4">Дар бораи система</h2>
    <p class="mb-0">Ин веб-барнома барои намоиши самтҳо, ҷадвали сафарҳо ва маълумоти истгоҳҳо пешбинӣ шудааст. Додаҳои ин лаборатория муваққатан дар контроллерҳо нигоҳ дошта мешаванд.</p>
</div>

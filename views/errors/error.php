<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm p-4 text-center">
            <div class="display-3 fw-bold text-danger"><?= e($errorCode ?? 500) ?></div>
            <h1 class="h3">Хатогӣ рух дод</h1>
            <p class="text-secondary"><?= e($errorMessage ?? 'Дархост иҷро карда нашуд.') ?></p>
            <a class="btn btn-primary" href="<?= e(url('/')) ?>">Ба саҳифаи асосӣ</a>
        </div>
    </div>
</div>

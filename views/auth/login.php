<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm p-4">
            <h1 class="h3 mb-3">Воридшавӣ</h1>
            <p class="text-secondary">Ин саҳифа барои нишон додани шаблони воридшавӣ дар лабораторияи №12 сохта шудааст.</p>
            <form method="post" action="<?= e(url('/login')) ?>">
                <div class="mb-3">
                    <label class="form-label" for="username">Номи корбар ё почтаи электронӣ</label>
                    <input class="form-control" id="username" name="username" type="text" autocomplete="username">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Рамз</label>
                    <input class="form-control" id="password" name="password" type="password" autocomplete="current-password">
                </div>
                <button class="btn btn-primary" type="submit">Ворид шудан</button>
            </form>
            <small class="text-secondary mt-3">Қабули POST дар ин лаборатория амалӣ нашудааст; Router ҷавоби 405 медиҳад.</small>
        </div>
    </div>
</div>

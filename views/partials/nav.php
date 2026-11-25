<nav class="navbar navbar-expand-lg navbar-dark" style="background:#123b65">
    <div class="container">
        <a class="navbar-brand" href="<?= e(url('/')) ?>">🚆 Роҳи оҳани Тоҷикистон</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Кушодани меню">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/')) ?>">Асосӣ</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/trips')) ?>">Сафарҳо</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/stations')) ?>">Истгоҳҳо</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/login')) ?>">Воридшавӣ</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/register')) ?>">Бақайдгирӣ</a></li>
            </ul>
        </div>
    </div>
</nav>

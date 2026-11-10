<?php
use App\Core\Auth;
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Роҳи оҳани Тоҷикистон — Фурӯши чиптаҳо', ENT_QUOTES, 'UTF-8') ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- App Custom CSS -->
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Навбари асосӣ -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
                <i class="bi bi-train-front-fill me-2 fs-4 text-warning"></i>
                <span>Роҳи Оҳани Тоҷикистон</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' || str_starts_with($_SERVER['REQUEST_URI'], '/search')) ? 'active fw-bold' : '' ?>" href="/">
                            <i class="bi bi-search me-1"></i> Ҷустуҷӯи қатора
                        </a>
                    </li>
                    <?php if (Auth::check()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/my-tickets') ? 'active fw-bold' : '' ?>" href="/my-tickets">
                                <i class="bi bi-ticket-perforated me-1"></i> Чиптаҳои ман
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (Auth::hasRole('cashier', 'admin')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-cash-coin me-1"></i> Касса
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/cashier"><i class="bi bi-laptop me-2"></i> Терминали кассир</a></li>
                                <li><a class="dropdown-item" href="/cashier/lookup"><i class="bi bi-arrow-counterclockwise me-2"></i> Баргардонидани чипта</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (Auth::hasRole('admin')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-info fw-bold" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-gear-fill me-1"></i> Маъмурият
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i> Панели асосӣ</a></li>
                                <li><a class="dropdown-item" href="/admin/trips"><i class="bi bi-calendar-event me-2"></i> Қатнавҳо ва қатораҳо</a></li>
                                <li><a class="dropdown-item" href="/admin/routes"><i class="bi bi-signpost-split me-2"></i> Хатсайрҳо</a></li>
                                <li><a class="dropdown-item" href="/admin/stations"><i class="bi bi-geo-alt me-2"></i> Истгоҳҳо</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin/reports"><i class="bi bi-graph-up-arrow me-2"></i> Ҳисоботи фурӯш</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (Auth::check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm text-white px-3" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars(Auth::user()['full_name'] ?? Auth::user()['username']) ?>
                                <span class="badge bg-warning text-dark ms-1"><?= htmlspecialchars(Auth::role()) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="/my-tickets"><i class="bi bi-ticket-detailed me-2"></i> Чиптаҳои ман</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="/logout" method="POST" class="d-inline">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Баромад
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light btn-sm px-3" href="/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Воридшавӣ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm px-3 fw-bold" href="/register">
                                <i class="bi bi-person-plus me-1"></i> Бақайдгирӣ
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Огоҳиномаҳои Flash -->
    <div class="container mt-3">
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> <?= htmlspecialchars($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flashWarning)): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i> <?= htmlspecialchars($flashWarning) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Асосии саҳифа -->
    <main class="flex-grow-1 py-3">
        <?= $content ?>
    </main>

    <!-- Поварақ (Footer) -->
    <footer class="bg-dark text-light py-4 mt-auto border-top border-primary border-3">
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="fw-bold text-warning mb-1">
                        <i class="bi bi-train-front me-2"></i>КВД «Роҳи оҳани Тоҷикистон»
                    </h5>
                    <p class="small text-muted mb-0">Низоми ягонаи миллии фурӯши онлайни чиптаҳо барои қатораҳои байнишаҳрӣ.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-secondary me-2">PHP 8.2+ MVC</span>
                    <span class="badge bg-success me-2">15-min Lock Timer</span>
                    <span class="badge bg-info text-dark">PDF QR-Ticket</span>
                    <div class="small text-muted mt-2">© <?= date('Y') ?> Ҳамаи ҳуқуқҳо маҳфузанд. Муаллиф: Фарҳод Раҳимов</div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App Custom JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>

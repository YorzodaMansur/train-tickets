<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="tg">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($title ?? 'Система') ?> | Роҳи оҳани Тоҷикистон</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            color: #253247;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .hero {
            background: linear-gradient(120deg, #123b65, #2476a8);
            color: white;
            border-radius: 18px;
        }

        .card {
            border: 0;
            border-radius: 14px;
        }

        footer {
            color: #6c757d;
        }
    </style>
</head>

<body>

    <?php require __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-4">
        <?= $content ?? '' ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
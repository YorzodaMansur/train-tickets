<?php
$exception = $exception ?? null;

?>
<!DOCTYPE html>
<html lang="tg">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Хатои система</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .error-box {
            background: white;
            padding: 35px;
            border-radius: 12px;
            max-width: 650px;
            width: 90%;
            box-shadow: 0 2px 15px #00000012;
        }

        h1 {
            color: #c62828;
        }

        pre {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            background: #f3f3f3;
            padding: 15px;
            border-radius: 7px;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <h1>Хатои система</h1>

        <?php if (!empty($debug)): ?>
            <p>Маълумоти техникӣ барои таҳиягар:</p>

            <pre><?= htmlspecialchars(
                        (string)$exception,
                        ENT_QUOTES | ENT_SUBSTITUTE,
                        'UTF-8'
                    ) ?></pre>
        <?php else: ?>
            <p>
                Ҳангоми иҷрои дархост хатогӣ ба вуҷуд омад.
                Лутфан баъдтар кӯшиш кунед.
            </p>
        <?php endif; ?>

        <a href="index.php">Бозгашт ба саҳифаи асосӣ</a>
    </div>
</body>

</html>
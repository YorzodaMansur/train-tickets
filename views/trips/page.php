<?php

$data = $data ?? [];

$trips = $data['trips'] ?? [];
$trains = $data['trains'] ?? [];
$routes = $data['routes'] ?? [];

$errors = $data['errors'] ?? [];
$filters = $data['filters'] ?? [];
$pagination = $data['pagination'] ?? [];

$old = $data['old'] ?? [];





function e(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$form = $data['form'];
$errors = $data['errors'];
$isEdit = $data['edit_id'] !== null;

function fieldError(array $errors, string $field): void
{
    if (isset($errors[$field])) {
        echo '<div class="field-error">' . e($errors[$field]) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="tg">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Идоракунии рейсҳо</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #f4f6f9;
            color: #263238;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1250px;
            margin: auto;
        }

        h1,
        h2 {
            color: #173b63;
        }

        .card {
            background: white;
            padding: 22px;
            margin-bottom: 22px;
            border-radius: 12px;
            box-shadow: 0 2px 12px #0000000d;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccd3dc;
            border-radius: 7px;
            font-size: 14px;
        }

        button,
        .btn {
            display: inline-block;
            border: 0;
            padding: 10px 15px;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .primary {
            background: #1769aa;
            color: white;
        }

        .edit {
            background: #e9a323;
            color: #222;
        }

        .delete {
            background: #d94141;
            color: white;
        }

        .cancel {
            background: #e4e8ed;
            color: #222;
        }

        .field-error {
            color: #c62828;
            font-size: 13px;
            margin-top: 5px;
        }

        .alert {
            padding: 13px;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .success {
            background: #d9f4e1;
            color: #155d2b;
        }

        .error {
            background: #ffe0e0;
            color: #8c1c1c;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e6e9ed;
        }

        th {
            background: #edf2f7;
        }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .actions {
            display: flex;
            gap: 7px;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 8px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .pagination a {
            padding: 8px 12px;
            background: #edf2f7;
            color: #173b63;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination a.active {
            background: #1769aa;
            color: white;
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        @media(max-width: 750px) {
            body {
                padding: 12px;
            }

            .form-grid,
            .filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>Идоракунии рейсҳои қатора</h1>
        <p>Илова, таҳрир, ҷустуҷӯ ва идоракунии рейсҳо</p>

        <?php if ($data['flash']): ?>
            <div class="alert <?= e($data['flash']['type']) ?>">
                <?= e($data['flash']['message']) ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <h2><?= $isEdit ? 'Таҳрири рейс' : 'Иловаи рейси нав' ?></h2>

            <form method="POST" action="index.php">
                <input type="hidden" name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">

                <input type="hidden" name="action"
                    value="<?= $isEdit ? 'update' : 'create' ?>">

                <?php if ($isEdit): ?>
                    <input type="hidden" name="id"
                        value="<?= e($data['edit_id']) ?>">
                <?php endif; ?>

                <div class="form-grid">

                    <div>
                        <label for="train_id">Қатора</label>
                        <select id="train_id" name="train_id" required>
                            <option value="">Қатораро интихоб кунед</option>

                            <?php foreach ($data['trains'] as $train): ?>
                                <option value="<?= e($train['id']) ?>"
                                    <?= (string)$form['train_id'] ===
                                        (string)$train['id'] ? 'selected' : '' ?>>
                                    <?= e($train['train_number']) ?>
                                    — <?= e($train['train_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php fieldError($errors, 'train_id'); ?>
                    </div>

                    <div>
                        <label for="route_id">Хатсайр</label>
                        <select id="route_id" name="route_id" required>
                            <option value="">Хатсайрро интихоб кунед</option>

                            <?php foreach ($data['routes'] as $route): ?>
                                <option value="<?= e($route['id']) ?>"
                                    <?= (string)$form['route_id'] ===
                                        (string)$route['id'] ? 'selected' : '' ?>>
                                    <?= e($route['name']) ?>
                                    (<?= e($route['origin_name']) ?>
                                    — <?= e($route['destination_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php fieldError($errors, 'route_id'); ?>
                    </div>

                    <div>
                        <label for="departure_time">Вақти рафтан</label>
                        <input type="datetime-local"
                            id="departure_time"
                            name="departure_time"
                            value="<?= e($form['departure_time']) ?>"
                            required>
                        <?php fieldError($errors, 'departure_time'); ?>
                    </div>

                    <div>
                        <label for="arrival_time">Вақти омадан</label>
                        <input type="datetime-local"
                            id="arrival_time"
                            name="arrival_time"
                            value="<?= e($form['arrival_time']) ?>"
                            required>
                        <?php fieldError($errors, 'arrival_time'); ?>
                    </div>

                    <div>
                        <label for="status">Ҳолати рейс</label>
                        <select id="status" name="status" required>
                            <?php
                            $statuses = [
                                'scheduled' => 'Дар нақша',
                                'boarding' => 'Саворшавӣ',
                                'completed' => 'Анҷом ёфт',
                                'cancelled' => 'Бекор карда шуд',
                            ];
                            ?>

                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= e($key) ?>"
                                    <?= $form['status'] === $key
                                        ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php fieldError($errors, 'status'); ?>
                    </div>

                    <div>
                        <label for="base_price">Нархи чипта (сомонӣ)</label>
                        <input type="number"
                            id="base_price"
                            name="base_price"
                            min="0"
                            step="0.01"
                            value="<?= e($form['base_price']) ?>"
                            required>
                        <?php fieldError($errors, 'base_price'); ?>
                    </div>

                </div>

                <br>

                <button class="primary" type="submit">
                    <?= $isEdit ? 'Сабти тағйирот' : 'Илова кардани рейс' ?>
                </button>

                <?php if ($isEdit): ?>
                    <a class="btn cancel" href="index.php">Бекор кардан</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="card">
            <h2>Ҷустуҷӯ ва филтр</h2>

            <form method="GET" action="index.php" class="filters">
                <div>
                    <label for="search">Ҷустуҷӯ</label>
                    <input id="search" type="search" name="search"
                        placeholder="Рақами қатор, хатсайр ё истгоҳ..."
                        value="<?= e($data['search']) ?>">
                </div>

                <div>
                    <label for="filter_status">Ҳолат</label>
                    <select id="filter_status" name="status">
                        <option value="">Ҳама</option>
                        <option value="scheduled"
                            <?= $data['status'] === 'scheduled' ? 'selected' : '' ?>>
                            Дар нақша
                        </option>
                        <option value="boarding"
                            <?= $data['status'] === 'boarding' ? 'selected' : '' ?>>
                            Саворшавӣ
                        </option>
                        <option value="completed"
                            <?= $data['status'] === 'completed' ? 'selected' : '' ?>>
                            Анҷом ёфт
                        </option>
                        <option value="cancelled"
                            <?= $data['status'] === 'cancelled' ? 'selected' : '' ?>>
                            Бекоршуда
                        </option>
                    </select>
                </div>

                <div>
                    <label for="date_from">Аз сана</label>
                    <input id="date_from" type="date" name="date_from"
                        value="<?= e($data['date_from']) ?>">
                </div>

                <div>
                    <label for="date_to">То сана</label>
                    <input id="date_to" type="date" name="date_to"
                        value="<?= e($data['date_to']) ?>">
                </div>

                <div>
                    <button class="primary" type="submit">Ҷустуҷӯ</button>
                    <a class="btn cancel" href="index.php">Тоза</a>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Рӯйхати рейсҳо</h2>

            <p>Ҳамагӣ: <strong><?= e($data['total']) ?></strong> рейс</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Қатора</th>
                            <th>Хатсайр</th>
                            <th>Рафтан</th>
                            <th>Омадан</th>
                            <th>Ҳолат</th>
                            <th>Нарх</th>
                            <th>Амалиёт</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!$data['items']): ?>
                            <tr>
                                <td colspan="8">Рейс ёфт нашуд.</td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($data['items'] as $trip): ?>
                                <tr>
                                    <td><?= e($trip['id']) ?></td>

                                    <td>
                                        <?= e($trip['train_number']) ?>
                                        — <?= e($trip['train_name']) ?>
                                    </td>

                                    <td>
                                        <?= e($trip['origin_name']) ?>
                                        — <?= e($trip['destination_name']) ?>
                                    </td>

                                    <td><?= e($trip['departure_time']) ?></td>
                                    <td><?= e($trip['arrival_time']) ?></td>
                                    <td><?= e($statuses[$trip['status']] ?? $trip['status']) ?></td>
                                    <td><?= e($trip['base_price']) ?> сомонӣ</td>

                                    <td>
                                        <div class="actions">
                                            <a class="btn edit"
                                                href="index.php?action=edit&id=<?= e($trip['id']) ?>">
                                                Таҳрир
                                            </a>

                                            <form method="POST"
                                                action="index.php"
                                                onsubmit="return confirm('Рейсро нест мекунед?');">

                                                <input type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e($_SESSION['csrf_token']) ?>">

                                                <input type="hidden"
                                                    name="action"
                                                    value="delete">

                                                <input type="hidden"
                                                    name="id"
                                                    value="<?= e($trip['id']) ?>">

                                                <button class="delete" type="submit">
                                                    Нест кардан
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php
                $query = $_GET;
                unset($query['page']);
                ?>

                <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
                    <?php
                    $query['page'] = $i;
                    $url = 'index.php?' . http_build_query($query);
                    ?>

                    <a class="<?= $i === $data['page'] ? 'active' : '' ?>"
                        href="<?= e($url) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </section>

        <section class="card">
            <h2>Ҳисобот аз рӯи хатсайрҳо</h2>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Хатсайр</th>
                            <th>Шумораи рейсҳо</th>
                            <th>Ҷамъи нархҳои асосӣ</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['report'] as $row): ?>
                            <tr>
                                <td><?= e($row['route_name']) ?></td>
                                <td><?= e($row['trip_count']) ?></td>
                                <td><?= e($row['total_price']) ?> сомонӣ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</body>

</html>
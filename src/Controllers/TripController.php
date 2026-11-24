<?php

declare(strict_types=1);

class TripController
{
    private const PER_PAGE = 10;

    public function __construct(
        private TripRepository $trips,
        private TrainRepository $trains,
        private RouteRepository $routes
    ) {}

    public function handle(): array
    {
        $action = $_GET['action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handlePost();
        }

        $form = [
            'train_id' => '',
            'route_id' => '',
            'departure_time' => '',
            'arrival_time' => '',
            'status' => 'scheduled',
            'base_price' => '',
        ];

        $errors = [];
        $editId = null;

        if ($action === 'edit') {
            $editId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if (!$editId) {
                http_response_code(404);
                throw new RuntimeException('Рейс ёфт нашуд.');
            }

            $trip = $this->trips->find($editId);

            if (!$trip) {
                http_response_code(404);
                throw new RuntimeException('Рейс ёфт нашуд.');
            }

            $form = $trip;
            $form['departure_time'] = date(
                'Y-m-d\TH:i',
                strtotime($trip['departure_time'])
            );
            $form['arrival_time'] = date(
                'Y-m-d\TH:i',
                strtotime($trip['arrival_time'])
            );
        }

        $search = trim((string)($_GET['search'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));
        $dateFrom = trim((string)($_GET['date_from'] ?? ''));
        $dateTo = trim((string)($_GET['date_to'] ?? ''));

        if (!in_array($status, [
            '',
            'scheduled',
            'boarding',
            'completed',
            'cancelled',
        ], true)) {
            $status = '';
        }

        if (!$this->validDate($dateFrom)) {
            $dateFrom = '';
        }

        if (!$this->validDate($dateTo)) {
            $dateTo = '';
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $page = max(1, $page);

        $result = $this->trips->paginate(
            $search,
            $status,
            $dateFrom,
            $dateTo,
            $page,
            self::PER_PAGE
        );

        if ($page > $result['pages']) {
            $page = $result['pages'];

            $result = $this->trips->paginate(
                $search,
                $status,
                $dateFrom,
                $dateTo,
                $page,
                self::PER_PAGE
            );
        }

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'pages' => $result['pages'],
            'page' => $page,
            'search' => $search,
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'form' => $form,
            'errors' => $errors,
            'edit_id' => $editId,
            'trains' => $this->trains->all(),
            'routes' => $this->routes->all(),
            'report' => $this->trips->reportByRoute(),
            'flash' => $_SESSION['flash'] ?? null,
        ];
    }

    private function handlePost(): array
    {
        $token = (string)($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            throw new RuntimeException(
                'Дархости шумо тасдиқ нашуд. Саҳифаро нав кунед.'
            );
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'delete') {
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id) {
                throw new RuntimeException('ID-и рейс нодуруст аст.');
            }

            try {
                $this->trips->delete($id);

                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Рейс бомуваффақият нест карда шуд.',
                ];
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $_SESSION['flash'] = [
                        'type' => 'error',
                        'message' =>
                        'Ин рейсро нест кардан мумкин нест, зеро ба он чиптаҳо вобастаанд.',
                    ];
                } else {
                    throw $e;
                }
            }

            $this->redirect();
        }

        if (!in_array($action, ['create', 'update'], true)) {
            throw new RuntimeException('Амалиёт нодуруст аст.');
        }

        $data = [
            'train_id' => trim((string)($_POST['train_id'] ?? '')),
            'route_id' => trim((string)($_POST['route_id'] ?? '')),
            'departure_time' => trim(
                (string)($_POST['departure_time'] ?? '')
            ),
            'arrival_time' => trim(
                (string)($_POST['arrival_time'] ?? '')
            ),
            'status' => trim((string)($_POST['status'] ?? '')),
            'base_price' => trim((string)($_POST['base_price'] ?? '')),
        ];

        $id = null;

        if ($action === 'update') {
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || !$this->trips->find($id)) {
                throw new RuntimeException('Рейс ёфт нашуд.');
            }
        }

        $errors = Validator::validateTrip(
            $data,
            $this->trains,
            $this->routes,
            $this->trips,
            $id
        );

        if ($errors) {
            http_response_code(422);

            return $this->renderPostErrors($data, $errors, $id);
        }

        if ($action === 'create') {
            $this->trips->create($data);

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Рейси нав илова карда шуд.',
            ];
        } else {
            $this->trips->update($id, $data);

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Маълумоти рейс нав карда шуд.',
            ];
        }

        $this->redirect();
    }

    private function renderPostErrors(
        array $data,
        array $errors,
        ?int $id
    ): array {
        $result = $this->trips->paginate('', '', '', '', 1, self::PER_PAGE);

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'pages' => $result['pages'],
            'page' => 1,
            'search' => '',
            'status' => '',
            'date_from' => '',
            'date_to' => '',
            'form' => $data,
            'errors' => $errors,
            'edit_id' => $id,
            'trains' => $this->trains->all(),
            'routes' => $this->routes->all(),
            'report' => $this->trips->reportByRoute(),
            'flash' => null,
        ];
    }

    private function validDate(string $value): bool
    {
        if ($value === '') {
            return true;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function redirect(): never
    {
        header('Location: index.php');
        exit;
    }
}

<?php

declare(strict_types=1);

class Validator
{
    public static function validateTrip(
        array $data,
        TrainRepository $trains,
        RouteRepository $routes,
        TripRepository $trips,
        ?int $ignoreId = null
    ): array {
        $errors = [];

        $trainId = filter_var(
            $data['train_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $routeId = filter_var(
            $data['route_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $departure = trim((string)($data['departure_time'] ?? ''));
        $arrival = trim((string)($data['arrival_time'] ?? ''));
        $status = trim((string)($data['status'] ?? ''));
        $price = trim((string)($data['base_price'] ?? ''));

        if (!$trainId || !$trains->exists($trainId)) {
            $errors['train_id'] = 'Қатораро дуруст интихоб кунед.';
        }

        if (!$routeId || !$routes->exists($routeId)) {
            $errors['route_id'] = 'Хатсайрро дуруст интихоб кунед.';
        }

        $departureDate = self::parseDateTime($departure);
        $arrivalDate = self::parseDateTime($arrival);

        if ($departureDate === null) {
            $errors['departure_time'] = 'Сана ва вақти рафтан нодуруст аст.';
        }

        if ($arrivalDate === null) {
            $errors['arrival_time'] = 'Сана ва вақти омадан нодуруст аст.';
        }

        if (
            $departureDate !== null &&
            $arrivalDate !== null &&
            $arrivalDate <= $departureDate
        ) {
            $errors['arrival_time'] =
                'Вақти омадан бояд аз вақти рафтан дертар бошад.';
        }

        $allowedStatuses = [
            'scheduled',
            'boarding',
            'completed',
            'cancelled',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $errors['status'] = 'Ҳолати рейс нодуруст аст.';
        }

        if (
            $price === '' ||
            !is_numeric($price) ||
            (float)$price < 0 ||
            (float)$price > 999999999
        ) {
            $errors['base_price'] = 'Нархи чипта бояд адади мусбат бошад.';
        }

        if (
            $trainId &&
            $departureDate !== null &&
            $trips->existsDuplicateSchedule(
                $trainId,
                $departureDate->format('Y-m-d H:i:s'),
                $ignoreId
            )
        ) {
            $errors['departure_time'] =
                'Барои ин қатор дар ҳамин вақт аллакай рейс мавҷуд аст.';
        }

        return $errors;
    }

    private static function parseDateTime(string $value): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d\TH:i',
            $value
        );

        $check = DateTimeImmutable::getLastErrors();

        if (
            !$date ||
            ($check !== false &&
                ($check['warning_count'] > 0 || $check['error_count'] > 0)) ||
            $date->format('Y-m-d\TH:i') !== $value
        ) {
            return null;
        }

        return $date;
    }
}

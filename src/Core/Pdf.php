<?php
namespace App\Core;

class Pdf {
    public static function renderTicketHtml(array $ticket): string {
        $qrData = urlencode("TICKET:{$ticket['ticket_number']}|PASSENGER:{$ticket['passenger_name']}|TRAIN:{$ticket['train_number']}|SEAT:{$ticket['seat_number']}");
        $barcodeData = strtoupper(substr(md5($ticket['ticket_number']), 0, 16));

        $carTypeTitle = ($ticket['car_type'] === 'coupe') ? 'Купе (Бароҳат)' : 'Плацкарт (Умумӣ)';
        $seatTypeTitle = match($ticket['seat_type']) {
            'lower' => 'Поёнӣ (Нижнее)',
            'upper' => 'Болоӣ (Верхнее)',
            'side_lower' => 'Паҳлуӣ поёнӣ (Боковое нижнее)',
            'side_upper' => 'Паҳлуӣ болоӣ (Боковое верхнее)',
            default => 'Стандартӣ'
        };

        $depDate = date('d.m.Y H:i', strtotime($ticket['departure_time']));
        $arrDate = date('d.m.Y H:i', strtotime($ticket['arrival_time']));

        return <<<HTML
<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <title>Чиптаи электронӣ — {$ticket['ticket_number']}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222; background: #f4f6f9; margin: 0; padding: 20px; }
        .ticket-box { max-width: 800px; margin: 0 auto; background: #fff; border: 2px solid #0d6efd; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #0d6efd; padding-bottom: 20px; }
        .logo-title { font-size: 20px; font-weight: bold; color: #0d6efd; text-transform: uppercase; letter-spacing: 1px; }
        .sub-title { font-size: 13px; color: #666; margin-top: 4px; }
        .ticket-num { font-size: 18px; font-weight: bold; color: #dc3545; background: #ffebee; padding: 6px 14px; border-radius: 6px; }
        .info-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 25px; }
        .section-title { font-size: 12px; font-weight: bold; color: #6c757d; text-transform: uppercase; margin-bottom: 6px; }
        .highlight-val { font-size: 18px; font-weight: bold; color: #111; margin-bottom: 15px; }
        .badges-row { display: flex; gap: 15px; margin-top: 10px; margin-bottom: 20px; }
        .badge { background: #e7f1ff; color: #0d6efd; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 15px; border: 1px solid #b6d4fe; }
        .qr-section { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 15px; }
        .barcode { font-family: monospace; letter-spacing: 5px; font-size: 16px; font-weight: bold; margin-top: 10px; color: #333; }
        .qr-placeholder { width: 140px; height: 140px; background: #fff; border: 2px solid #333; display: flex; align-items: center; justify-content: center; font-size: 11px; text-align: center; padding: 5px; box-sizing: border-box; }
        .footer-note { margin-top: 25px; border-top: 1px solid #eee; padding-top: 15px; font-size: 11px; color: #777; line-height: 1.5; }
        .no-print-bar { max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 10px 20px; font-size: 15px; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .btn-primary { background: #0d6efd; color: #fff; border: none; }
        .btn-secondary { background: #6c757d; color: #fff; border: none; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .ticket-box { border: 1px solid #000; box-shadow: none; padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <a href="/my-tickets" class="btn btn-secondary">← Бозгашт ба чиптаҳои ман</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Чопи чипта (Print / PDF)</button>
    </div>

    <div class="ticket-box">
        <div class="header">
            <div>
                <div class="logo-title">🚆 КВД «РОҲИ ОҲАНИ ТОҶИКИСТОН»</div>
                <div class="sub-title">ЧИПТАИ ЭЛЕКТРОНИИ МУСОФИРКАШОНИИ БАЙНИШАҲРӢ</div>
            </div>
            <div class="ticket-num">№ {$ticket['ticket_number']}</div>
        </div>

        <div class="info-grid">
            <div>
                <div class="section-title">Хатсайр ва самти сафар:</div>
                <div class="highlight-val">{$ticket['route_name']}</div>

                <div style="display: flex; gap: 30px; margin-bottom: 15px;">
                    <div>
                        <div class="section-title">Истгоҳ ва вақти равонашавӣ:</div>
                        <div style="font-weight: bold; font-size: 15px;">{$ticket['origin_station']}</div>
                        <div style="color: #0d6efd; font-weight: bold; font-size: 16px;">{$depDate}</div>
                    </div>
                    <div>
                        <div class="section-title">Истгоҳи расидан:</div>
                        <div style="font-weight: bold; font-size: 15px;">{$ticket['destination_station']}</div>
                        <div style="color: #198754; font-weight: bold; font-size: 16px;">{$arrDate}</div>
                    </div>
                </div>

                <div class="badges-row">
                    <div class="badge">Қатора: {$ticket['train_number']}</div>
                    <div class="badge">Вагон № {$ticket['car_number']} ({$carTypeTitle})</div>
                    <div class="badge" style="background: #d1e7dd; color: #0f5132; border-color: #badbcc;">ҶОЙ: {$ticket['seat_number']} ({$seatTypeTitle})</div>
                </div>

                <div class="section-title">Мусофир:</div>
                <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">{$ticket['passenger_name']}</div>
                <div style="font-size: 13px; color: #555;">Шиноснома / ID: {$ticket['passenger_doc']}</div>
            </div>

            <div class="qr-section">
                <div class="qr-placeholder">
                    <div>
                        <strong>QR-VERIFY</strong><br>
                        <span style="font-size: 9px; word-break: break-all;">{$ticket['ticket_number']}</span><br>
                        <span style="color: green; font-weight: bold;">ТАФТИШ ШУД</span>
                    </div>
                </div>
                <div class="barcode">||| | | |||| | |||</div>
                <div style="font-size: 11px; color: #777; margin-top: 5px;">Рамзи рақамӣ: {$barcodeData}</div>
                <div style="margin-top: 15px; font-size: 22px; font-weight: bold; color: #0d6efd;">
                    {$ticket['price']} сомонӣ
                </div>
                <div style="font-size: 11px; color: green; font-weight: bold;">ПАРДОХТ ШУД (ҲОЛАТ: ФАЪОЛ)</div>
            </div>
        </div>

        <div class="footer-note">
            <strong>Эзоҳи муҳим барои мусофир:</strong> Чиптаи мазкур ҳуҷҷати расмии ҳуқуқи саворшавӣ ба қатораи байнишаҳрӣ мебошад. Лутфан на камтар аз 30 дақиқа пеш аз равона шудани қатора бо шиноснома ё ҳуҷҷати тасдиқкунандаи шахсият дар назди қатора ҳозир шавед. Барои баргардонидани чипта ба кассаи вокзал ё бахши онлайни кабинет муроҷиат намоед.
        </div>
    </div>
</body>
</html>
HTML;
    }
}

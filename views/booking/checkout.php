<?php
use App\Core\Csrf;
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- ТАЙМЕРИ ҲИСОБИ БАРЪАКСИ 15 ДАҚИҚА (Талаботи махсуси ҳатмӣ) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 timer-card bg-warning-subtle border border-warning border-2">
                <div class="card-body p-4 text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-stopwatch-fill fs-2 text-warning me-2"></i>
                        <h4 class="fw-bold mb-0 text-dark">Мӯҳлати банди 15-дақиқагӣ фаъол аст</h4>
                    </div>
                    <p class="text-muted small mb-3">
                        Ин ҷой маҳз барои шумо то анҷоми вақт мунҷамид (lock) шудааст. Лутфан маълумотро ворид намуда пардохт кунед:
                    </p>
                    
                    <div class="display-3 fw-bold text-danger font-monospace mb-2" id="countdownTimer">
                        15:00
                    </div>

                    <div class="progress" style="height: 8px;">
                        <div id="timerProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-danger" 
                             role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Маълумоти сафар ва ҷой -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-train-front me-2 text-primary"></i> Маълумоти қатора ва ҷой
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Самти хатсайр:</div>
                        <strong class="fs-5"><?= htmlspecialchars($seat['route_name']) ?></strong>
                        <div class="small text-muted"><?= htmlspecialchars($seat['origin_name']) ?> → <?= htmlspecialchars($seat['dest_name']) ?></div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="small text-muted">Вақти равонашавӣ:</div>
                        <strong class="fs-5 text-primary"><?= date('d.m.Y H:i', strtotime($seat['departure_time'])) ?></strong>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary fs-6 me-2">Қатора № <?= htmlspecialchars($seat['train_number']) ?></span>
                                <span class="badge bg-secondary fs-6 me-2">Вагони № <?= $seat['car_number'] ?> (<?= ($seat['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)</span>
                                <span class="badge bg-success fs-6">Ҷойи № <?= $seat['seat_number'] ?></span>
                            </div>
                            <div class="text-end">
                                <span class="small text-muted d-block">Маблағи пардохт:</span>
                                <span class="fs-3 fw-bold text-success"><?= $seat['seat_price'] ?> сомонӣ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Шакли маълумоти мусофир ва пардохт -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-person-vcard me-2 text-primary"></i> Маълумоти мусофир барои чипта
                </h5>

                <form id="paymentForm" action="/booking/pay" method="POST">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="seat_id" value="<?= $seat['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ному насаби пурраи мусофир (ФИО):</label>
                        <input type="text" name="passenger_name" class="form-control form-control-lg" required 
                               placeholder="Масалан: Раҳимов Фарҳод" 
                               value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                        <div class="form-text">Мувофиқи шиноснома ё шаҳодатномаи таваллуд ворид кунед.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Ҳуҷҷати тасдиқкунандаи шахсият (Шиноснома / ID-корт):</label>
                        <input type="text" name="passenger_doc" class="form-control form-control-lg" required 
                               placeholder="Масалан: А2938475 ё рақами ID" 
                               value="А2458991">
                    </div>

                    <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i> Усули пардохти онлайнӣ:</h6>
                    <div class="border rounded-3 p-3 mb-4 bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay1" checked>
                            <label class="form-check-label fw-bold" for="pay1">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-1"></i> Корти миллӣ / Кортҳои бонкии Тоҷикистон (Бе комиссия)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay2">
                            <label class="form-check-label fw-bold" for="pay2">
                                <i class="bi bi-wallet2 text-success me-1"></i> Ҳамёни электронӣ (Dushanbe City, Alif, DC, Korti Milli)
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="btnPay" class="btn btn-success btn-lg py-3 fw-bold shadow">
                            <i class="bi bi-check2-circle me-1"></i> Пардохт кардан (<?= $seat['seat_price'] ?> сомонӣ) ва гирифтани чипта
                        </button>
                        <a href="/booking/seat-map/<?= $seat['trip_id'] ?>/<?= $seat['car_id'] ?>" class="btn btn-outline-secondary">
                            Ивази ҷой ё бозгашт
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Ҳисоби баръакси 15 дақиқа (900 сония)
let totalDuration = 900; // 15 дақиқа
let remainingSeconds = <?= (int)$remainingSeconds ?>;

function updateCountdown() {
    if (remainingSeconds <= 0) {
        document.getElementById('countdownTimer').innerText = "00:00";
        document.getElementById('timerProgressBar').style.width = "0%";
        document.getElementById('btnPay').disabled = true;
        alert("Мӯҳлати 15-дақиқагии банди ҷой ба охир расид! Ҷой ба таври худкор озод шуд.");
        window.location.href = "/booking/seat-map/<?= $seat['trip_id'] ?>/<?= $seat['car_id'] ?>";
        return;
    }

    let minutes = Math.floor(remainingSeconds / 60);
    let seconds = remainingSeconds % 60;

    let mStr = minutes < 10 ? "0" + minutes : minutes;
    let sStr = seconds < 10 ? "0" + seconds : seconds;

    document.getElementById('countdownTimer').innerText = mStr + ":" + sStr;

    let percent = (remainingSeconds / totalDuration) * 100;
    document.getElementById('timerProgressBar').style.width = percent + "%";

    remainingSeconds--;
}

updateCountdown();
setInterval(updateCountdown, 1000);
</script>

<?php use App\Core\Csrf; ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle d-inline-block mb-2">
                        <i class="bi bi-person-plus fs-1"></i>
                    </div>
                    <h3 class="fw-bold">Бақайдгирии мусофир</h3>
                    <p class="text-muted small">Барои харидории онлайн ва дидани чиптаҳо сабти ном кунед</p>
                </div>

                <form action="/register" method="POST">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ному насаб (ФИО):</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Масалан: Қосимов Ҷамшед">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Логин:</label>
                        <input type="text" name="username" class="form-control" required placeholder="Логини дилхоҳ бо ҳарфҳои лотинӣ">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Почтаи электронӣ:</label>
                            <input type="email" name="email" class="form-control" placeholder="nom@example.tj">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Рақами телефон:</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+992 92 000 00 00">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Рамз (Парол):</label>
                        <input type="password" name="password" class="form-control" required placeholder="Камаш 4 аломат">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold shadow-sm mb-3">
                        <i class="bi bi-check-circle me-1"></i> Бақайдгирӣ ва оғози сафар
                    </button>

                    <div class="text-center small">
                        Аллакай ба қайд гирифта шудаед? <a href="/login" class="text-decoration-none fw-bold">Воридшавӣ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

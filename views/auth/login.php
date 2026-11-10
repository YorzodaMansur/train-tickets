<?php use App\Core\Csrf; ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-block mb-2">
                        <i class="bi bi-person-lock fs-1"></i>
                    </div>
                    <h3 class="fw-bold">Воридшавӣ ба низом</h3>
                    <p class="text-muted small">Барои идора ё хариди чипта маълумоти худро ворид кунед</p>
                </div>

                <form action="/login" method="POST" id="loginForm">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Логин (Номи корбарӣ):</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control" required placeholder="Масалан: passenger1 ё admin">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Рамз (Парол):</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Рамзи худро нависед">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Ворид шудан
                    </button>
                </form>
                <div class="text-center mt-3 small">
                    Ҳисоб надоред? <a href="/register" class="text-decoration-none fw-bold">Бақайдгирӣ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemo(u, p) {
    document.getElementById('username').value = u;
    document.getElementById('password').value = p;
}
</script>

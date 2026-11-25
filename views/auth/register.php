<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm p-4">
            <h1 class="h3 mb-3">Бақайдгирии мусофир</h1>
            <p class="text-secondary">Намунаи интерфейси бақайдгирӣ барои қисми сервер.</p>
            <form method="post" action="<?= e(url('/register')) ?>">
                <div class="mb-3">
                    <label class="form-label" for="full_name">Ному насаб</label>
                    <input class="form-control" id="full_name" name="full_name" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Почтаи электронӣ</label>
                    <input class="form-control" id="email" name="email" type="email">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="phone">Рақами телефон</label>
                    <input class="form-control" id="phone" name="phone" type="tel">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="new_password">Рамз</label>
                    <input class="form-control" id="new_password" name="password" type="password">
                </div>
                <button class="btn btn-primary" type="submit">Эҷод кардани ҳисоб</button>
            </form>
            <small class="text-secondary mt-3">Коркарди бақайдгирӣ берун аз доираи лабораторияи №12 аст.</small>
        </div>
    </div>
</div>

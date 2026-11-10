/**
 * Роҳи оҳани Тоҷикистон — app.js
 * Коркарди интерактивӣ ва таймерҳои 15-дақиқагӣ
 */

document.addEventListener('DOMContentLoaded', function () {
    // Фаъолсозии Tooltips-и Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], .btn-seat-item'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Огоҳинома дар консол
    console.log("Railway Tickets Application Loaded Successfully.");
});

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="alert alert-success d-flex align-items-center rounded-4 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill fs-2 me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Чипта бо муваффақият расмиёт шуд!</h5>
                    <div>Чиптаи электронии шумо омода аст. Шумо метавонед онро чоп кунед ё дар формати PDF сабт намоед.</div>
                </div>
            </div>

            <!-- Бланкаи расмии чипта -->
            <div class="card border-primary border-2 shadow-sm rounded-4 p-4 bg-white mb-4 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-center border-bottom border-2 border-primary pb-3 mb-3">
                    <div>
                        <div class="fs-5 fw-bold text-primary text-uppercase">
                            <i class="bi bi-train-front me-2"></i> КВД «РОҲИ ОҲАНИ ТОҶИКИСТОН»
                        </div>
                        <div class="small text-muted">ЧИПТАИ ЭЛЕКТРОНИИ БАЙНИШАҲРӢ</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger fs-6 px-3 py-2">№ <?= htmlspecialchars($ticket['ticket_number']) ?></span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold">Хатсайри қатора:</div>
                            <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($ticket['route_name']) ?></h4>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="small text-muted">Истгоҳи оғоз:</div>
                                <div class="fw-bold fs-5 text-primary"><?= htmlspecialchars($ticket['origin_station']) ?></div>
                                <div class="text-dark fw-bold"><?= date('d.m.Y H:i', strtotime($ticket['departure_time'])) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Истгоҳи анҷом:</div>
                                <div class="fw-bold fs-5 text-success"><?= htmlspecialchars($ticket['destination_station']) ?></div>
                                <div class="text-dark fw-bold"><?= date('d.m.Y H:i', strtotime($ticket['arrival_time'])) ?></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <span class="badge bg-primary p-2">Қатора № <?= htmlspecialchars($ticket['train_number']) ?></span>
                            <span class="badge bg-secondary p-2">Вагони № <?= htmlspecialchars($ticket['car_number']) ?> (<?= ($ticket['car_type'] === 'coupe') ? 'Купе' : 'Плацкарт' ?>)</span>
                            <span class="badge bg-success p-2">ҶОЙ № <?= htmlspecialchars($ticket['seat_number']) ?> (<?= ($ticket['seat_type'] === 'lower') ? 'Поёнӣ' : 'Болоӣ' ?>)</span>
                        </div>

                        <div class="border-top pt-2">
                            <div class="small text-muted">Мусофир:</div>
                            <strong class="fs-5"><?= htmlspecialchars($ticket['passenger_name']) ?></strong>
                            <div class="small text-muted">Шиноснома: <?= htmlspecialchars($ticket['passenger_doc']) ?></div>
                        </div>
                    </div>

                    <!-- Бахши QR-код ва тафтишот -->
                    <div class="col-md-4 text-center border-start-md d-flex flex-column justify-content-center align-items-center bg-light rounded-3 p-3">
                        <div class="qr-box p-3 bg-white border border-dark rounded-3 mb-2" style="width: 130px; height: 130px; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center font-monospace small">
                                <i class="bi bi-qr-code fs-1 text-dark"></i>
                                <div style="font-size: 8px;">QR-ТАФТИШ</div>
                            </div>
                        </div>
                        <div class="font-monospace small text-muted">|||| | ||| |||| |</div>
                        <div class="fs-4 fw-bold text-success mt-2"><?= htmlspecialchars($ticket['price']) ?> сомонӣ</div>
                        <span class="badge bg-success mt-1">ПАРДОХТ ШУДААСТ</span>
                    </div>
                </div>
            </div>

            <!-- Тугмаҳои амал -->
            <div class="d-flex justify-content-between">
                <a href="/my-tickets" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Ба рӯйхати чиптаҳо
                </a>
                <a href="/tickets/view/<?= $ticket['id'] ?>?print=1" target="_blank" class="btn btn-primary btn-lg fw-bold shadow">
                    <i class="bi bi-printer-fill me-2"></i> Чопи чипта / Сабт дар PDF
                </a>
            </div>
        </div>
    </div>
</div>

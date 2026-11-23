'use strict';

// ============================================
// 1. ВАЛИДАТСИЯИ ФОРМАИ ҶУСТУҶӮ
// ============================================

const searchForm = document.querySelector('#ticket-search-form');

if (searchForm) {
    const from = searchForm.querySelector('[name="from"]');
    const to = searchForm.querySelector('[name="to"]');
    const date = searchForm.querySelector('[name="date"]');
    const passengers = searchForm.querySelector('[name="passengers"]');

    const fields = [from, to, date, passengers];

    // Санаи имрӯзро ҳамчун ҳадди ақал мегузорем.
    const today = new Date();
    const localToday = [
        today.getFullYear(),
        String(today.getMonth() + 1).padStart(2, '0'),
        String(today.getDate()).padStart(2, '0')
    ].join('-');

    date.min = localToday;

    if (!date.value) {
        date.value = localToday;
    }

    function getErrorElement(field) {
        const errorId = field.getAttribute('aria-describedby');
        return errorId ? document.getElementById(errorId) : null;
    }

    function clearError(field) {
        const error = getErrorElement(field);

        if (error) {
            error.textContent = '';
        }

        field.removeAttribute('aria-invalid');
    }

    function showError(field, message) {
        const error = getErrorElement(field);

        if (error) {
            error.textContent = message;
        }

        field.setAttribute('aria-invalid', 'true');
    }

    function validateField(field) {
        clearError(field);

        // Қоидаи махсус: истгоҳҳо набояд якхела бошанд.
        if (field === to) {
            if (from.value && to.value && from.value === to.value) {
                to.setCustomValidity(
                    'Истгоҳи рафтан ва омадан набояд якхела бошанд.'
                );
            } else {
                to.setCustomValidity('');
            }
        }

        if (field.validity.valueMissing) {
            showError(field, 'Ин майдонро пур кардан ҳатмист.');
            return false;
        }

        if (field.validity.rangeUnderflow) {
            showError(field, 'Сана ё қимати воридшуда нодуруст аст.');
            return false;
        }

        if (field.validity.rangeOverflow) {
            showError(field, 'Шумораи мусофирон набояд аз 6 зиёд бошад.');
            return false;
        }

        if (field.validity.customError) {
            showError(field, field.validationMessage);
            return false;
        }

        if (!field.validity.valid) {
            showError(field, 'Маълумоти воридшударо дуруст санҷед.');
            return false;
        }

        return true;
    }

    fields.forEach((field) => {
        field.addEventListener('input', () => {
            if (field === from || field === to) {
                to.setCustomValidity('');
                clearError(to);
            }

            validateField(field);
        });

        field.addEventListener('change', () => {
            if (field === from || field === to) {
                to.setCustomValidity('');
                clearError(to);
            }

            validateField(field);
        });
    });

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();

        let valid = true;

        fields.forEach((field) => {
            if (!validateField(field)) {
                valid = false;
            }
        });

        if (!valid) {
            const firstInvalid = fields.find(
                (field) => !field.validity.valid
            );

            if (firstInvalid) {
                firstInvalid.focus();
            }
        }
    });
}


// ============================================
// 2. ҶУСТУҶӮИ ФАВРӢ — DEBOUNCE 300 MS
// ============================================

const liveSearch = document.querySelector('#trip-live-search');
const resultsContainer = document.querySelector('#trip-search-results');

if (liveSearch && resultsContainer) {
    let debounceTimer = null;
    let activeController = null;

    // Роҳи API нисбат ба ҷойи ҳамин файли JS.
    const apiUrl = new URL('../../api/trips/index.php', import.meta.url);

    function showSearchMessage(message) {
        resultsContainer.replaceChildren();

        const paragraph = document.createElement('p');
        paragraph.className = 'text-secondary mb-0 p-3';
        paragraph.textContent = message;

        resultsContainer.appendChild(paragraph);
        resultsContainer.classList.remove('hidden');
    }

    function renderResults(trips) {
        resultsContainer.replaceChildren();

        if (trips.length === 0) {
            showSearchMessage('Қатораи мувофиқ ёфт нашуд.');
            return;
        }

        trips.forEach((trip) => {
            const card = document.createElement('div');
            card.className = 'result-item';

            const title = document.createElement('h3');
            title.className = 'h6 text-info mb-2';

            // Муҳим: HTML иҷро намешавад, танҳо матн нишон дода мешавад.
            title.textContent = trip.route_name;

            const details = document.createElement('p');
            details.className = 'small text-secondary mb-0';
            details.textContent =
                'Қатора № ' + trip.train_number +
                ' | Рафтан: ' + trip.departure +
                ' | Нарх: ' + trip.price + ' сомонӣ';

            card.append(title, details);
            resultsContainer.appendChild(card);
        });

        resultsContainer.classList.remove('hidden');
    }

    async function searchTrips(query) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        showSearchMessage('Ҷустуҷӯ идома дорад...');

        const requestUrl = new URL(apiUrl);
        requestUrl.searchParams.set('q', query);

        try {
            const response = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                signal: activeController.signal
            });

            if (!response.ok) {
                throw new Error('HTTP error: ' + response.status);
            }

            const result = await response.json();

            if (
                result.success !== true ||
                !Array.isArray(result.data)
            ) {
                throw new Error('Ҷавоби API нодуруст аст.');
            }

            renderResults(result.data);

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            showSearchMessage(
                'Хатои шабака. Пайвастшавӣ ба серверро санҷед.'
            );
        }
    }

    liveSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);

        const query = liveSearch.value.trim();

        if (query === '') {
            if (activeController) {
                activeController.abort();
            }

            resultsContainer.replaceChildren();
            resultsContainer.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            searchTrips(query);
        }, 300);
    });
}


// ============================================
// 3. РАВЗАНАИ ТАСДИҚ БО EVENT DELEGATION
// ============================================

const confirmModal = document.querySelector('#confirm-modal');

if (confirmModal) {
    const confirmText = confirmModal.querySelector('#confirm-modal-text');
    const confirmButton = confirmModal.querySelector('[data-modal-confirm]');
    const cancelButtons = confirmModal.querySelectorAll('[data-modal-cancel]');

    let pendingForm = null;
    let previousFocus = null;

    function openModal(form) {
        pendingForm = form;
        previousFocus = document.activeElement;

        confirmText.textContent =
            form.dataset.confirmMessage ||
            'Оё мехоҳед ин амалро иҷро кунед?';

        confirmModal.style.display = 'block';
        confirmModal.classList.add('show');
        confirmModal.setAttribute('aria-hidden', 'false');

        confirmButton.focus();
    }

    function closeModal() {
        confirmModal.classList.remove('show');
        confirmModal.style.display = 'none';
        confirmModal.setAttribute('aria-hidden', 'true');

        pendingForm = null;

        if (previousFocus) {
            previousFocus.focus();
        }
    }

    // Як event listener барои ҳамаи формаҳои тасдиқшаванда.
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('.js-confirm-form');

        if (!form) {
            return;
        }

        // Агар корбар аллакай тасдиқ карда бошад, форма фиристода мешавад.
        if (form.dataset.confirmed === 'true') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        openModal(form);
    });

    confirmButton.addEventListener('click', () => {
        if (!pendingForm) {
            return;
        }

        const form = pendingForm;

        closeModal();

        form.dataset.confirmed = 'true';
        form.requestSubmit();
    });

    cancelButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    confirmModal.addEventListener('click', (event) => {
        if (event.target === confirmModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            confirmModal.style.display === 'block'
        ) {
            closeModal();
        }
    });
}

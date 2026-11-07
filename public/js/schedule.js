/**
 * Harmonogram: przesuwanie tygodnia, wybór dnia, odhaczanie zadań, własne zabiegi.
 * Wymaga app.js (`window.Komorebi`). Bez JS wszystko działa na zwykłych linkach i formularzach.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    const root = document.querySelector('[data-week]');
    if (!K || !root) {
        return;
    }

    const doc = document;
    let ticket = 0;

    const dateOf = (url) => new URL(url, location.href).searchParams.get('date');
    const currentDate = () => doc.querySelector('[data-add-form] input[name="date"]')?.value ?? '';

    const plural = (n, one, few, many) => {
        if (n === 1) {
            return one;
        }
        const tens = n % 100;
        return n % 10 >= 2 && n % 10 <= 4 && (tens < 12 || tens > 14) ? few : many;
    };

    /** Odświeża liczniki „do zrobienia” w pasku tygodnia na podstawie odpowiedzi serwera. */
    function updateWeek(days) {
        (days ?? []).forEach((day) => {
            const link = doc.querySelector(`[data-day="${CSS.escape(day.date)}"]`);
            const count = link?.querySelector('[data-day-count]');
            if (!count) {
                return;
            }

            count.replaceChildren();
            if (day.total > 0 && day.open === 0) {
                const icon = doc.createElement('span');
                icon.className = 'ms';
                icon.textContent = 'done_all';
                count.append(icon);
            } else if (day.open > 0) {
                count.textContent = String(day.open);
            }

            const tasks = day.total === 0
                ? 'brak zabiegów'
                : `${day.total} ${plural(day.total, 'zabieg', 'zabiegi', 'zabiegów')}, do zrobienia ${day.open}`;
            link.setAttribute('aria-label', `${day.name}, ${day.day}${day.isToday ? ' (dziś)' : ''}: ${tasks}`);
        });
    }

    /**
     * Przechodzi na wskazany adres harmonogramu, podmieniając fragmenty strony.
     * `direction`: 'next' | 'prev' (zmiana tygodnia), 'fade' (ten sam tydzień lub odświeżenie).
     */
    async function navigate(url, { push = true, direction = null } = {}) {
        const myTicket = ++ticket;
        const target = dateOf(url) ?? new Date().toLocaleDateString('sv-SE');
        const dayInWeek = doc.querySelector(`[data-day="${CSS.escape(target)}"]`);
        const dir = direction ?? (dayInWeek ? 'fade' : target > currentDate() ? 'next' : 'prev');

        // Natychmiastowa informacja zwrotna: podświetl wybrany dzień jeszcze przed odpowiedzią serwera.
        if (dayInWeek) {
            doc.querySelectorAll('[data-day]').forEach((day) => {
                const active = day === dayInWeek;
                day.classList.toggle('is-active', active);
                active ? day.setAttribute('aria-current', 'date') : day.removeAttribute('aria-current');
            });
        }

        const leaving = dir === 'fade' ? [] : [doc.querySelector('[data-week-days]'), doc.querySelector('[data-day-panel]')];
        const outAnimation = Promise.all(leaving.map((element) => K.play(element, `is-out-${dir}`)));

        let next;
        try {
            [next] = await Promise.all([K.fetchDocument(url), outAnimation]);
        } catch {
            return;
        }

        if (myTicket !== ticket) {
            return;
        }

        K.swap(next, ['[data-week]', '[data-day-panel]', '.schedule-season', '.schedule-rule', '.schedule-upcoming']);

        // Formularz dodawania zostaje (nie tracimy wpisanego tekstu), aktualizujemy tylko datę.
        const newForm = next.querySelector('[data-add-form]');
        const form = doc.querySelector('[data-add-form]');
        if (form && newForm) {
            form.querySelector('input[name="date"]').value = newForm.querySelector('input[name="date"]').value;
            form.querySelector('input[name="_back"]').value = newForm.querySelector('input[name="_back"]').value;
            form.querySelector('.schedule-add__hint').innerHTML = newForm.querySelector('.schedule-add__hint').innerHTML;
        }

        const entering = dir === 'fade' ? 'is-in-fade' : `is-in-${dir}`;
        K.play(doc.querySelector('[data-week-days]'), entering);
        K.play(doc.querySelector('[data-day-panel]'), entering);

        if (push) {
            history.pushState({}, '', url);
        }
        K.refreshNotifications();
    }

    // --- Nawigacja: strzałki, „Dziś”, dni, kafelki nadchodzących zabiegów ---------------
    doc.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-add]');
        if (opener) {
            event.preventDefault();
            const details = doc.querySelector('[data-add]');
            if (details) {
                details.open = true;
                details.scrollIntoView({ block: 'nearest', behavior: K.reduceMotion ? 'auto' : 'smooth' });
                doc.getElementById('add-title')?.focus({ preventScroll: true });
            }
            return;
        }

        const link = event.target.closest('[data-week-nav], [data-day], [data-jump-date]');
        if (!link || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        event.preventDefault();
        const nav = link.dataset.weekNav;
        navigate(link.href, { direction: nav === 'prev' || nav === 'next' ? nav : null });
    });

    window.addEventListener('popstate', () => navigate(location.href, { push: false }));

    // --- Strzałki lewo/prawo między dniami tygodnia --------------------------------------
    doc.addEventListener('keydown', (event) => {
        if (!['ArrowLeft', 'ArrowRight'].includes(event.key) || !event.target.matches?.('[data-day]')) {
            return;
        }

        const days = [...doc.querySelectorAll('[data-day]')];
        const index = days.indexOf(event.target) + (event.key === 'ArrowRight' ? 1 : -1);
        if (days[index]) {
            event.preventDefault();
            days[index].focus();
        }
    });

    // --- Gest przeciągnięcia na ekranie dotykowym: w lewo = następny tydzień -------------
    let startX = 0;
    let startY = 0;
    doc.addEventListener('touchstart', (event) => {
        if (event.target.closest('[data-week]')) {
            startX = event.touches[0].clientX;
            startY = event.touches[0].clientY;
        } else {
            startX = 0;
        }
    }, { passive: true });

    doc.addEventListener('touchend', (event) => {
        if (!startX) {
            return;
        }

        const dx = event.changedTouches[0].clientX - startX;
        const dy = event.changedTouches[0].clientY - startY;
        startX = 0;
        if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy) * 1.5) {
            doc.querySelector(`[data-week-nav="${dx < 0 ? 'next' : 'prev'}"]`)?.click();
        }
    }, { passive: true });

    // --- Odhaczanie zadań -----------------------------------------------------------------
    doc.addEventListener('change', async (event) => {
        const checkbox = event.target.closest('.schedule-task__check');
        const form = checkbox?.closest('[data-task-form]');
        if (!form) {
            return;
        }

        const task = form.closest('[data-task]');
        task.classList.toggle('is-done', checkbox.checked);
        const result = await K.post(form);

        if (!result.ok) {
            checkbox.checked = !checkbox.checked;
            task.classList.toggle('is-done', checkbox.checked);
            K.toast(result.data.message ?? 'Nie udało się zapisać zadania.', 'error');
            return;
        }

        const summary = doc.querySelector('[data-day-summary]');
        if (summary && result.data.summary) {
            summary.textContent = result.data.summary;
        }

        const urgent = doc.querySelector('[data-urgent]');
        if (urgent) {
            urgent.textContent = `Pilne: ${result.data.urgent}`;
            urgent.hidden = result.data.urgent === 0;
        }

        updateWeek(result.data.week);

        if (result.data.open === 0 && result.data.total > 0) {
            K.toast('Wszystkie zadania na ten dzień wykonane.', 'success');
        }

        K.applyUnread(result.data.unread ?? 0);
        K.refreshNotifications();
    });

    // --- Usuwanie własnego zabiegu ---------------------------------------------------------
    doc.addEventListener('submit', async (event) => {
        const form = event.target;

        if (form.matches('[data-task-delete]')) {
            event.preventDefault();
            const task = form.closest('[data-task]');
            task.classList.add('is-removing');
            const [result] = await Promise.all([K.post(form), K.sleep(K.reduceMotion ? 0 : 240)]);

            if (!result.ok) {
                task.classList.remove('is-removing');
                K.toast(result.data.message ?? 'Nie udało się usunąć zabiegu.', 'error');
                return;
            }

            K.toast(result.data.message, 'success');
            navigate(location.href, { push: false, direction: 'fade' });
        }

        // --- Dodawanie własnego zabiegu -------------------------------------------------------
        if (form.matches('[data-add-form]')) {
            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            const result = await K.post(form);
            button.disabled = false;

            if (!result.ok) {
                K.toast(result.data.message ?? 'Nie udało się dodać zabiegu.', 'error');
                return;
            }

            K.toast(result.data.message, 'success');
            form.querySelector('[name="title"]').value = '';
            form.querySelector('[name="note"]').value = '';
            form.querySelector('[name="time"]').value = '';
            doc.querySelector('[data-add]')?.removeAttribute('open');
            await navigate(location.href, { push: false, direction: 'fade' });
            doc.querySelector('[data-tasks] [data-task]:last-child')?.classList.add('is-new');
        }
    });
})();

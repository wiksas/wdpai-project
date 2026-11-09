/**
 * Szczegóły skanu (/clinic/scans/{id}): lupa na zdjęciu i ponowna ocena bez przeładowania strony.
 * Wymaga app.js (`window.Komorebi`); bez JS zdjęcie otwiera się w nowej karcie, a formularze działają zwykłym POST.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;
    const MAX_SCALE = 6;
    const CLICK_SCALE = 2.5;
    const STEP = 1.5;

    function initZoom(zoom) {
        const link = zoom.querySelector('[data-zoom-link]');
        const img = zoom.querySelector('[data-zoom-img]');
        const tools = zoom.querySelector('[data-zoom-tools]');
        const levelLabel = zoom.querySelector('[data-zoom-level]');
        const hint = doc.querySelector('[data-zoom-hint]');
        if (!link || !img) {
            return;
        }

        let scale = 1;
        let x = 0;
        let y = 0;
        let drag = null;
        let moved = false;

        tools.hidden = false;
        if (hint) {
            hint.textContent = 'Kliknij zdjęcie albo użyj przycisków, żeby je powiększyć. Przeciągnij, żeby przesunąć (na klawiaturze strzałki).';
        }
        img.draggable = false;

        function apply(animate) {
            const { width, height } = link.getBoundingClientRect();
            x = Math.min(0, Math.max(width - width * scale, x));
            y = Math.min(0, Math.max(height - height * scale, y));
            img.style.transition = animate && !K.reduceMotion ? 'transform 160ms ease-out' : 'none';
            img.style.transform = `translate(${x}px, ${y}px) scale(${scale})`;
            levelLabel.textContent = `${Math.round(scale * 100)}%`;
            zoom.classList.toggle('is-zoomed', scale > 1);
        }

        // Skala zmienia się wokół punktu (cx, cy) we współrzędnych ramki, więc ten punkt zostaje pod kursorem.
        function zoomAt(next, cx, cy, animate = true) {
            const target = Math.min(MAX_SCALE, Math.max(1, next));
            x = cx - (cx - x) * (target / scale);
            y = cy - (cy - y) * (target / scale);
            scale = target;
            apply(animate);
        }

        function center() {
            const { width, height } = link.getBoundingClientRect();
            return [width / 2, height / 2];
        }

        link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) {
                return;
            }
            event.preventDefault();
            if (moved) {
                moved = false;
                return;
            }
            const rect = link.getBoundingClientRect();
            if (scale > 1) {
                zoomAt(1, 0, 0);
            } else {
                const [cx, cy] = event.detail === 0 ? center() : [event.clientX - rect.left, event.clientY - rect.top];
                zoomAt(CLICK_SCALE, cx, cy);
            }
        });

        link.addEventListener('pointerdown', (event) => {
            if (scale <= 1 || event.button !== 0) {
                return;
            }
            drag = { id: event.pointerId, startX: event.clientX, startY: event.clientY, originX: x, originY: y };
            moved = false;
            link.setPointerCapture(event.pointerId);
        });
        link.addEventListener('pointermove', (event) => {
            if (!drag || event.pointerId !== drag.id) {
                return;
            }
            const dx = event.clientX - drag.startX;
            const dy = event.clientY - drag.startY;
            if (Math.abs(dx) + Math.abs(dy) > 4) {
                moved = true;
            }
            x = drag.originX + dx;
            y = drag.originY + dy;
            apply(false);
        });
        const endDrag = (event) => {
            if (drag && event.pointerId === drag.id) {
                drag = null;
            }
        };
        link.addEventListener('pointerup', endDrag);
        link.addEventListener('pointercancel', endDrag);

        // Ctrl + kółko (także szczypanie na gładziku) powiększa; zwykłe kółko nadal przewija stronę.
        link.addEventListener('wheel', (event) => {
            if (!event.ctrlKey) {
                return;
            }
            event.preventDefault();
            const rect = link.getBoundingClientRect();
            zoomAt(scale * (event.deltaY < 0 ? 1.2 : 1 / 1.2), event.clientX - rect.left, event.clientY - rect.top, false);
        }, { passive: false });

        link.addEventListener('keydown', (event) => {
            const pan = { ArrowLeft: [40, 0], ArrowRight: [-40, 0], ArrowUp: [0, 40], ArrowDown: [0, -40] }[event.key];
            if (pan && scale > 1) {
                event.preventDefault();
                x += pan[0];
                y += pan[1];
                apply(true);
            } else if (event.key === '+' || event.key === '=') {
                zoomAt(scale * STEP, ...center());
            } else if (event.key === '-') {
                zoomAt(scale / STEP, ...center());
            } else if (event.key === '0') {
                zoomAt(1, 0, 0);
            }
        });

        zoom.querySelector('[data-zoom-in]').addEventListener('click', () => zoomAt(scale * STEP, ...center()));
        zoom.querySelector('[data-zoom-out]').addEventListener('click', () => zoomAt(scale / STEP, ...center()));
        zoom.querySelector('[data-zoom-reset]').addEventListener('click', () => zoomAt(1, 0, 0));
        window.addEventListener('resize', () => apply(false));
    }

    const zoom = doc.querySelector('[data-zoom]');
    if (zoom) {
        initZoom(zoom);
    }

    // Ponowna ocena tego samego zdjęcia: po odpowiedzi podmieniamy nagłówek i wynik, formularz zostaje.
    const form = doc.querySelector('[data-reassess-form]');
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const button = form.querySelector('[data-reassess-button]');
        const status = form.querySelector('[data-reassess-status]');
        button.disabled = true;
        status.hidden = false;

        const result = await K.request(form.action, { method: 'POST', body: new FormData(form) });
        button.disabled = false;
        status.hidden = true;

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się ocenić ponownie. Spróbuj za chwilę.', 'error');
            return;
        }

        const next = await K.fetchDocument(location.href);
        if (next) {
            K.swap(next, ['[data-scan-head]', '[data-scan-result-box]']);
            K.play(doc.querySelector('[data-scan-result-box]'), 'fx-in');
        }
        K.toast(result.data.message, 'success');
    });
})();

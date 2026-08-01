<script>
(function () {
    function setupSnapSlider(wrap) {
        const track = wrap.querySelector('[data-snap-slider]');
        const viewport = wrap.querySelector('.snap-slider-viewport');
        const prev = wrap.querySelector('[data-snap-prev]');
        const next = wrap.querySelector('[data-snap-next]');
        if (!track || !viewport) return;

        const minSlide = Math.max(160, parseFloat(wrap.getAttribute('data-min-slide')) || 200);
        const fixedSlide = parseFloat(wrap.getAttribute('data-fixed-slide')) || 0;
        const naturalSlides = wrap.getAttribute('data-natural-slides') === '1';
        const maxPerView = Math.max(1, parseInt(wrap.getAttribute('data-max-per-view') || '0', 10) || 0);
        const reserveSlots = Math.max(0, parseInt(wrap.getAttribute('data-reserve-slots') || '0', 10) || 0);
        let animating = false;
        let index = 0;

        track.classList.add('is-transform-slider');
        track.style.willChange = 'transform';

        const slides = () => Array.from(track.querySelectorAll('.snap-slide'));
        const isRtl = () =>
            getComputedStyle(document.documentElement).direction === 'rtl'
            || getComputedStyle(track).direction === 'rtl'
            || getComputedStyle(wrap).direction === 'rtl';

        const gapOf = () => {
            const styles = getComputedStyle(track);
            return parseFloat(styles.columnGap || styles.gap) || 14.4;
        };

        const contentWidth = (list, gapVal) => {
            if (!list.length) return 0;
            return list.reduce((sum, el, i) => {
                return sum + el.getBoundingClientRect().width + (i < list.length - 1 ? gapVal : 0);
            }, 0);
        };

        const offsetForIndex = (list, i, gapVal) => {
            let left = 0;
            for (let n = 0; n < i; n++) {
                left += list[n].getBoundingClientRect().width + gapVal;
            }
            return left;
        };

        const applyTransform = (offsetPx, animate) => {
            const rtl = isRtl();
            const x = rtl ? offsetPx : -offsetPx;
            track.style.transition = animate
                ? 'transform 1.05s cubic-bezier(.22,.8,.24,1)'
                : 'none';
            track.style.transform = `translate3d(${x}px, 0, 0)`;
        };

        const layout = () => {
            const list = slides();
            if (!list.length) return { perView: 1, step: minSlide, count: 0, natural: false, gap: 0 };
            const gapVal = gapOf();
            const vw = Math.max(1, viewport.clientWidth);
            const maxSlide = Math.max(140, vw);

            // Exact fill: N whole cards, no peek of the next one.
            const fillWidth = (slots) => {
                const n = Math.max(1, slots);
                return (vw - gapVal * Math.max(0, n - 1)) / n;
            };

            if (naturalSlides) {
                list.forEach((el) => {
                    el.style.flex = '0 0 auto';
                    el.style.width = 'max-content';
                    el.style.minWidth = '0';
                    el.style.maxWidth = 'none';
                });
                void track.offsetWidth;
                const naturalWidths = list.map((el) => el.getBoundingClientRect().width);
                const equalW = Math.ceil(Math.max(...naturalWidths, minSlide));
                let perView = Math.max(1, Math.floor((vw + gapVal) / (equalW + gapVal)));
                perView = Math.min(perView, list.length);
                const slideW = fillWidth(perView);
                list.forEach((el) => {
                    el.style.width = `${slideW}px`;
                    el.style.minWidth = `${slideW}px`;
                    el.style.maxWidth = `${slideW}px`;
                    el.style.flex = `0 0 ${slideW}px`;
                });
                return { perView, step: slideW + gapVal, count: list.length, natural: true, gap: gapVal };
            }

            if (reserveSlots > 0) {
                const maxFit = Math.max(1, Math.floor((vw + gapVal) / (minSlide + gapVal)));
                const slots = Math.min(reserveSlots, maxFit, Math.max(1, list.length));
                const slideW = fillWidth(slots);
                list.forEach((el) => {
                    el.style.flex = `0 0 ${slideW}px`;
                    el.style.width = `${slideW}px`;
                    el.style.minWidth = `${slideW}px`;
                    el.style.maxWidth = `${slideW}px`;
                });
                return { perView: slots, step: slideW + gapVal, count: list.length, natural: false, gap: gapVal };
            }

            const baseW = fixedSlide > 0 ? Math.min(fixedSlide, maxSlide) : Math.min(minSlide, maxSlide);
            let perView = Math.max(1, Math.floor((vw + gapVal) / (baseW + gapVal)));
            if (maxPerView > 0) perView = Math.min(perView, maxPerView);
            perView = Math.min(perView, list.length);

            const slideW = fillWidth(perView);
            list.forEach((el) => {
                el.style.flex = `0 0 ${slideW}px`;
                el.style.width = `${slideW}px`;
                el.style.minWidth = `${slideW}px`;
                el.style.maxWidth = `${slideW}px`;
            });
            return { perView, step: slideW + gapVal, count: list.length, natural: false, gap: gapVal };
        };

        const maxScrollOffset = (meta) => {
            const list = slides();
            const total = contentWidth(list, meta.gap);
            return Math.max(0, total - viewport.clientWidth);
        };

        // How many next-clicks are needed to fully reveal the last card.
        // Use ceil so a leftover peek (< 1 step) is still a reachable end position.
        const maxIndex = (meta) => {
            const maxOff = maxScrollOffset(meta);
            if (maxOff <= 1 || meta.step <= 0) return 0;
            return Math.max(1, Math.ceil(maxOff / meta.step));
        };

        const syncNav = (meta) => {
            const show = maxScrollOffset(meta) > 1;
            if (prev) prev.hidden = !show;
            if (next) next.hidden = !show;
            wrap.classList.toggle('is-nav-hidden', !show);
            return show;
        };

        const offsetFor = (meta, i) => {
            const list = slides();
            const raw = meta.natural
                ? offsetForIndex(list, i, meta.gap)
                : i * meta.step;
            return Math.min(raw, maxScrollOffset(meta));
        };

        const currentOffset = (meta) => offsetFor(meta, index);

        const scrollToIndex = (i, meta, animate = true) => {
            if (animating && animate) return;
            const max = maxIndex(meta);
            const maxOff = maxScrollOffset(meta);

            if (max <= 0 || maxOff <= 1) {
                index = 0;
                applyTransform(0, false);
                return;
            }

            if (i > max) {
                // Already showing the last card fully → loop to start.
                // Otherwise clamp to the end so the leftover peek can finish scrolling in.
                index = currentOffset(meta) >= maxOff - 1 ? 0 : max;
            } else if (i < 0) {
                index = currentOffset(meta) <= 1 ? max : 0;
            } else {
                index = i;
            }

            const offset = offsetFor(meta, index);
            if (animate) {
                animating = true;
                applyTransform(offset, true);
                window.setTimeout(() => { animating = false; }, 1100);
            } else {
                applyTransform(offset, false);
            }
        };

        let meta = layout();
        syncNav(meta);
        applyTransform(0, false);

        const go = (dir) => {
            meta = layout();
            if (!syncNav(meta)) {
                index = 0;
                applyTransform(0, false);
                return;
            }
            scrollToIndex(index + dir, meta, true);
        };

        prev?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            go(-1);
        });
        next?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            go(1);
        });

        window.addEventListener('resize', () => {
            meta = layout();
            syncNav(meta);
            const max = maxIndex(meta);
            if (index > max) index = max;
            scrollToIndex(index, meta, false);
        });

        // Autoplay: advance until the last card is fully visible, then restart.
        let timer = null;
        const enableAuto = wrap.getAttribute('data-autoplay') === '1';
        const start = () => {
            stop();
            if (!enableAuto) return;
            meta = layout();
            if (!syncNav(meta)) return;
            timer = window.setInterval(() => {
                meta = layout();
                if (!syncNav(meta)) {
                    stop();
                    return;
                }
                const max = maxIndex(meta);
                const maxOff = maxScrollOffset(meta);
                if (index >= max || currentOffset(meta) >= maxOff - 1) {
                    scrollToIndex(0, meta, true);
                } else {
                    scrollToIndex(index + 1, meta, true);
                }
            }, 2800);
        };
        const stop = () => { if (timer) { clearInterval(timer); timer = null; } };
        wrap.addEventListener('mouseenter', stop);
        wrap.addEventListener('mouseleave', start);
        wrap.addEventListener('focusin', stop);
        wrap.addEventListener('focusout', start);
        window.addEventListener('load', () => { meta = layout(); syncNav(meta); scrollToIndex(index, meta, false); start(); });
        window.setTimeout(() => { meta = layout(); syncNav(meta); scrollToIndex(index, meta, false); start(); }, 120);
        track.querySelectorAll('img').forEach((img) => {
            if (!img.complete) {
                img.addEventListener('load', () => {
                    meta = layout();
                    syncNav(meta);
                    scrollToIndex(index, meta, false);
                }, { once: true });
            }
        });
    }

    document.querySelectorAll('[data-snap-slider-wrap]').forEach((wrap) => setupSnapSlider(wrap));
})();
</script>

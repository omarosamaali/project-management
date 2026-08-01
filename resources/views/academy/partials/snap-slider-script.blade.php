<script>
(function () {
    function animateScroll(track, toLeft, duration = 900) {
        const from = track.scrollLeft;
        const change = toLeft - from;
        if (Math.abs(change) < 1) return Promise.resolve();
        const start = performance.now();
        return new Promise((resolve) => {
            function frame(now) {
                const t = Math.min(1, (now - start) / duration);
                const eased = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
                track.scrollLeft = from + change * eased;
                if (t < 1) requestAnimationFrame(frame);
                else resolve();
            }
            requestAnimationFrame(frame);
        });
    }

    function setupSnapSlider(wrap) {
        const track = wrap.querySelector('[data-snap-slider]');
        const viewport = wrap.querySelector('.snap-slider-viewport');
        const prev = wrap.querySelector('[data-snap-prev]');
        const next = wrap.querySelector('[data-snap-next]');
        if (!track || !viewport) return;

        const minSlide = Math.max(160, parseFloat(wrap.getAttribute('data-min-slide')) || 200);
        const fixedSlide = parseFloat(wrap.getAttribute('data-fixed-slide')) || 0;
        const maxPerView = Math.max(1, parseInt(wrap.getAttribute('data-max-per-view') || '0', 10) || 0);
        const reserveSlots = Math.max(0, parseInt(wrap.getAttribute('data-reserve-slots') || '0', 10) || 0);
        let animating = false;
        let index = 0;

        const slides = () => Array.from(track.querySelectorAll('.snap-slide'));
        const isRtl = () =>
            getComputedStyle(document.documentElement).direction === 'rtl'
            || getComputedStyle(track).direction === 'rtl';

        const layout = () => {
            const list = slides();
            if (!list.length) return { perView: 1, step: minSlide, count: 0 };
            const styles = getComputedStyle(track);
            const gapVal = parseFloat(styles.columnGap || styles.gap) || 14.4;
            const vw = viewport.clientWidth;
            const maxSlide = Math.max(140, vw);

            if (reserveSlots > 0) {
                const maxFit = Math.max(1, Math.floor((vw + gapVal) / (minSlide + gapVal)));
                const slots = Math.min(reserveSlots, maxFit);
                const slideW = (vw - gapVal * Math.max(0, slots - 1)) / slots;
                list.forEach((el) => {
                    el.style.flex = `0 0 ${slideW}px`;
                    el.style.width = `${slideW}px`;
                    el.style.minWidth = `${slideW}px`;
                    el.style.maxWidth = `${slideW}px`;
                });
                return { perView: slots, step: slideW + gapVal, count: list.length };
            }

            const baseW = fixedSlide > 0 ? Math.min(fixedSlide, maxSlide) : Math.min(minSlide, maxSlide);
            let perView = Math.max(1, Math.floor((vw + gapVal) / (baseW + gapVal)));
            if (maxPerView > 0) perView = Math.min(perView, maxPerView);
            if (list.length <= perView) perView = list.length;
            const slideW = Math.min(
                maxSlide,
                fixedSlide > 0
                    ? fixedSlide
                    : (perView > 0 ? (vw - gapVal * Math.max(0, perView - 1)) / perView : vw)
            );
            list.forEach((el) => {
                el.style.flex = `0 0 ${slideW}px`;
                el.style.width = `${slideW}px`;
                el.style.minWidth = `${slideW}px`;
                el.style.maxWidth = `${slideW}px`;
            });
            return { perView, step: slideW + gapVal, count: list.length };
        };

        const maxIndex = (meta) => Math.max(0, meta.count - meta.perView);

        const syncNav = (meta) => {
            const show = meta.count > meta.perView;
            if (prev) prev.hidden = !show;
            if (next) next.hidden = !show;
            wrap.classList.toggle('is-nav-hidden', !show);
            return show;
        };

        const scrollToIndex = async (i, meta) => {
            if (animating) return;
            const max = maxIndex(meta);
            index = ((i % (max + 1)) + (max + 1)) % (max + 1);
            const rtl = isRtl();
            const target = rtl ? -(index * meta.step) : index * meta.step;
            animating = true;
            await animateScroll(track, target, 1100);
            animating = false;
        };

        let meta = layout();
        syncNav(meta);

        const go = (dir) => {
            meta = layout();
            if (!syncNav(meta)) return;
            scrollToIndex(index + dir, meta);
        };

        prev?.addEventListener('click', () => go(-1));
        next?.addEventListener('click', () => go(1));
        window.addEventListener('resize', () => {
            meta = layout();
            syncNav(meta);
            const max = maxIndex(meta);
            if (index > max) index = max;
            const rtl = isRtl();
            track.scrollLeft = rtl ? -(index * meta.step) : index * meta.step;
        });

        let timer = null;
        const enableAuto = wrap.getAttribute('data-autoplay') === '1';
        const start = () => {
            stop();
            if (!enableAuto) return;
            meta = layout();
            if (!syncNav(meta)) return;
            timer = setInterval(() => go(1), 2000);
        };
        const stop = () => { if (timer) clearInterval(timer); timer = null; };
        wrap.addEventListener('mouseenter', stop);
        wrap.addEventListener('mouseleave', start);
        window.addEventListener('load', () => { meta = layout(); syncNav(meta); start(); });
        setTimeout(() => { meta = layout(); syncNav(meta); start(); }, 120);
        track.querySelectorAll('img').forEach((img) => {
            if (!img.complete) img.addEventListener('load', () => { meta = layout(); syncNav(meta); }, { once: true });
        });
    }

    document.querySelectorAll('[data-snap-slider-wrap]').forEach((wrap) => setupSnapSlider(wrap));
})();
</script>

<script>
(function () {
    // Count-up anywhere on academy routes (hero floats live outside .academy-page)
    document.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseFloat(el.getAttribute('data-count') || '0');
        if (!target) return;
        const suffix = el.getAttribute('data-suffix') || '';
        let done = false;
        const run = () => {
            if (done) return;
            done = true;
            const start = performance.now();
            const dur = 1100;
            const tick = (now) => {
                const t = Math.min(1, (now - start) / dur);
                const eased = 1 - Math.pow(1 - t, 3);
                const val = Math.round(target * eased);
                el.textContent = val + suffix;
                if (t < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    run();
                    io.disconnect();
                }
            }, { threshold: 0.4 });
            io.observe(el);
        } else run();
    });

    const page = document.querySelector('.academy-page');
    if (!page) return;

    document.querySelectorAll('.academy-page .soni-grid, .academy-page .trust-grid, .academy-page .academy-steps')
        .forEach((el) => el.classList.add('reveal-stagger'));

    const nodes = document.querySelectorAll('.academy-page .reveal');
    if (!('IntersectionObserver' in window)) {
        nodes.forEach((n) => n.classList.add('is-in'));
    } else {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                e.target.classList.add('is-in');
                e.target.querySelectorAll('.reveal-stagger > *').forEach((child, i) => {
                    child.style.transitionDelay = `${0.05 + i * 0.07}s`;
                    child.classList.add('reveal', 'is-in');
                });
                io.unobserve(e.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
        nodes.forEach((n) => io.observe(n));
    }

    const sections = Array.from(document.querySelectorAll('.academy-page section[id]'));
    if (sections.length) {
        const dots = document.createElement('div');
        dots.className = 'academy-dots';
        dots.setAttribute('aria-hidden', 'true');
        sections.forEach((sec) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'academy-dot';
            btn.title = sec.id;
            btn.addEventListener('click', () => {
                sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            dots.appendChild(btn);
        });
        document.body.appendChild(dots);
        const dotBtns = Array.from(dots.children);
        const syncDots = () => {
            let active = 0;
            const mid = window.innerHeight * 0.35;
            sections.forEach((sec, i) => {
                const r = sec.getBoundingClientRect();
                if (r.top <= mid && r.bottom > mid) active = i;
            });
            dotBtns.forEach((d, i) => d.classList.toggle('is-active', i === active));
        };
        window.addEventListener('scroll', syncDots, { passive: true });
        syncDots();
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion) {
        document.querySelectorAll('.academy-page .soni-card').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - 0.5;
                const y = (e.clientY - r.top) / r.height - 0.5;
                card.style.transform = `translateY(-6px) rotateX(${(-y * 4).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
            });
            card.addEventListener('pointerleave', () => {
                card.style.transform = '';
            });
        });
    }

    document.querySelectorAll('.academy-page .snap-nav').forEach((btn) => {
        btn.addEventListener('pointermove', (e) => {
            const r = btn.getBoundingClientRect();
            const x = e.clientX - r.left - r.width / 2;
            const y = e.clientY - r.top - r.height / 2;
            btn.style.translate = `${x * 0.2}px ${y * 0.2}px`;
        });
        btn.addEventListener('pointerleave', () => {
            btn.style.translate = '';
        });
    });

    // Sticky section rail active state
    const railLinks = Array.from(document.querySelectorAll('.academy-rail-link'));
    if (railLinks.length && 'IntersectionObserver' in window) {
        const map = new Map();
        railLinks.forEach((a) => {
            const id = (a.getAttribute('href') || '').replace('#', '');
            const sec = id ? document.getElementById(id) : null;
            if (sec) map.set(sec, a);
        });
        const rio = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                railLinks.forEach((l) => l.classList.remove('is-active'));
                const link = map.get(e.target);
                if (link) link.classList.add('is-active');
            });
        }, { rootMargin: '-35% 0px -50% 0px', threshold: 0.01 });
        map.forEach((_, sec) => rio.observe(sec));
    }
})();
</script>

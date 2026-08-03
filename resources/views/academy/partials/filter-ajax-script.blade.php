<script>
(function () {
    const listing = document.querySelector('[data-academy-listing]');
    if (!listing) return;

    let busy = false;
    let abortCtrl = null;

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    function keepScroll(y) {
        const lock = () => window.scrollTo(0, y);
        lock();
        requestAnimationFrame(lock);
        setTimeout(lock, 0);
        setTimeout(lock, 50);
        setTimeout(lock, 120);
    }

    function setBusy(on) {
        busy = on;
        listing.classList.toggle('is-filtering', on);
        listing.setAttribute('aria-busy', on ? 'true' : 'false');
    }

    function sameOrigin(url) {
        try {
            const u = new URL(url, window.location.href);
            return u.origin === window.location.origin;
        } catch (e) {
            return false;
        }
    }

    async function loadListing(url, { push = true } = {}) {
        if (!url || busy) return;
        if (!sameOrigin(url)) {
            window.location.href = url;
            return;
        }

        const nextUrl = new URL(url, window.location.href);
        const current = new URL(window.location.href);
        if (nextUrl.pathname === current.pathname && nextUrl.search === current.search) {
            return;
        }

        const scrollY = window.scrollY || window.pageYOffset || 0;
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();
        setBusy(true);

        try {
            const res = await fetch(nextUrl.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin',
                signal: abortCtrl.signal,
            });

            if (!res.ok) throw new Error('filter failed');

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const incoming = doc.querySelector('[data-academy-listing]');
            if (!incoming) throw new Error('listing missing');

            listing.innerHTML = incoming.innerHTML;

            if (push) {
                history.pushState({ academyFilter: true }, '', nextUrl.toString());
            }

            if (window.AcademySnapSlider && typeof window.AcademySnapSlider.init === 'function') {
                window.AcademySnapSlider.init(listing);
            }

            const title = doc.querySelector('title');
            if (title && title.textContent) {
                document.title = title.textContent;
            }
        } catch (err) {
            if (err && err.name === 'AbortError') return;
            console.error(err);
            window.location.href = nextUrl.toString();
            return;
        } finally {
            setBusy(false);
            keepScroll(scrollY);
        }
    }

    listing.addEventListener('click', function (e) {
        const a = e.target.closest('a');
        if (!a || !listing.contains(a)) return;
        if (a.target && a.target !== '_self') return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const isFilterLink =
            a.classList.contains('academy-chip')
            || a.classList.contains('cat-slide')
            || a.closest('.pagination, nav[role="navigation"]');

        if (!isFilterLink) return;

        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        e.preventDefault();
        loadListing(href, { push: true });
    });

    listing.addEventListener('submit', function (e) {
        const form = e.target.closest('form.academy-search');
        if (!form || !listing.contains(form)) return;
        e.preventDefault();

        const action = form.getAttribute('action') || window.location.pathname;
        const params = new URLSearchParams(new FormData(form));
        // Drop empty query values so URLs stay clean.
        [...params.keys()].forEach((key) => {
            if ((params.get(key) || '').trim() === '') params.delete(key);
        });
        const qs = params.toString();
        const url = qs ? `${action}?${qs}` : action;
        loadListing(url, { push: true });
    });

    window.addEventListener('popstate', function () {
        loadListing(window.location.href, { push: false });
    });
})();
</script>
<style>
    [data-academy-listing].is-filtering {
        pointer-events: none;
    }
    [data-academy-listing].is-filtering .academy-toolbar,
    [data-academy-listing].is-filtering .soni-grid,
    [data-academy-listing].is-filtering .snap-slider-wrap,
    [data-academy-listing].is-filtering .text-center.py-16,
    [data-academy-listing].is-filtering .mt-8 {
        opacity: .55;
        transition: opacity .18s ease;
    }
</style>

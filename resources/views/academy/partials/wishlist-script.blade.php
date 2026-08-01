<script>
(function () {
    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function applyState(btn, wishlisted) {
        btn.classList.toggle('is-on', wishlisted);
        btn.dataset.wishlisted = wishlisted ? '1' : '0';
        btn.setAttribute('aria-pressed', wishlisted ? 'true' : 'false');
        const icon = btn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fas', wishlisted);
            icon.classList.toggle('far', !wishlisted);
        }
        const label = wishlisted
            ? @json(__('messages.academy_wishlist_remove'))
            : @json(__('messages.academy_wishlist_add'));
        btn.setAttribute('aria-label', label);
        btn.setAttribute('title', label);
        const labelEl = btn.querySelector('[data-wishlist-label]');
        if (labelEl) labelEl.textContent = label;
    }

    async function toggleWishlist(btn) {
        if (btn.classList.contains('is-busy')) return;
        const url = btn.getAttribute('data-url');
        const loginUrl = btn.getAttribute('data-login-url');
        if (!url) return;

        btn.classList.add('is-busy');
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (res.status === 401) {
                const data = await res.json().catch(() => ({}));
                window.location.href = data.login_url || loginUrl || @json(\App\Support\AuthUi::loginUrl());
                return;
            }

            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'wishlist failed');
            }

            applyState(btn, !!data.wishlisted);

            // On wishlist page, remove card when unfavorited.
            if (!data.wishlisted && btn.closest('[data-wishlist-page]')) {
                const card = btn.closest('.soni-card');
                if (card) {
                    card.style.transition = 'opacity .25s, transform .25s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(.96)';
                    setTimeout(() => {
                        card.remove();
                        const grid = document.querySelector('[data-wishlist-grid]');
                        if (grid && !grid.querySelector('.soni-card')) {
                            window.location.reload();
                        }
                    }, 260);
                }
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.classList.remove('is-busy');
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-wishlist-toggle]');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        toggleWishlist(btn);
    });
})();
</script>

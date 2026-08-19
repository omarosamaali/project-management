<script>
document.addEventListener('DOMContentLoaded', function () {
    const FOCUS_KEY = 'evorq_search_focus';

    function normalizePath(url) {
        try {
            return new URL(url || '', window.location.origin).pathname;
        } catch (_) {
            return String(url || window.location.pathname).split('?')[0];
        }
    }

    function saveSearchFocus(input, form) {
        try {
            sessionStorage.setItem(FOCUS_KEY, JSON.stringify({
                inputId: input.id || '',
                inputName: input.name || 'search',
                formAction: normalizePath(form.getAttribute('action') || window.location.pathname),
                cursor: typeof input.selectionStart === 'number' ? input.selectionStart : input.value.length,
            }));
        } catch (_) {}
    }

    function restoreSearchFocus() {
        let raw = null;
        try {
            raw = sessionStorage.getItem(FOCUS_KEY);
        } catch (_) {
            return;
        }
        if (!raw) return;

        try {
            sessionStorage.removeItem(FOCUS_KEY);
        } catch (_) {}

        let data;
        try {
            data = JSON.parse(raw);
        } catch (_) {
            return;
        }

        let input = data.inputId ? document.getElementById(data.inputId) : null;
        if (!input && data.inputName) {
            document.querySelectorAll('form[method="GET"], form[method="get"]').forEach((form) => {
                if (input) return;
                const formPath = normalizePath(form.getAttribute('action') || window.location.pathname);
                if (formPath !== data.formAction && window.location.pathname !== data.formAction) return;
                input = form.querySelector(`input[name="${CSS.escape(data.inputName)}"]`);
            });
        }

        if (!input) return;

        requestAnimationFrame(() => {
            input.focus({ preventScroll: true });
            const pos = typeof data.cursor === 'number' ? data.cursor : input.value.length;
            try {
                input.setSelectionRange(pos, pos);
            } catch (_) {}
        });
    }

    restoreSearchFocus();

    document.querySelectorAll('form[method="GET"], form[method="get"]').forEach((form) => {
        const searchInput = form.querySelector('input[name="search"], input[type="search"][name]');
        if (!searchInput || form.dataset.noAutosearch === '1') return;

        let timer = null;
        let touched = false;
        let lastValue = searchInput.value;

        const triggerSubmit = () => {
            if (!touched) return;
            const currentValue = searchInput.value;
            if (currentValue === lastValue) return;
            lastValue = currentValue;
            saveSearchFocus(searchInput, form);
            form.submit();
        };

        const scheduleSubmit = () => {
            touched = true;
            if (timer) clearTimeout(timer);
            timer = setTimeout(triggerSubmit, 500);
        };

        searchInput.addEventListener('input', scheduleSubmit);
        searchInput.addEventListener('change', scheduleSubmit);
        form.querySelectorAll('button, [role="button"], a').forEach((el) => {
            el.addEventListener('click', scheduleSubmit);
        });
    });
});
</script>

<div id="evorq-dialog-root" class="fixed inset-0 z-[120] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/55" data-evorq-dialog-backdrop></div>
    <div class="relative mx-auto mt-24 w-[92%] max-w-md rounded-2xl bg-white shadow-2xl border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 id="evorq-dialog-title" class="text-lg font-bold text-slate-900">تنبيه</h3>
        </div>
        <div class="px-5 py-4">
            <p id="evorq-dialog-message" class="text-sm leading-7 text-slate-600 whitespace-pre-line"></p>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
            <button type="button" id="evorq-dialog-cancel"
                class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 hidden"
                data-default-label="{{ __('messages.cancel') }}">
                {{ __('messages.cancel') }}
            </button>
            <button type="button" id="evorq-dialog-ok"
                class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700"
                data-default-label="{{ __('messages.ok') }}">
                {{ __('messages.ok') }}
            </button>
        </div>
    </div>
</div>

<script>
(() => {
    if (window.__evorqDialogReady) return;
    window.__evorqDialogReady = true;

    const root = document.getElementById('evorq-dialog-root');
    if (!root) return;

    const titleEl = document.getElementById('evorq-dialog-title');
    const messageEl = document.getElementById('evorq-dialog-message');
    const okBtn = document.getElementById('evorq-dialog-ok');
    const cancelBtn = document.getElementById('evorq-dialog-cancel');
    const backdrop = root.querySelector('[data-evorq-dialog-backdrop]');
    const defaultOkLabel = okBtn?.dataset.defaultLabel || 'OK';
    const defaultCancelLabel = cancelBtn?.dataset.defaultLabel || 'Cancel';

    let activeResolver = null;
    let activeType = 'alert';

    const close = (result) => {
        root.classList.add('hidden');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        const resolver = activeResolver;
        activeResolver = null;
        if (resolver) resolver(result);
    };

    const open = (type, message, opts = {}) => {
        activeType = type;
        titleEl.textContent = opts.title || (type === 'confirm' ? 'تأكيد' : 'تنبيه');
        messageEl.textContent = String(message ?? '');
        okBtn.textContent = opts.okText || defaultOkLabel;
        cancelBtn.textContent = opts.cancelText || defaultCancelLabel;
        cancelBtn.classList.toggle('hidden', type !== 'confirm');

        root.classList.remove('hidden');
        root.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        return new Promise((resolve) => {
            activeResolver = resolve;
        });
    };

    okBtn.addEventListener('click', () => close(true));
    cancelBtn.addEventListener('click', () => close(false));
    backdrop?.addEventListener('click', () => close(activeType === 'confirm' ? false : true));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !root.classList.contains('hidden')) {
            close(activeType === 'confirm' ? false : true);
        }
    });

    window.evorqAlert = (message, opts = {}) => open('alert', message, opts);
    window.evorqConfirm = (message, opts = {}) => open('confirm', message, opts);

    // Replace native blocking alert with custom modal UI.
    window.alert = function (message) {
        window.evorqAlert(message);
    };

    const decodeJsString = (raw) => {
        const v = String(raw || '').trim();
        if ((v.startsWith('"') && v.endsWith('"')) || (v.startsWith("'") && v.endsWith("'"))) {
            return v.slice(1, -1)
                .replace(/\\"/g, '"')
                .replace(/\\'/g, "'")
                .replace(/\\n/g, '\n');
        }
        return v;
    };

    const extractConfirmMessage = (handlerCode) => {
        const code = String(handlerCode || '');
        const match = code.match(/confirm\s*\(([\s\S]*?)\)/i);
        if (!match) return null;
        const parsed = decodeJsString(match[1]);
        return parsed || 'Are you sure?';
    };

    const enhanceInlineConfirmHandlers = () => {
        document.querySelectorAll('[onsubmit*="confirm("]').forEach((form) => {
            if (!(form instanceof HTMLFormElement) || form.dataset.evorqConfirmBound === '1') return;
            const inline = form.getAttribute('onsubmit') || '';
            const message = extractConfirmMessage(inline);
            if (!message) return;

            form.removeAttribute('onsubmit');
            form.dataset.evorqConfirmBound = '1';
            form.addEventListener('submit', async (e) => {
                if (form.dataset.evorqConfirmBypass === '1') {
                    form.dataset.evorqConfirmBypass = '0';
                    return;
                }
                e.preventDefault();
                const ok = await window.evorqConfirm(message);
                if (!ok) return;
                form.dataset.evorqConfirmBypass = '1';
                HTMLFormElement.prototype.submit.call(form);
            });
        });

        document.querySelectorAll('[onclick*="confirm("]').forEach((el) => {
            if (el.dataset.evorqConfirmBound === '1') return;
            const inline = el.getAttribute('onclick') || '';
            const message = extractConfirmMessage(inline);
            if (!message) return;

            el.removeAttribute('onclick');
            el.dataset.evorqConfirmBound = '1';
            el.addEventListener('click', async (e) => {
                e.preventDefault();
                const ok = await window.evorqConfirm(message);
                if (!ok) return;

                if (el instanceof HTMLAnchorElement && el.href) {
                    window.location.href = el.href;
                    return;
                }

                if (el instanceof HTMLButtonElement && el.type === 'submit' && el.form) {
                    HTMLFormElement.prototype.submit.call(el.form);
                    return;
                }
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceInlineConfirmHandlers);
    } else {
        enhanceInlineConfirmHandlers();
    }
})();
</script>

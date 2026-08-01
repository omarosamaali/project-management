<script>
(function () {
    function money(n) {
        return (Math.round((Number(n) + Number.EPSILON) * 100) / 100).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    window.setupTrainerProfitPreview = function setupTrainerProfitPreview() {
        const priceInput = document.getElementById('price');
        const box = document.getElementById('trainer_profit_preview');
        const freeToggle = document.getElementById('is_free_toggle');
        if (!priceInput || !box || box.dataset.bound === '1') return;
        box.dataset.bound = '1';

        const trainerPct = parseFloat(box.dataset.trainerPct || '50') || 0;
        const platformPct = parseFloat(box.dataset.platformPct || String(100 - trainerPct)) || 0;
        const rateUrl = box.dataset.rateUrl || '';

        const priceEl = document.getElementById('tp_price');
        const feeEl = document.getElementById('tp_fee');
        const profitEl = document.getElementById('tp_profit');
        const egpEl = document.getElementById('tp_egp');
        const rateEl = document.getElementById('tp_rate');

        let egpRate = null;
        let rateLoading = false;
        let rateFailed = false;

        async function ensureRate() {
            if (egpRate !== null || rateLoading || rateFailed || !rateUrl) return;
            rateLoading = true;
            try {
                const res = await fetch(rateUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (res.ok && data && data.ok && Number(data.rate) > 0) {
                    egpRate = Number(data.rate);
                } else {
                    rateFailed = true;
                }
            } catch (e) {
                rateFailed = true;
            } finally {
                rateLoading = false;
                render();
            }
        }

        function render() {
            const isFree = freeToggle && freeToggle.checked;
            const price = parseFloat(String(priceInput.value || '').trim());

            if (isFree || !Number.isFinite(price) || price <= 0) {
                box.classList.add('hidden');
                return;
            }

            box.classList.remove('hidden');
            const platformFee = price * (platformPct / 100);
            const profit = price * (trainerPct / 100);

            if (priceEl) priceEl.textContent = money(price);
            if (feeEl) feeEl.textContent = '− ' + money(platformFee);
            if (profitEl) profitEl.textContent = money(profit);

            if (egpEl && rateEl) {
                if (egpRate !== null) {
                    egpEl.textContent = '≈ ' + money(profit * egpRate) + ' ج.م';
                    rateEl.textContent = money(egpRate) + ' ج.م';
                } else if (rateFailed) {
                    egpEl.textContent = '—';
                    rateEl.textContent = 'تعذر جلب السعر حالياً';
                } else {
                    egpEl.textContent = '—';
                    rateEl.textContent = '…';
                    ensureRate();
                }
            }
        }

        priceInput.addEventListener('input', render);
        priceInput.addEventListener('change', render);
        if (freeToggle) {
            freeToggle.addEventListener('change', render);
        }

        ensureRate();
        render();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.setupTrainerProfitPreview();
        });
    } else {
        window.setupTrainerProfitPreview();
    }
})();
</script>

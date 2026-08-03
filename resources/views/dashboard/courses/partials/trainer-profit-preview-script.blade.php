<script>
(function () {
    function money(n) {
        return (Math.round((Number(n) + Number.EPSILON) * 100) / 100).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function fmtPct(n) {
        const rounded = Math.round((Number(n) + Number.EPSILON) * 100) / 100;
        // Only strip trailing zeros after a decimal (do not turn 60 → 6).
        if (Number.isInteger(rounded)) return String(rounded);
        return String(rounded).replace(/0+$/, '').replace(/\.$/, '');
    }

    window.setupTrainerProfitPreview = function setupTrainerProfitPreview() {
        const priceInput = document.getElementById('price');
        const box = document.getElementById('trainer_profit_preview');
        const freeToggle = document.getElementById('is_free_toggle');
        if (!priceInput || !box || box.dataset.bound === '1') return;
        box.dataset.bound = '1';

        const pctByType = {
            online: parseFloat(box.dataset.pctOnline || '60') || 0,
            recorded: parseFloat(box.dataset.pctRecorded || '50') || 0,
            private: parseFloat(box.dataset.pctPrivate || '70') || 0,
            on_site: box.dataset.pctOnsite === '' || box.dataset.pctOnsite == null
                ? 0
                : (parseFloat(box.dataset.pctOnsite) || 0),
        };
        const rateUrl = box.dataset.rateUrl || '';

        const priceEl = document.getElementById('tp_price');
        const feeEl = document.getElementById('tp_fee');
        const profitEl = document.getElementById('tp_profit');
        const egpEl = document.getElementById('tp_egp');
        const rateEl = document.getElementById('tp_rate');

        let egpRate = null;
        let rateLoading = false;
        let rateFailed = false;

        function selectedType() {
            const checked = document.querySelector('input[name="location_type"]:checked');
            return checked ? checked.value : 'online';
        }

        function currentPct() {
            const type = selectedType();
            return pctByType[type] != null ? pctByType[type] : pctByType.online;
        }

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
            const trainerPct = currentPct();
            const platformPct = Math.max(0, 100 - trainerPct);

            box.dataset.trainerPct = String(trainerPct);
            box.dataset.platformPct = String(platformPct);

            const pctLabel = document.getElementById('tp_trainer_pct_label');
            const platLabel = document.getElementById('tp_platform_pct_label');
            const platLabel2 = document.getElementById('tp_platform_pct_label_2');
            if (pctLabel) pctLabel.textContent = fmtPct(trainerPct);
            if (platLabel) platLabel.textContent = fmtPct(platformPct);
            if (platLabel2) platLabel2.textContent = fmtPct(platformPct);

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
        document.querySelectorAll('input[name="location_type"]').forEach((el) => {
            el.addEventListener('change', render);
        });

        ensureRate();
        render();
    };

    window.setupPrivateTrainerProfitPreview = function setupPrivateTrainerProfitPreview() {
        const priceInput = document.getElementById('private_course_price');
        const box = document.getElementById('private_trainer_profit_preview');
        const allowToggle = document.getElementById('allows_private_requests');
        if (!priceInput || !box || box.dataset.bound === '1') return;
        box.dataset.bound = '1';

        const trainerPct = parseFloat(box.dataset.trainerPct || '70') || 0;
        const platformPct = parseFloat(box.dataset.platformPct || String(100 - trainerPct)) || Math.max(0, 100 - trainerPct);
        const rateUrl = box.dataset.rateUrl || '';

        const priceEl = document.getElementById('tpp_price');
        const feeEl = document.getElementById('tpp_fee');
        const profitEl = document.getElementById('tpp_profit');
        const egpEl = document.getElementById('tpp_egp');
        const rateEl = document.getElementById('tpp_rate');

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
            const allowed = !allowToggle || allowToggle.checked;
            const price = parseFloat(String(priceInput.value || '').trim());

            if (!allowed || !Number.isFinite(price) || price <= 0) {
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
        if (allowToggle) {
            allowToggle.addEventListener('change', render);
        }

        ensureRate();
        render();
        window.__renderPrivateTrainerProfitPreview = render;
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.setupTrainerProfitPreview();
            window.setupPrivateTrainerProfitPreview();
        });
    } else {
        window.setupTrainerProfitPreview();
        window.setupPrivateTrainerProfitPreview();
    }
})();
</script>

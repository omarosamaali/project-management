<script>
(() => {
    async function translateText(text, sourceLang, targetLang) {
        if (!text || !String(text).trim()) return '';
        const cleanText = String(text).trim();
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(cleanText)}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return cleanText;
            const data = await response.json();
            if (data && data[0] && Array.isArray(data[0])) {
                let translatedText = '';
                for (let i = 0; i < data[0].length; i++) {
                    if (data[0][i] && data[0][i][0]) translatedText += data[0][i][0];
                }
                return translatedText.trim() || cleanText;
            }
            return cleanText;
        } catch (error) {
            return cleanText;
        }
    }

    function setupPairTranslation(source, target, fromLang, toLang, delay = 800) {
        if (!source || !target || source.dataset.translateBound === '1') return;
        source.dataset.translateBound = '1';
        let timer = null;
        source.addEventListener('input', (e) => {
            const val = e.target.value;
            if (timer) clearTimeout(timer);
            if (!val.trim()) return;
            timer = setTimeout(async () => {
                const translated = await translateText(val, fromLang, toLang);
                if (translated && translated !== target.value) {
                    target.value = translated;
                }
            }, delay);
        });
    }

    const nameAr = document.getElementById('name_ar');
    const nameEn = document.getElementById('name_en');
    setupPairTranslation(nameAr, nameEn, 'ar', 'en', 800);
    setupPairTranslation(nameEn, nameAr, 'en', 'ar', 800);

    const wrap = document.getElementById('pmFieldsWrap');
    const rows = document.getElementById('pmFieldsRows');
    const addBtn = document.getElementById('pmAddField');
    const template = document.getElementById('pmFieldRowTemplate');

    if (!wrap || !rows) return;

    let counter = rows.querySelectorAll('.pm-field-row').length;

    const bindRemove = (row) => {
        const btn = row.querySelector('.pm-remove-field');
        if (btn) {
            btn.addEventListener('click', () => row.remove());
        }
    };

    rows.querySelectorAll('.pm-field-row').forEach(bindRemove);

    if (!rows.dataset.translateBound) {
        rows.dataset.translateBound = '1';
        rows.addEventListener('input', (e) => {
            const lang = e.target?.dataset?.pmLabel;
            if (lang !== 'ar' && lang !== 'en') return;
            const row = e.target.closest('.pm-field-row');
            if (!row) return;
            const target = row.querySelector(`[data-pm-label="${lang === 'ar' ? 'en' : 'ar'}"]`);
            if (!target) return;
            if (e.target._translateTimer) clearTimeout(e.target._translateTimer);
            const val = e.target.value.trim();
            if (!val) return;
            e.target._translateTimer = setTimeout(async () => {
                const res = await translateText(val, lang, lang === 'ar' ? 'en' : 'ar');
                if (res && res !== target.value) {
                    target.value = res;
                }
            }, 800);
        });
    }

    addBtn?.addEventListener('click', () => {
        if (!template) return;
        const html = template.innerHTML.replaceAll('__INDEX__', 'new_' + (counter++));
        const div = document.createElement('div');
        div.innerHTML = html.trim();
        const row = div.firstElementChild;
        rows.appendChild(row);
        bindRemove(row);
    });
})();
</script>

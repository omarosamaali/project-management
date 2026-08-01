<script>
(function () {
    async function translateText(text, sourceLang, targetLang) {
        const clean = String(text || '').trim();
        if (!clean) return '';
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(clean)}`;
        const res = await fetch(url);
        if (!res.ok) return '';
        const data = await res.json();
        let out = '';
        if (Array.isArray(data?.[0])) {
            data[0].forEach((part) => { if (part?.[0]) out += part[0]; });
        }
        return out.trim() || '';
    }

    function bindPair(fromId, toId, fromLang, toLang) {
        const from = document.getElementById(fromId);
        const to = document.getElementById(toId);
        if (!from || !to) return;
        let timer = null;
        let touched = to.value.trim() !== '';
        to.addEventListener('input', () => { touched = to.value.trim() !== ''; });
        from.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                if (touched && to.value.trim() !== '') return;
                const val = from.value.trim();
                if (!val) return;
                const translated = await translateText(val, fromLang, toLang);
                if (translated) to.value = translated;
            }, 700);
        });
    }

    bindPair('title_ar', 'title_en', 'ar', 'en');
    bindPair('title_en', 'title_ar', 'en', 'ar');
    bindPair('description_ar', 'description_en', 'ar', 'en');
    bindPair('description_en', 'description_ar', 'en', 'ar');
    bindPair('sub_title_ar', 'sub_title_en', 'ar', 'en');
    bindPair('sub_title_en', 'sub_title_ar', 'en', 'ar');
    bindPair('sub_description_ar', 'sub_description_en', 'ar', 'en');
    bindPair('sub_description_en', 'sub_description_ar', 'en', 'ar');
})();
</script>

/**
 * دالة الترجمة المحسنة - تدعم النصوص الطويلة والأسطر المتعددة
 */
async function translateText(text, sourceLang, targetLang) {
    if (!text.trim()) return "";
    
    const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        // جوجل تقسم النص عند الأسطر الجديدة أو الفقرات الطويلة
        // نقوم بجمع كل الأجزاء (parts) لضمان عدم ضياع أي سطر
        if (data && data[0]) {
            return data[0].map(part => part[0]).join('');
        }
        return text;
    } catch (error) {
        console.error('Translation error:', error);
        return text;
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // 1. ترجمة المميزات (Features)
    const featuresContainer = document.getElementById('features-container');
    if (featuresContainer) {
        featuresContainer.addEventListener('input', function (e) {
            const isArabic = e.target.name === 'features_ar[]';
            const isEnglish = e.target.name === 'features_en[]';

            if (isArabic || isEnglish) {
                const row = e.target.closest('.feature-row');
                const targetInput = row.querySelector(`input[name="${isArabic ? 'features_en[]' : 'features_ar[]'}"]`);
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, isArabic ? 'ar' : 'en', isArabic ? 'en' : 'ar');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
        });
    }

    // 2. ترجمة المتطلبات (Requirements)
    const requirementsContainer = document.getElementById('requirements-container');
    if (requirementsContainer) {
        requirementsContainer.addEventListener('input', function (e) {
            const isArabic = e.target.name === 'requirements_ar[]';
            const isEnglish = e.target.name === 'requirements_en[]';

            if (isArabic || isEnglish) {
                const row = e.target.closest('.requirement-row');
                const targetInput = row.querySelector(`input[name="${isArabic ? 'requirements_en[]' : 'requirements_ar[]'}"]`);
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, isArabic ? 'ar' : 'en', isArabic ? 'en' : 'ar');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
        });
    }

    // 3. ترجمة حقول "الاسم" (Name)
    setupAutoTranslation('name_ar', 'name_en', 'ar', 'en');
    setupAutoTranslation('name_en', 'name_ar', 'en', 'ar');

    // 4. ترجمة حقول "الوصف" (Description) - مع مهلة أطول قليلاً للنصوص الطويلة
    setupAutoTranslation('description_ar', 'description_en', 'ar', 'en', 1200);
    setupAutoTranslation('description_en', 'description_ar', 'en', 'ar', 1500);

    /**
     * دالة مساعدة لربط الحقول ببعضها (Helper Function)
     */
    function setupAutoTranslation(sourceId, targetId, fromLang, toLang, delay = 1000) {
        const sourceInput = document.getElementById(sourceId);
        const targetInput = document.getElementById(targetId);
        let timeout;

        if (sourceInput && targetInput) {
            sourceInput.addEventListener('input', function(e) {
                clearTimeout(timeout);
                timeout = setTimeout(async () => {
                    const value = e.target.value.trim();
                    if (value) {
                        targetInput.value = await translateText(value, fromLang, toLang);
                    }
                }, delay);
            });
        }
    }
});
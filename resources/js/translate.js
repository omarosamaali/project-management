async function translateText(text, sourceLang, targetLang) {
    const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        return data[0][0][0];
    } catch (error) {
        console.error('Translation error:', error);
        return text;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    
    // ترجمة المميزات باستخدام Event Delegation
    const featuresContainer = document.getElementById('features-container');
    if (featuresContainer) {
        featuresContainer.addEventListener('input', function (e) {
            // إذا كان الإدخال في حقل عربي
            if (e.target.name === 'features_ar[]') {
                const row = e.target.closest('.feature-row');
                const targetInput = row.querySelector('input[name="features_en[]"]');
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, 'ar', 'en');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
            
            // إذا كان الإدخال في حقل إنجليزي
            if (e.target.name === 'features_en[]') {
                const row = e.target.closest('.feature-row');
                const targetInput = row.querySelector('input[name="features_ar[]"]');
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, 'en', 'ar');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
        });
    }

    // ترجمة المتطلبات باستخدام Event Delegation
    const requirementsContainer = document.getElementById('requirements-container');
    if (requirementsContainer) {
        requirementsContainer.addEventListener('input', function (e) {
            // إذا كان الإدخال في حقل عربي
            if (e.target.name === 'requirements_ar[]') {
                const row = e.target.closest('.requirement-row');
                const targetInput = row.querySelector('input[name="requirements_en[]"]');
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, 'ar', 'en');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
            
            // إذا كان الإدخال في حقل إنجليزي
            if (e.target.name === 'requirements_en[]') {
                const row = e.target.closest('.requirement-row');
                const targetInput = row.querySelector('input[name="requirements_ar[]"]');
                
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(async () => {
                    if (e.target.value.trim()) {
                        const translated = await translateText(e.target.value, 'en', 'ar');
                        targetInput.value = translated;
                    }
                }, 1000);
            }
        });
    }

    // ترجمة الاسم من عربي لإنجليزي
    let nameArTimeout;
    const nameArInput = document.getElementById('name_ar');
    if (nameArInput) {
        nameArInput.addEventListener('input', function(e) {
            clearTimeout(nameArTimeout);
            nameArTimeout = setTimeout(async () => {
                if (e.target.value.trim()) {
                    const translated = await translateText(e.target.value, 'ar', 'en');
                    document.getElementById('name_en').value = translated;
                }
            }, 1000);
        });
    }

    // ترجمة الاسم من إنجليزي لعربي
    let nameEnTimeout;
    const nameEnInput = document.getElementById('name_en');
    if (nameEnInput) {
        nameEnInput.addEventListener('input', function(e) {
            clearTimeout(nameEnTimeout);
            nameEnTimeout = setTimeout(async () => {
                if (e.target.value.trim()) {
                    const translated = await translateText(e.target.value, 'en', 'ar');
                    document.getElementById('name_ar').value = translated;
                }
            }, 1000);
        });
    }

    // ترجمة الوصف من عربي لإنجليزي
    let descArTimeout;
    const descArInput = document.getElementById('description_ar');
    if (descArInput) {
        descArInput.addEventListener('input', function(e) {
            clearTimeout(descArTimeout);
            descArTimeout = setTimeout(async () => {
                if (e.target.value.trim()) {
                    const translated = await translateText(e.target.value, 'ar', 'en');
                    document.getElementById('description_en').value = translated;
                }
            }, 1000);
        });
    }

    // ترجمة الوصف من إنجليزي لعربي
    let descEnTimeout;
    const descEnInput = document.getElementById('description_en');
    if (descEnInput) {
        descEnInput.addEventListener('input', function(e) {
            clearTimeout(descEnTimeout);
            descEnTimeout = setTimeout(async () => {
                if (e.target.value.trim()) {
                    const translated = await translateText(e.target.value, 'en', 'ar');
                    document.getElementById('description_ar').value = translated;
                }
            }, 1500);
        });
    }

});
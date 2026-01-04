document.addEventListener('DOMContentLoaded', function () {
    // إضافة زر جديد
    const addButtonBtn = document.querySelector('.add-button-btn');
    if (addButtonBtn) {
        addButtonBtn.addEventListener('click', function() {
            const container = document.getElementById('buttons-container');
            const newButton = document.createElement('div');
            const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
            
            newButton.className = 'button-row border border-gray-200 rounded-lg p-4 bg-gray-50';
            newButton.innerHTML = `
                <div class="grid md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            محتوى الزر (عربي)
                        </label>
                        <input type="text" name="buttons_text_ar[]"
                            class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="اطلب الآن">
                    </div>
                    <div>
                        <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                            Button Text (English)
                        </label>
                        <input type="text" name="buttons_text_en[]" dir="ltr"
                            class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Order Now">
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            رابط الزر
                        </label>
                        <input type="url" name="buttons_link[]" dir="ltr"
                            class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="https://example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            لون الزر
                        </label>
                        <div class="flex gap-2">
                            <input type="color" name="buttons_color[]" value="${randomColor}"
                                class="button-color-picker w-16 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" name="buttons_color_hex[]" value="${randomColor}" dir="ltr"
                                class="button-color-text flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="#3B82F6" readonly>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-3">
                    <button type="button"
                        class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        حذف الزر
                    </button>
                </div>
            `;
            
            container.appendChild(newButton);
            attachButtonEvents(newButton);
        });
    }

    // حذف زر (باستخدام Delegation)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-button-btn') || e.target.closest('.remove-button-btn')) {
            const buttonRow = e.target.closest('.button-row');
            if (buttonRow) {
                buttonRow.remove();
            }
        }
    });

    // ربط أحداث اختيار اللون (Picker <-> Text)
    function attachButtonEvents(buttonRow) {
        const colorPicker = buttonRow.querySelector('.button-color-picker');
        const colorText = buttonRow.querySelector('.button-color-text');
        
        if (colorPicker && colorText) {
            colorPicker.addEventListener('input', function() {
                colorText.value = this.value;
            });
        }
    }

    // تطبيق الأحداث على الأزرار الموجودة مسبقاً
    document.querySelectorAll('.button-row').forEach(attachButtonEvents);

    // ربط color picker مع text input للأزرار الموجودة مسبقاً (لأزرار التحميل المسبق)
    document.querySelectorAll('input[name="buttons_color[]"]').forEach((picker) => {
        // نستخدم index لتحديد الحقل النصي المقابل
        const parentDiv = picker.closest('.button-row'); 
        const colorText = parentDiv ? parentDiv.querySelector('input[name="buttons_color_hex[]"]') : null;
        
        if (colorText) {
            picker.addEventListener('input', function() {
                colorText.value = this.value;
            });
        }
    });

    // إضافة متطلب
    document.querySelector('.add-requirement-btn')?.addEventListener('click', addRequirement);
    
    // إضافة ميزة
    document.querySelector('.add-feature-btn')?.addEventListener('click', addFeature);
    
    // ربط زر حذف الصورة الرئيسية
    document.getElementById('remove_main_image_btn')?.addEventListener('click', removeMainImage); // <=== تم الإضافة
    
    // حذف متطلب (Delegation)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-requirement-btn')) {
            removeRequirement(e.target.closest('.remove-requirement-btn'));
        }
        
        // حذف ميزة (Delegation)
        if (e.target.closest('.remove-feature-btn')) {
            removeFeature(e.target.closest('.remove-feature-btn'));
        }
    });
    
    // معاينة الصور
    document.getElementById("main_image_input")?.addEventListener("change", function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById("main_image_preview");
        const container = document.getElementById("main_preview_container");

        if (file) {
            preview.src = URL.createObjectURL(file);
            container.classList.remove("hidden");
        }
    });
    
    document.getElementById("extra_images_input")?.addEventListener("change", function (e) {
        const files = [...e.target.files];
        const container = document.getElementById("extra_images_preview");

        container.innerHTML = "";

        files.forEach((file, index) => {
            const wrapper = document.createElement("div");
            wrapper.className = "relative w-20 h-20";

            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            img.className = "w-32 h-full object-cover rounded-lg border";

            const removeBtn = document.createElement("button");
            removeBtn.innerHTML = `<i class="fas fa-times"></i>`;
            removeBtn.className = "absolute top-1 right-1 bg-red-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow hover:bg-red-700 cursor-pointer";

            // تحديد موقع الملف المراد حذفه بدقة (لتجنب مشاكل ترتيب الملفات)
            removeBtn.onclick = () => {
                wrapper.remove();
                const input = document.getElementById("extra_images_input");
                const dt = new DataTransfer();
                
                // إعادة بناء قائمة الملفات باستثناء الملف المحذوف
                const remainingFiles = [...input.files].filter(f => f !== file);
                remainingFiles.forEach(f => dt.items.add(f));
                
                input.files = dt.files;
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            container.appendChild(wrapper);
        });
    });

    // للمتطلبات والمميزات الديناميكية
    document.addEventListener('input', async function(e) {
        let timeout;
        
        // المتطلبات عربي -> إنجليزي
        if (e.target.name === 'requirements_ar[]') {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const parent = e.target.closest('.requirement-row');
                const enInput = parent.querySelector('input[name="requirements_en[]"]');
                if (e.target.value.trim() && enInput) {
                    const translated = await translateText(e.target.value, 'ar', 'en');
                    enInput.value = translated;
                }
            }, 1000);
        }
        
        // المتطلبات إنجليزي -> عربي
        if (e.target.name === 'requirements_en[]') {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const parent = e.target.closest('.requirement-row');
                const arInput = parent.querySelector('input[name="requirements_ar[]"]');
                if (e.target.value.trim() && arInput) {
                    const translated = await translateText(e.target.value, 'en', 'ar');
                    arInput.value = translated;
                }
            }, 1000);
        }
        
        // المميزات عربي -> إنجليزي
        if (e.target.name === 'features_ar[]') {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const parent = e.target.closest('.feature-row');
                const enInput = parent.querySelector('input[name="features_en[]"]');
                if (e.target.value.trim() && enInput) {
                    const translated = await translateText(e.target.value, 'ar', 'en');
                    enInput.value = translated;
                }
            }, 1000);
        }
        
        // المميزات إنجليزي -> عربي
        if (e.target.name === 'features_en[]') {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const parent = e.target.closest('.feature-row');
                const arInput = parent.querySelector('input[name="features_ar[]"]');
                if (e.target.value.trim() && arInput) {
                    const translated = await translateText(e.target.value, 'en', 'ar');
                    arInput.value = translated;
                }
            }, 1000);
        }
    });

});

// هاية DOMContentLoaded

// ========== الدوال برة DOMContentLoaded (لأنها ديناميكية) ==========



/**
 * دالة لإضافة حقل متطلب جديد ديناميكياً
 */
function addRequirement() {
    const container = document.getElementById("requirements-container");
    const newReq = document.createElement("div");
    newReq.className = "flex gap-2 requirement-row mb-3"; // أضفت mb-3 للمسافة
    newReq.innerHTML = `
        <input type="text" name="requirements_ar[]"
            class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="متطلب جديد">
        <input type="text" name="requirements_en[]" dir="ltr"
            class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="New Requirement">
        <button type="button" class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center justify-center">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newReq);
}

/**
 * دالة لحذف حقل متطلب
 */
function removeRequirement(button) {
    const row = button.closest(".requirement-row");
    // يجب التأكد من وجود "requirements-container" لإجراء العد الصحيح
    const container = document.getElementById("requirements-container");
    if (container.querySelectorAll(".requirement-row").length > 1) { 
        row.remove();
    } else {
        alert("يجب أن يكون هناك متطلب واحد على الأقل");
    }
}

/**
 * دالة لإضافة حقل ميزة جديدة ديناميكياً
 */
function addFeature() {
    const container = document.getElementById("features-container");
    const newFeature = document.createElement("div");
    newFeature.className = "flex gap-2 feature-row mb-3"; // أضفت mb-3 للمسافة
    newFeature.innerHTML = `
        <input type="text" name="features_ar[]" 
            class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="ميزة جديدة">
        <input type="text" name="features_en[]" dir="ltr"
            class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="New Feature">
        <button type="button" class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center justify-center">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newFeature);
}

/**
 * دالة لحذف حقل ميزة
 */
function removeFeature(button) {
    const row = button.closest(".feature-row");
    // يجب التأكد من وجود "features-container" لإجراء العد الصحيح
    const container = document.getElementById("features-container");
    if (container.querySelectorAll(".feature-row").length > 1) { 
        row.remove();
    } else {
        alert("يجب أن يكون هناك ميزة واحدة على الأقل");
    }
}

/**
 * دالة لحذف معاينة الصورة الرئيسية
 */
function removeMainImage() {
    const input = document.getElementById("main_image_input");
    const preview = document.getElementById("main_image_preview"); // للتأكد من حذف مصدر الصورة
    const container = document.getElementById("main_preview_container");
    
    // تفريغ قيمة حقل الملف
    input.value = "";
    
    // إخفاء الـ container وإزالة مصدر الصورة من المعاينة
    container.classList.add("hidden");
    preview.src = "";
}
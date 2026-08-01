{{-- Educational path builder for recorded courses. Expects optional $course with units.items.examQuestions.answers --}}
@php
    $pathUnits = old('units');
    if ($pathUnits === null && isset($course)) {
        $pathUnits = $course->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'title_ar' => $unit->title_ar,
                'title_en' => $unit->title_en,
                'items' => $unit->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'title_ar' => $item->title_ar,
                        'title_en' => $item->title_en,
                        'video_path' => $item->video_path,
                        'video_url' => $item->videoUrl(),
                        'video_thumbnail_path' => $item->video_thumbnail_path,
                        'video_thumbnail_url' => $item->thumbnailUrl(),
                        'video_embed_url' => $item->video_embed_url,
                        'video_source' => $item->video_path ? 'upload' : ($item->video_embed_url ? 'embed' : 'upload'),
                        'video_duration_seconds' => $item->video_duration_seconds,
                        'exam_pass_score' => $item->exam_pass_score ?? 1,
                        'exam_duration_minutes' => $item->exam_duration_minutes ?? 30,
                        'questions' => $item->examQuestions->map(function ($q) {
                            return [
                                'question' => $q->question,
                                'answers' => $q->answers->pluck('answer')->values()->all(),
                                'correct' => max(0, (int) $q->answers->search(fn ($a) => $a->is_correct)),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }
    $pathUnits = $pathUnits ?: [];
@endphp

<style>
    .path-unit { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; margin-bottom: .75rem; overflow: hidden; }
    .path-unit-header { display:flex; align-items:center; gap:.75rem; padding:.85rem 1rem; background:#f8fafc; cursor:pointer; }
    .path-unit-num { width:2rem; height:2rem; border-radius:9999px; background:#f59e0b; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.875rem; flex-shrink:0; }
    .path-unit-body { display:none; padding:1rem; border-top:1px solid #e5e7eb; background:#fff; }
    .path-unit.open .path-unit-body { display:block; }
    .path-unit.open .path-chevron { transform: rotate(180deg); }
    .path-item { border:1px solid #e5e7eb; border-radius:.65rem; padding:.85rem; margin-bottom:.65rem; background:#fafafa; }
    .path-item-lesson { border-right: 3px solid #3b82f6; }
    .path-item-exam { border-right: 3px solid #4f46e5; }
    .lesson-video-dropzone.is-dragover,
    .lesson-thumbnail-dropzone.is-dragover { border-color: #3b82f6; background: #eff6ff; }
</style>

<div id="educational-path-builder" class="space-y-4"
    data-duration-endpoint="{{ route('dashboard.courses.resolve-video-duration') }}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-gray-600">أضف وحدات، ثم دروس فيديو أو اختبارات داخل كل وحدة. المتدرب يتقدّم بالترتيب.</p>
            <p class="text-xs text-gray-500 mt-1">
                إجمالي مدة الفيديوهات:
                <strong id="pathTotalDurationLabel">{{ isset($course) ? $course->formattedTotalVideoDuration() : '—' }}</strong>
            </p>
        </div>
        <button type="button" id="addPathUnitBtn"
            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-sm font-medium">
            <i class="fas fa-plus"></i> إضافة وحدة
        </button>
    </div>

    <div id="pathUnitsContainer" class="space-y-3"></div>
</div>

<template id="pathUnitTemplate">
    <div class="path-unit open" data-unit-index="">
        <div class="path-unit-header">
            <span class="path-unit-num">1</span>
            <input type="hidden" class="unit-id-input" value="">
            <div class="flex-1 grid sm:grid-cols-2 gap-2" onclick="event.stopPropagation()">
                <input type="text" class="unit-title-ar path-title-ar px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="عنوان الوحدة بالعربي">
                <input type="text" class="unit-title-en path-title-en px-3 py-2 border border-gray-300 rounded-lg text-sm" dir="ltr"
                    placeholder="Unit title in English">
            </div>
            <button type="button" class="remove-unit-btn px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg" title="حذف الوحدة">
                <i class="fas fa-trash"></i>
            </button>
            <i class="fas fa-chevron-down path-chevron text-gray-400 transition-transform"></i>
        </div>
        <div class="path-unit-body">
            <div class="path-items space-y-2 mb-3"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button type="button"
                    class="add-lesson-btn inline-flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-video"></i> إضافة درس
                </button>
                <button type="button"
                    class="add-exam-btn inline-flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-clipboard-list"></i> إضافة اختبار
                </button>
            </div>
        </div>
    </div>
</template>

<script>
(function () {
    const builder = document.getElementById('educational-path-builder');
    const container = document.getElementById('pathUnitsContainer');
    const unitTpl = document.getElementById('pathUnitTemplate');
    const addUnitBtn = document.getElementById('addPathUnitBtn');
    const totalLabel = document.getElementById('pathTotalDurationLabel');
    if (!container || !unitTpl || !addUnitBtn) return;

    const initialUnits = @json($pathUnits);
    const durationEndpoint = builder?.dataset.durationEndpoint || '';
    const embedTimers = new WeakMap();

    function formatDuration(sec) {
        sec = Math.max(0, Math.round(Number(sec) || 0));
        if (!sec) return '—';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        if (h > 0) return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        return m + ':' + String(s).padStart(2, '0');
    }

    function setLessonDuration(item, sec, statusText) {
        const durationInput = item.querySelector('.lesson-duration-input');
        const label = item.querySelector('.lesson-duration-label');
        const value = Math.max(0, Math.round(Number(sec) || 0));
        if (durationInput) durationInput.value = String(value);
        if (label) {
            label.textContent = statusText || ('المدة: ' + formatDuration(value));
        }
        refreshTotal();
    }

    function reindex() {
        const units = container.querySelectorAll('.path-unit');
        units.forEach((unitEl, uIndex) => {
            unitEl.dataset.unitIndex = String(uIndex);
            unitEl.querySelector('.path-unit-num').textContent = String(uIndex + 1);
            const idInput = unitEl.querySelector('.unit-id-input');
            const titleAr = unitEl.querySelector('.unit-title-ar');
            const titleEn = unitEl.querySelector('.unit-title-en');
            idInput.name = `units[${uIndex}][id]`;
            if (titleAr) titleAr.name = `units[${uIndex}][title_ar]`;
            if (titleEn) titleEn.name = `units[${uIndex}][title_en]`;

            unitEl.querySelectorAll('.path-item').forEach((itemEl, iIndex) => {
                itemEl.querySelectorAll('[data-name]').forEach((field) => {
                    const base = field.getAttribute('data-name');
                    field.name = `units[${uIndex}][items][${iIndex}]${base}`;
                });
            });
        });
        refreshTotal();
    }
    window.reindexEducationalPath = reindex;

    async function pathTranslateText(text, sourceLang, targetLang) {
        if (typeof window.translateText === 'function') {
            return window.translateText(text, sourceLang, targetLang);
        }
        if (!text || !String(text).trim()) return '';
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(String(text).trim())}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return text;
            const data = await response.json();
            if (data && data[0] && Array.isArray(data[0])) {
                let out = '';
                for (let i = 0; i < data[0].length; i++) {
                    if (data[0][i] && data[0][i][0]) out += data[0][i][0];
                }
                return out.trim() || text;
            }
        } catch (_) {}
        return text;
    }

    function schedulePathTitleTranslate(source, target, fromLang, toLang) {
        if (!source || !target) return;
        if (source._pathTranslateTimer) clearTimeout(source._pathTranslateTimer);
        const val = source.value.trim();
        if (!val) return;
        source._pathTranslateTimer = setTimeout(async () => {
            const translated = await pathTranslateText(val, fromLang, toLang);
            if (translated && translated !== target.value) {
                target.value = translated;
            }
        }, 800);
    }

    function refreshTotal() {
        let total = 0;
        container.querySelectorAll('.lesson-duration-input').forEach((el) => {
            total += Math.max(0, parseInt(el.value || '0', 10) || 0);
        });
        if (totalLabel) totalLabel.textContent = formatDuration(total);
    }

    function buildQuestionHtml(qIndex, qData) {
        qData = qData || { question: '', answers: [''], correct: 0 };
        const answers = (qData.answers && qData.answers.length) ? qData.answers : [''];
        let answersHtml = answers.map((ans, ai) => `
            <div class="exam-answer-row flex items-center gap-2 mb-1">
                <input type="radio" data-name="[questions][${qIndex}][correct]" value="${ai}"
                    ${Number(qData.correct) === ai ? 'checked' : ''} class="w-4 h-4 text-green-600" title="الإجابة الصحيحة">
                <input type="text" data-name="[questions][${qIndex}][answers][]" value="${escAttr(ans)}"
                    class="flex-1 px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="نص الإجابة">
                <button type="button" class="remove-path-answer text-red-500 px-1"><i class="fas fa-times"></i></button>
            </div>
        `).join('');

        return `
            <div class="path-exam-question border border-indigo-100 rounded-lg p-3 bg-white mb-2" data-q-index="${qIndex}">
                <div class="flex gap-2 mb-2">
                    <input type="text" data-name="[questions][${qIndex}][question]" value="${escAttr(qData.question || '')}"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="نص السؤال">
                    <button type="button" class="remove-path-question px-2 text-red-600"><i class="fas fa-trash"></i></button>
                </div>
                <div class="path-exam-answers">${answersHtml}</div>
                <button type="button" class="add-path-answer text-xs text-indigo-600 mt-1"><i class="fas fa-plus ml-1"></i>إضافة إجابة</button>
            </div>
        `;
    }

    function escAttr(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');
    }

    function probeHtml5Duration(src) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.preload = 'metadata';
            const timer = setTimeout(() => {
                cleanup();
                resolve(null);
            }, 8000);

            function cleanup() {
                clearTimeout(timer);
                video.removeAttribute('src');
                video.load();
            }

            video.onloadedmetadata = () => {
                const sec = Math.round(video.duration || 0);
                cleanup();
                resolve(Number.isFinite(sec) && sec > 0 ? sec : null);
            };
            video.onerror = () => {
                cleanup();
                resolve(null);
            };
            video.src = src;
        });
    }

    async function resolveEmbedDuration(item, url) {
        url = String(url || '').trim();
        if (!url) {
            setLessonDuration(item, 0);
            return;
        }

        setLessonDuration(item, item.querySelector('.lesson-duration-input')?.value || 0, 'جاري حساب المدة...');

        let sec = await probeHtml5Duration(url);
        if (sec == null && durationEndpoint) {
            try {
                const res = await fetch(durationEndpoint + '?url=' + encodeURIComponent(url), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.seconds != null) sec = Number(data.seconds) || 0;
                }
            } catch (_) { /* ignore */ }
        }

        if (sec == null) {
            setLessonDuration(item, 0, 'تعذّر حساب المدة تلقائياً');
            return;
        }
        setLessonDuration(item, sec);
    }

    function scheduleEmbedDuration(item) {
        const input = item.querySelector('.lesson-embed-input');
        const url = input?.value || '';
        const prev = embedTimers.get(item);
        if (prev) clearTimeout(prev);
        embedTimers.set(item, setTimeout(() => resolveEmbedDuration(item, url), 500));
    }

    function applyVideoFile(item, file) {
        const input = item.querySelector('.lesson-video-input');
        const nameEl = item.querySelector('.lesson-video-filename');
        if (!file || !input) return;

        const maxBytes = 1024 * 1024 * 1024; // 1GB
        if (file.size > maxBytes) {
            alert('حجم فيديو الدرس يجب ألا يتجاوز 1 جيجابايت');
            input.value = '';
            if (nameEl) nameEl.textContent = '';
            return;
        }

        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch (_) {
            // If DataTransfer is unavailable, rely on native input change only
        }

        if (nameEl) nameEl.textContent = file.name;

        const durationUrl = URL.createObjectURL(file);
        const thumbUrl = URL.createObjectURL(file);
        setLessonDuration(item, 0, 'جاري حساب المدة...');
        probeHtml5Duration(durationUrl).then((sec) => {
            try { URL.revokeObjectURL(durationUrl); } catch (_) {}
            setLessonDuration(item, sec || 0, sec ? null : 'تعذّر حساب المدة تلقائياً');
        });
        captureFrameThumbnail(item, thumbUrl);
    }

    function setThumbnailPreview(item, url) {
        const preview = item.querySelector('.lesson-thumbnail-preview');
        const removeWrap = item.querySelector('.lesson-remove-thumb-wrap');
        if (preview) {
            if (url) {
                preview.src = url;
                preview.classList.remove('hidden');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('hidden');
            }
        }
        if (removeWrap) removeWrap.classList.toggle('hidden', !url);
    }

    function applyThumbnailFile(item, file) {
        const input = item.querySelector('.lesson-thumbnail-input');
        const nameEl = item.querySelector('.lesson-thumbnail-filename');
        if (!file || !input) return;

        if (!/^image\/(jpeg|png|webp)$/i.test(file.type) && !/\.(jpe?g|png|webp)$/i.test(file.name)) {
            alert('صيغة صورة المصغّر غير مدعومة (JPG / PNG / WEBP)');
            return;
        }
        const maxBytes = 4 * 1024 * 1024;
        if (file.size > maxBytes) {
            alert('حجم صورة المصغّر يجب ألا يتجاوز 4 ميجابايت');
            return;
        }

        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch (_) {}

        if (nameEl) nameEl.textContent = file.name;
        setThumbnailPreview(item, URL.createObjectURL(file));
        const removeCb = item.querySelector('.lesson-remove-thumbnail');
        if (removeCb) removeCb.checked = false;
    }

    function captureFrameThumbnail(item, objectUrl) {
        const video = document.createElement('video');
        video.preload = 'auto';
        video.muted = true;
        video.playsInline = true;
        video.src = objectUrl;

        const cleanup = () => {
            try { URL.revokeObjectURL(objectUrl); } catch (_) {}
        };

        const fail = () => cleanup();

        video.addEventListener('loadeddata', () => {
            const target = Math.min(1, Math.max(0.1, (video.duration || 2) * 0.08));
            try {
                video.currentTime = target;
            } catch (_) {
                fail();
            }
        }, { once: true });

        video.addEventListener('seeked', () => {
            try {
                const canvas = document.createElement('canvas');
                const w = video.videoWidth || 640;
                const h = video.videoHeight || 360;
                canvas.width = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(video, 0, 0, w, h);
                canvas.toBlob((blob) => {
                    cleanup();
                    if (!blob) return;
                    const thumbInput = item.querySelector('.lesson-thumbnail-input');
                    if (!thumbInput) return;
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(new File([blob], 'thumbnail.jpg', { type: 'image/jpeg' }));
                        thumbInput.files = dt.files;
                    } catch (_) { return; }
                    const previewUrl = URL.createObjectURL(blob);
                    setThumbnailPreview(item, previewUrl);
                    const nameEl = item.querySelector('.lesson-thumbnail-filename');
                    if (nameEl) nameEl.textContent = 'لقطة تلقائية من الفيديو';
                    const removeCb = item.querySelector('.lesson-remove-thumbnail');
                    if (removeCb) removeCb.checked = false;
                }, 'image/jpeg', 0.85);
            } catch (_) {
                fail();
            }
        }, { once: true });

        video.addEventListener('error', fail, { once: true });
    }

    function youtubeIdFromUrl(url) {
        const m = String(url || '').match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/i);
        return m ? m[1] : null;
    }

    function vimeoIdFromUrl(url) {
        const m = String(url || '').match(/vimeo\.com\/(?:video\/)?(\d+)/i);
        return m ? m[1] : null;
    }

    function updateEmbedThumbPreview(item) {
        const input = item.querySelector('.lesson-embed-input');
        const preview = item.querySelector('.lesson-embed-thumb-preview');
        if (!preview) return;
        const url = input?.value || '';
        const yt = youtubeIdFromUrl(url);
        const vim = vimeoIdFromUrl(url);
        if (yt) {
            const maxres = `https://i.ytimg.com/vi/${yt}/maxresdefault.jpg`;
            const hq = `https://i.ytimg.com/vi/${yt}/hqdefault.jpg`;
            preview.onerror = () => {
                preview.onerror = null;
                preview.src = hq;
            };
            preview.src = maxres;
            preview.classList.remove('hidden');
        } else if (vim) {
            preview.src = `https://vumbnail.com/${vim}.jpg`;
            preview.classList.remove('hidden');
        } else {
            preview.removeAttribute('src');
            preview.classList.add('hidden');
        }
    }

    function addItem(unitEl, type, data) {
        data = data || {};
        const itemsWrap = unitEl.querySelector('.path-items');
        const item = document.createElement('div');
        item.className = 'path-item path-item-' + type;
        item.dataset.type = type;

        if (type === 'lesson') {
            item.innerHTML = `
                <input type="hidden" data-name="[id]" value="${escAttr(data.id || '')}">
                <input type="hidden" data-name="[type]" value="lesson">
                <input type="hidden" data-name="[video_duration_seconds]" value="${escAttr(data.video_duration_seconds || 0)}"
                    class="lesson-duration-input">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <i class="fas fa-play-circle text-blue-600"></i>
                    <span class="text-xs font-bold text-blue-700">درس</span>
                    <button type="button" class="remove-item-btn text-red-600 px-2 ms-auto"><i class="fas fa-trash"></i></button>
                </div>
                <div class="grid sm:grid-cols-2 gap-2 mb-3">
                    <input type="text" data-name="[title_ar]" value="${escAttr(data.title_ar || data.title || '')}"
                        class="path-title-ar w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="عنوان الدرس بالعربي">
                    <input type="text" data-name="[title_en]" value="${escAttr(data.title_en || '')}" dir="ltr"
                        class="path-title-en w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Lesson title in English">
                </div>
                <div class="space-y-3">
                    <div class="flex flex-wrap gap-3 text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" data-name="[video_source]" value="upload" class="lesson-source-radio w-4 h-4 text-blue-600"
                                ${(data.video_source || 'upload') !== 'embed' ? 'checked' : ''}>
                            رفع فيديو
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" data-name="[video_source]" value="embed" class="lesson-source-radio w-4 h-4 text-blue-600"
                                ${(data.video_source || 'upload') === 'embed' ? 'checked' : ''}>
                            رابط مضمّن / خارجي
                        </label>
                    </div>
                    <div class="lesson-source-upload ${(data.video_source || 'upload') === 'embed' ? 'hidden' : ''}">
                        <div class="relative lesson-video-dropzone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                            <input type="file" data-name="[video]" accept="video/mp4,video/webm,video/quicktime,video/ogg,.mp4,.webm,.mov,.ogg,.avi"
                                class="lesson-video-input absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600">اضغط أو اسحب فيديو الدرس هنا</p>
                            <p class="text-xs text-gray-400 mt-1">حتى 1 جيجابايت — MP4 / WEBM / MOV</p>
                            <p class="lesson-video-filename text-xs text-blue-600 mt-2 font-medium"></p>
                        </div>
                        ${data.video_url ? `<video src="${escAttr(data.video_url)}" poster="${escAttr(data.video_thumbnail_url || '')}" class="mt-2 w-full max-h-40 rounded border bg-black" controls></video>
                            <p class="text-[11px] text-gray-500 mt-1">رفع ملف جديد يستبدل الفيديو الحالي</p>` : ''}
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-700 mb-2">صورة مصغّرة للفيديو (اختياري)</label>
                            <div class="relative lesson-thumbnail-dropzone border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-500 transition cursor-pointer">
                                <input type="file" data-name="[thumbnail]" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                    class="lesson-thumbnail-input absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <i class="fas fa-image text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">اضغط أو اسحب صورة المصغّر هنا</p>
                                <p class="text-xs text-gray-400 mt-1">JPG / PNG / WEBP — حتى 4 ميجابايت</p>
                                <p class="lesson-thumbnail-filename text-xs text-blue-600 mt-2 font-medium"></p>
                            </div>
                            <div class="mt-2 flex flex-wrap items-start gap-3">
                                <img src="${escAttr(data.video_thumbnail_url || '')}" alt=""
                                    class="lesson-thumbnail-preview w-40 h-24 object-cover rounded border ${data.video_thumbnail_url ? '' : 'hidden'}">
                                <label class="inline-flex items-center gap-1.5 text-xs text-red-600 cursor-pointer ${data.video_thumbnail_url ? '' : 'hidden'} lesson-remove-thumb-wrap">
                                    <input type="checkbox" data-name="[remove_thumbnail]" value="1" class="lesson-remove-thumbnail rounded border-gray-300">
                                    حذف المصغّر
                                </label>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">عند رفع فيديو تُلتقط لقطة تلقائياً — يمكنك استبدالها يدوياً</p>
                        </div>
                    </div>
                    <div class="lesson-source-embed ${(data.video_source || 'upload') === 'embed' ? '' : 'hidden'}">
                        <label class="block text-xs text-gray-500 mb-1">رابط الفيديو (YouTube / Vimeo / رابط ملف مباشر)</label>
                        <input type="url" data-name="[video_embed_url]" value="${escAttr(data.video_embed_url || '')}" dir="ltr"
                            class="lesson-embed-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="https://www.youtube.com/watch?v=... أو https://cdn.example.com/lesson.mp4">
                        <p class="text-[11px] text-gray-500 mt-1">يُشغَّل داخل نفس مشغّل التطبيق — المصغّر من المنصة الأصلية</p>
                        <img src="" alt="" class="lesson-embed-thumb-preview mt-2 w-40 h-24 object-cover rounded border hidden">
                    </div>
                    <p class="text-xs text-gray-500 lesson-duration-label">المدة: ${formatDuration(data.video_duration_seconds || 0)}</p>
                </div>
            `;
        } else {
            const questions = (data.questions && data.questions.length) ? data.questions : [{ question: '', answers: [''], correct: 0 }];
            item.innerHTML = `
                <input type="hidden" data-name="[id]" value="${escAttr(data.id || '')}">
                <input type="hidden" data-name="[type]" value="exam">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <i class="fas fa-clipboard-check text-indigo-600"></i>
                    <span class="text-xs font-bold text-indigo-700">اختبار</span>
                    <button type="button" class="remove-item-btn text-red-600 px-2 ms-auto"><i class="fas fa-trash"></i></button>
                </div>
                <div class="grid sm:grid-cols-2 gap-2 mb-3">
                    <input type="text" data-name="[title_ar]" value="${escAttr(data.title_ar || data.title || '')}"
                        class="path-title-ar w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="عنوان الاختبار بالعربي">
                    <input type="text" data-name="[title_en]" value="${escAttr(data.title_en || '')}" dir="ltr"
                        class="path-title-en w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Exam title in English">
                </div>
                <div class="grid sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">درجة النجاح</label>
                        <input type="number" min="1" data-name="[exam_pass_score]" value="${escAttr(data.exam_pass_score || 1)}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">مدة الاختبار (دقائق)</label>
                        <input type="number" min="1" data-name="[exam_duration_minutes]" value="${escAttr(data.exam_duration_minutes || 30)}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="path-exam-questions mb-2">
                    ${questions.map((q, qi) => buildQuestionHtml(qi, q)).join('')}
                </div>
                <button type="button" class="add-path-question text-xs text-indigo-700 font-medium">
                    <i class="fas fa-plus ml-1"></i>إضافة سؤال
                </button>
            `;
        }

        itemsWrap.appendChild(item);
        if (type === 'lesson') {
            const isEmbed = (data.video_source || 'upload') === 'embed';
            item.querySelectorAll('.lesson-source-upload input').forEach((el) => { el.disabled = isEmbed; });
            item.querySelectorAll('.lesson-source-embed input').forEach((el) => { el.disabled = !isEmbed; });
            if (isEmbed) {
                updateEmbedThumbPreview(item);
                if (data.video_embed_url && !(Number(data.video_duration_seconds) > 0)) {
                    scheduleEmbedDuration(item);
                }
            }
        }
        reindex();
    }

    function addUnit(data) {
        data = data || {};
        const node = unitTpl.content.firstElementChild.cloneNode(true);
        if (data.id) node.querySelector('.unit-id-input').value = data.id;
        const titleAr = node.querySelector('.unit-title-ar');
        const titleEn = node.querySelector('.unit-title-en');
        if (titleAr) titleAr.value = data.title_ar || data.title || '';
        if (titleEn) titleEn.value = data.title_en || '';
        container.appendChild(node);

        (data.items || []).forEach((item) => addItem(node, item.type || 'lesson', item));
        reindex();
    }

    addUnitBtn.addEventListener('click', () => addUnit({ title_ar: '', title_en: '', items: [] }));

    container.addEventListener('click', (e) => {
        const header = e.target.closest('.path-unit-header');
        if (header && !e.target.closest('input,button')) {
            header.closest('.path-unit')?.classList.toggle('open');
        }
        if (e.target.closest('.remove-unit-btn')) {
            e.target.closest('.path-unit')?.remove();
            reindex();
        }
        if (e.target.closest('.add-lesson-btn')) {
            addItem(e.target.closest('.path-unit'), 'lesson', {});
        }
        if (e.target.closest('.add-exam-btn')) {
            addItem(e.target.closest('.path-unit'), 'exam', { questions: [{ question: '', answers: [''], correct: 0 }] });
        }
        if (e.target.closest('.remove-item-btn')) {
            e.target.closest('.path-item')?.remove();
            reindex();
        }
        if (e.target.closest('.add-path-question')) {
            const wrap = e.target.closest('.path-item').querySelector('.path-exam-questions');
            const qi = wrap.querySelectorAll('.path-exam-question').length;
            wrap.insertAdjacentHTML('beforeend', buildQuestionHtml(qi, { question: '', answers: [''], correct: 0 }));
            reindex();
        }
        if (e.target.closest('.remove-path-question')) {
            e.target.closest('.path-exam-question')?.remove();
            reindex();
        }
        if (e.target.closest('.add-path-answer')) {
            const qEl = e.target.closest('.path-exam-question');
            const answers = qEl.querySelector('.path-exam-answers');
            const ai = answers.querySelectorAll('.exam-answer-row').length;
            const qIndex = qEl.dataset.qIndex || 0;
            answers.insertAdjacentHTML('beforeend', `
                <div class="exam-answer-row flex items-center gap-2 mb-1">
                    <input type="radio" data-name="[questions][${qIndex}][correct]" value="${ai}" class="w-4 h-4 text-green-600">
                    <input type="text" data-name="[questions][${qIndex}][answers][]" class="flex-1 px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="نص الإجابة">
                    <button type="button" class="remove-path-answer text-red-500 px-1"><i class="fas fa-times"></i></button>
                </div>
            `);
            reindex();
        }
        if (e.target.closest('.remove-path-answer')) {
            const row = e.target.closest('.exam-answer-row');
            const answers = row?.parentElement;
            if (answers && answers.querySelectorAll('.exam-answer-row').length > 1) {
                row.remove();
                reindex();
            }
        }
    });

    container.addEventListener('dragover', (e) => {
        const zone = e.target.closest('.lesson-video-dropzone, .lesson-thumbnail-dropzone');
        if (!zone) return;
        e.preventDefault();
        zone.classList.add('is-dragover');
    });

    container.addEventListener('dragleave', (e) => {
        const zone = e.target.closest('.lesson-video-dropzone, .lesson-thumbnail-dropzone');
        if (!zone) return;
        if (!zone.contains(e.relatedTarget)) {
            zone.classList.remove('is-dragover');
        }
    });

    container.addEventListener('drop', (e) => {
        const thumbZone = e.target.closest('.lesson-thumbnail-dropzone');
        const videoZone = e.target.closest('.lesson-video-dropzone');
        const zone = thumbZone || videoZone;
        if (!zone) return;
        e.preventDefault();
        zone.classList.remove('is-dragover');
        const item = zone.closest('.path-item');
        const file = e.dataTransfer?.files?.[0];
        if (!item || !file) return;
        if (thumbZone) {
            applyThumbnailFile(item, file);
        } else {
            applyVideoFile(item, file);
        }
    });

    container.addEventListener('change', (e) => {
        const sourceRadio = e.target.closest('.lesson-source-radio');
        if (sourceRadio) {
            const item = sourceRadio.closest('.path-item');
            const isEmbed = sourceRadio.value === 'embed';
            item.querySelector('.lesson-source-upload')?.classList.toggle('hidden', isEmbed);
            item.querySelector('.lesson-source-embed')?.classList.toggle('hidden', !isEmbed);
            item.querySelectorAll('.lesson-source-upload input').forEach((el) => { el.disabled = isEmbed; });
            item.querySelectorAll('.lesson-source-embed input').forEach((el) => { el.disabled = !isEmbed; });
            if (isEmbed) {
                scheduleEmbedDuration(item);
                updateEmbedThumbPreview(item);
            } else {
                const file = item.querySelector('.lesson-video-input')?.files?.[0];
                if (file) applyVideoFile(item, file);
            }
            return;
        }

        const thumbInput = e.target.closest('.lesson-thumbnail-input');
        if (thumbInput?.files?.[0]) {
            applyThumbnailFile(thumbInput.closest('.path-item'), thumbInput.files[0]);
            return;
        }

        const input = e.target.closest('.lesson-video-input');
        if (input?.files?.[0]) {
            applyVideoFile(input.closest('.path-item'), input.files[0]);
        }
    });

    container.addEventListener('input', (e) => {
        const embedInput = e.target.closest('.lesson-embed-input');
        if (embedInput) {
            const item = embedInput.closest('.path-item');
            scheduleEmbedDuration(item);
            updateEmbedThumbPreview(item);
            return;
        }

        const titleAr = e.target.closest('.path-title-ar, .unit-title-ar');
        if (titleAr) {
            const pairRoot = titleAr.closest('.path-item, .path-unit-header, .path-unit');
            const titleEn = pairRoot?.querySelector('.path-title-en, .unit-title-en');
            schedulePathTitleTranslate(titleAr, titleEn, 'ar', 'en');
            return;
        }

        const titleEn = e.target.closest('.path-title-en, .unit-title-en');
        if (titleEn) {
            const pairRoot = titleEn.closest('.path-item, .path-unit-header, .path-unit');
            const titleArEl = pairRoot?.querySelector('.path-title-ar, .unit-title-ar');
            schedulePathTitleTranslate(titleEn, titleArEl, 'en', 'ar');
        }
    });

    if (initialUnits.length) {
        initialUnits.forEach(addUnit);
    } else {
        addUnit({ title_ar: '', title_en: '', items: [] });
    }

    // Ensure field names are assigned before multipart submit (hidden tab / late edits)
    const courseForm = document.getElementById('courseForm');
    if (courseForm && !courseForm.dataset.pathReindexBound) {
        courseForm.dataset.pathReindexBound = '1';
        courseForm.addEventListener('submit', () => {
            reindex();
        });
    }
})();
</script>

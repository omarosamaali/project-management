{{-- Day exams builder (online / on-site only). Expects optional $course. --}}
@php
    $dayExamsLocked = isset($course) && $course->dayExams->contains(fn ($e) => $e->started_at || $e->skipped_at);
    $requiredPass = (int) old('required_exam_pass_count', isset($course) ? ($course->required_exam_pass_count ?? 1) : 1);
    $oldDayExams = old('day_exams');
    if ($oldDayExams === null && isset($course)) {
        $course->loadMissing('dayExams.questions.answers');
        $oldDayExams = $course->dayExams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'day_index' => $exam->day_index,
                'title' => $exam->title,
                'pass_score' => $exam->pass_score,
                'duration_minutes' => $exam->duration_minutes,
                'questions' => $exam->questions->map(function ($q) {
                    return [
                        'question' => $q->question,
                        'answers' => $q->answers->pluck('answer')->values()->all(),
                        'correct' => max(0, (int) $q->answers->search(fn ($a) => $a->is_correct)),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }
    $oldDayExams = $oldDayExams ?: [];

    $hasExamDefault = isset($course)
        ? ((bool) $course->has_exam || count($oldDayExams) > 0)
        : false;
    $hasExam = (bool) old('has_exam', $hasExamDefault);
    if ($dayExamsLocked) {
        $hasExam = true;
    }

    $teachingDayCount = 1;
    if (isset($course) && $course->start_date && $course->end_date) {
        $teachingDayCount = max(1, $course->teachingDays()->count());
    }
@endphp

@include('dashboard.courses.partials.course-switch-styles')

<div id="day-exams-section" class="border rounded-lg p-5 bg-gray-50 space-y-4"
    data-locked="{{ $dayExamsLocked ? '1' : '0' }}"
    data-initial-days="{{ $teachingDayCount }}">
    <div>
        <label for="has_exam_toggle" class="block text-sm font-medium text-gray-700 mb-2">
            هل تحتوي الدورة على اختبارات؟
        </label>
        <label class="course-switch-field cursor-pointer {{ $dayExamsLocked ? 'opacity-60 pointer-events-none' : '' }}">
            <span class="min-w-0">
                <span class="block text-sm text-gray-800">تفعيل اختبارات أيام الدورة</span>
                <span class="block text-xs text-gray-500 mt-0.5">يظهر بناء الاختبارات وعدد النجاح المطلوب للشهادة</span>
            </span>
            <span class="course-switch {{ $dayExamsLocked ? 'is-disabled' : '' }}">
                @if($dayExamsLocked)
                <input type="hidden" name="has_exam" value="1">
                @endif
                <input type="checkbox" name="has_exam" value="1" id="has_exam_toggle"
                    {{ $hasExam ? 'checked' : '' }} {{ $dayExamsLocked ? 'disabled' : '' }}>
                <span class="course-switch-track" aria-hidden="true"></span>
            </span>
        </label>
    </div>

    <div id="day-exams-builder" class="{{ $hasExam ? '' : 'hidden' }} space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3 pt-1 border-t border-gray-200">
            <div>
                <h4 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-[#0b8f7f]"></i>
                    اختبارات أيام الدورة
                </h4>
                <p class="text-xs text-gray-500 mt-1">
                    تُبنى الأيام تلقائياً من تاريخ البداية والنهاية وأيام الراحة. يمكن إضافة اختبار واحد أو أكثر لكل يوم.
                </p>
            </div>
            <div class="min-w-[220px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    عدد الاختبارات المطلوب اجتيازها للشهادة
                </label>
                <input type="number" name="required_exam_pass_count" id="required_exam_pass_count"
                    value="{{ max(1, $requiredPass) }}" min="1" max="{{ max(1, count($oldDayExams) ?: 1) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    {{ $dayExamsLocked ? 'readonly' : '' }}
                    {{ $hasExam ? '' : 'disabled' }}>
                <p class="text-xs text-gray-500 mt-1">الحد الأدنى 1 والحد الأقصى = عدد الاختبارات</p>
            </div>
        </div>

        @if($dayExamsLocked)
        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            <i class="fas fa-lock ml-1"></i>
            تم بدء أو تخطي أحد الاختبارات — لا يمكن تعديل بنية الاختبارات بعد الآن.
        </div>
        @endif

        <div id="day-exams-days" class="space-y-4"></div>

        @unless($dayExamsLocked)
        <p class="text-xs text-gray-500">
            غيّر تواريخ الدورة أو أيام الراحة لتحديث عدد الأيام. الاختبارات على الأيام المحذوفة ستُزال عند الحفظ.
        </p>
        @endunless
    </div>
</div>

<script type="application/json" id="day-exams-initial-data">{!! json_encode($oldDayExams, JSON_UNESCAPED_UNICODE) !!}</script>

<script>
(function () {
    const section = document.getElementById('day-exams-section');
    if (!section || section.dataset.bound === '1') return;
    section.dataset.bound = '1';

    const locked = section.dataset.locked === '1';
    const daysWrap = document.getElementById('day-exams-days');
    const requiredInput = document.getElementById('required_exam_pass_count');
    const initialData = JSON.parse(document.getElementById('day-exams-initial-data')?.textContent || '[]');
    const hasExamToggle = document.getElementById('has_exam_toggle');
    const builder = document.getElementById('day-exams-builder');

    function syncHasExamVisibility() {
        if (!builder || !hasExamToggle) return;
        const enabled = !!hasExamToggle.checked;
        builder.classList.toggle('hidden', !enabled);
        builder.querySelectorAll('input, select, textarea, button').forEach((el) => {
            if (el.id === 'has_exam_toggle') return;
            if (locked && el.hasAttribute('readonly')) {
                el.disabled = !enabled;
                return;
            }
            if (!locked) {
                el.disabled = !enabled;
            }
        });
        if (requiredInput) {
            requiredInput.disabled = !enabled || locked;
            if (locked && enabled) {
                requiredInput.readOnly = true;
                requiredInput.disabled = false;
            }
        }
    }

    if (hasExamToggle) {
        hasExamToggle.addEventListener('change', syncHasExamVisibility);
        syncHasExamVisibility();
    }

    let examSeq = 0;

    function esc(str) {
        return String(str ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function teachingDayCountFromForm() {
        const start = document.getElementById('start_date')?.value;
        const end = document.getElementById('end_date')?.value;
        if (!start || !end) {
            return Math.max(1, parseInt(section.dataset.initialDays || '1', 10) || 1);
        }
        const rest = Array.from(document.querySelectorAll('input[name="rest_days[]"]:checked'))
            .map((el) => el.value.toLowerCase());
        const s = new Date(start);
        const e = new Date(end);
        s.setHours(0, 0, 0, 0);
        e.setHours(0, 0, 0, 0);
        if (e < s) return 1;
        let count = 0;
        const cur = new Date(s);
        const names = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        while (cur <= e) {
            if (!rest.includes(names[cur.getDay()])) count++;
            cur.setDate(cur.getDate() + 1);
        }
        return Math.max(1, count || 1);
    }

    function groupByDay(exams) {
        const map = {};
        (exams || []).forEach((ex) => {
            const d = Math.max(1, parseInt(ex.day_index, 10) || 1);
            if (!map[d]) map[d] = [];
            map[d].push(ex);
        });
        return map;
    }

    function collectCurrentExams() {
        const exams = [];
        daysWrap.querySelectorAll('.day-exam-card').forEach((card) => {
            const dayIndex = parseInt(card.closest('[data-day-index]')?.dataset.dayIndex || '1', 10);
            const idx = card.dataset.examIndex;
            const questions = [];
            card.querySelectorAll('.exam-question-row').forEach((qRow) => {
                const qIdx = qRow.dataset.qIndex;
                const answers = Array.from(qRow.querySelectorAll(`input[name="day_exams[${idx}][questions][${qIdx}][answers][]"]`))
                    .map((i) => i.value);
                const correctEl = qRow.querySelector(`input[name="day_exams[${idx}][questions][${qIdx}][correct]"]:checked`);
                questions.push({
                    question: qRow.querySelector(`input[name="day_exams[${idx}][questions][${qIdx}][question]"]`)?.value || '',
                    answers,
                    correct: correctEl ? parseInt(correctEl.value, 10) : 0,
                });
            });
            exams.push({
                id: card.querySelector(`input[name="day_exams[${idx}][id]"]`)?.value || '',
                day_index: dayIndex,
                title: card.querySelector(`input[name="day_exams[${idx}][title]"]`)?.value || '',
                pass_score: card.querySelector(`input[name="day_exams[${idx}][pass_score]"]`)?.value || 1,
                duration_minutes: card.querySelector(`input[name="day_exams[${idx}][duration_minutes]"]`)?.value || 30,
                questions,
            });
        });
        return exams;
    }

    function updateRequiredMax() {
        const total = daysWrap.querySelectorAll('.day-exam-card').length;
        const max = Math.max(1, total || 1);
        if (requiredInput) {
            requiredInput.max = String(max);
            if (parseInt(requiredInput.value || '1', 10) > max) {
                requiredInput.value = String(max);
            }
            if (parseInt(requiredInput.value || '1', 10) < 1) {
                requiredInput.value = '1';
            }
            requiredInput.disabled = total === 0;
            if (total === 0) requiredInput.value = '1';
        }
    }

    function questionHtml(examIdx, qIdx, q = {}) {
        const answers = (q.answers && q.answers.length) ? q.answers : [''];
        const correct = parseInt(q.correct ?? 0, 10) || 0;
        const answersHtml = answers.map((a, ai) => `
            <div class="exam-answer-row flex items-center gap-2">
                <input type="radio" name="day_exams[${examIdx}][questions][${qIdx}][correct]" value="${ai}"
                    class="w-4 h-4 text-green-600" ${correct === ai ? 'checked' : ''} ${locked ? 'disabled' : ''}>
                <input type="text" name="day_exams[${examIdx}][questions][${qIdx}][answers][]" value="${esc(a)}"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" placeholder="نص الإجابة" ${locked ? 'readonly' : ''}>
                ${locked ? '' : `<button type="button" class="remove-day-exam-answer px-2 py-2 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-times"></i></button>`}
            </div>
        `).join('');

        return `
        <div class="exam-question-row border border-indigo-100 rounded-lg p-3 bg-white" data-q-index="${qIdx}">
            <div class="flex items-start justify-between gap-3 mb-2">
                <label class="block text-sm font-medium text-gray-700 flex-1">
                    سؤال <span class="question-number">${qIdx + 1}</span>
                    <input type="text" name="day_exams[${examIdx}][questions][${qIdx}][question]" value="${esc(q.question || '')}"
                        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="اكتب نص السؤال" ${locked ? 'readonly' : ''}>
                </label>
                ${locked ? '' : `<button type="button" class="remove-day-exam-question mt-6 px-3 py-2 bg-red-500 text-white rounded-lg"><i class="fas fa-trash"></i></button>`}
            </div>
            <div class="exam-answers space-y-2 mb-2">${answersHtml}</div>
            ${locked ? '' : `<button type="button" class="add-day-exam-answer text-sm text-indigo-600 font-medium"><i class="fas fa-plus ml-1"></i> إضافة إجابة</button>`}
        </div>`;
    }

    function examCardHtml(examIdx, dayIndex, exam = {}) {
        const questions = (exam.questions && exam.questions.length) ? exam.questions : [{ question: '', answers: [''], correct: 0 }];
        const questionsHtml = questions.map((q, qi) => questionHtml(examIdx, qi, q)).join('');
        return `
        <div class="day-exam-card border border-gray-200 rounded-lg p-4 bg-white space-y-3" data-exam-index="${examIdx}">
            <input type="hidden" name="day_exams[${examIdx}][id]" value="${esc(exam.id || '')}">
            <input type="hidden" name="day_exams[${examIdx}][day_index]" value="${dayIndex}">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">عنوان الاختبار</label>
                    <input type="text" name="day_exams[${examIdx}][title]" value="${esc(exam.title || '')}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="اختبار اليوم ${dayIndex}" ${locked ? 'readonly' : ''}>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-600 mb-1">المدة (د)</label>
                    <input type="number" min="1" max="600" name="day_exams[${examIdx}][duration_minutes]"
                        value="${esc(exam.duration_minutes || 30)}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" ${locked ? 'readonly' : ''}>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-600 mb-1">درجة النجاح</label>
                    <input type="number" min="1" name="day_exams[${examIdx}][pass_score]"
                        value="${esc(exam.pass_score || 1)}" class="w-full px-3 py-2 border border-gray-300 rounded-lg day-exam-pass-score" ${locked ? 'readonly' : ''}>
                </div>
                ${locked ? '' : `<button type="button" class="remove-day-exam px-3 py-2 bg-red-500 text-white rounded-lg self-end"><i class="fas fa-trash"></i></button>`}
            </div>
            <div class="day-exam-questions space-y-3">${questionsHtml}</div>
            ${locked ? '' : `<button type="button" class="add-day-exam-question text-sm text-indigo-600 font-medium"><i class="fas fa-plus ml-1"></i> إضافة سؤال</button>`}
        </div>`;
    }

    function dayBlockHtml(dayIndex, exams) {
        const cards = (exams && exams.length)
            ? exams.map((ex) => {
                const idx = ++examSeq;
                return examCardHtml(idx, dayIndex, ex);
            }).join('')
            : '';
        return `
        <div class="day-exams-day border border-indigo-100 rounded-xl p-4 bg-indigo-50/40" data-day-index="${dayIndex}">
            <div class="flex items-center justify-between gap-3 mb-3">
                <h4 class="font-semibold text-gray-800">اليوم ${dayIndex}</h4>
                ${locked ? '' : `<button type="button" class="add-day-exam px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700"><i class="fas fa-plus ml-1"></i> إضافة اختبار</button>`}
            </div>
            <div class="day-exams-list space-y-3">${cards || '<p class="text-xs text-gray-500 empty-day-hint">لا يوجد اختبار لهذا اليوم (اختياري)</p>'}</div>
        </div>`;
    }

    function render(exams) {
        const dayCount = teachingDayCountFromForm();
        const byDay = groupByDay(exams);
        examSeq = 0;
        let html = '';
        for (let d = 1; d <= dayCount; d++) {
            html += dayBlockHtml(d, byDay[d] || []);
        }
        daysWrap.innerHTML = html;
        updateRequiredMax();
        syncPassScoreMaxes();
        syncHasExamVisibility();
    }

    function syncPassScoreMaxes() {
        daysWrap.querySelectorAll('.day-exam-card').forEach((card) => {
            const qCount = card.querySelectorAll('.exam-question-row').length || 1;
            const pass = card.querySelector('.day-exam-pass-score');
            if (pass) {
                pass.max = String(qCount);
                if (parseInt(pass.value || '1', 10) > qCount) pass.value = String(qCount);
            }
        });
    }

    function reindexRadios(qRow) {
        const examIdx = qRow.closest('.day-exam-card')?.dataset.examIndex;
        const qIdx = qRow.dataset.qIndex;
        qRow.querySelectorAll('.exam-answer-row').forEach((row, ai) => {
            const radio = row.querySelector('input[type="radio"]');
            if (radio) {
                radio.name = `day_exams[${examIdx}][questions][${qIdx}][correct]`;
                radio.value = String(ai);
            }
        });
    }

    daysWrap.addEventListener('click', (e) => {
        if (locked) return;
        const addExamBtn = e.target.closest('.add-day-exam');
        if (addExamBtn) {
            const day = addExamBtn.closest('[data-day-index]');
            const list = day.querySelector('.day-exams-list');
            list.querySelector('.empty-day-hint')?.remove();
            const idx = ++examSeq;
            list.insertAdjacentHTML('beforeend', examCardHtml(idx, parseInt(day.dataset.dayIndex, 10), {}));
            updateRequiredMax();
            syncPassScoreMaxes();
            return;
        }

        const removeExamBtn = e.target.closest('.remove-day-exam');
        if (removeExamBtn) {
            const day = removeExamBtn.closest('[data-day-index]');
            removeExamBtn.closest('.day-exam-card')?.remove();
            if (day && !day.querySelector('.day-exam-card')) {
                day.querySelector('.day-exams-list').innerHTML = '<p class="text-xs text-gray-500 empty-day-hint">لا يوجد اختبار لهذا اليوم (اختياري)</p>';
            }
            updateRequiredMax();
            return;
        }

        const addQ = e.target.closest('.add-day-exam-question');
        if (addQ) {
            const card = addQ.closest('.day-exam-card');
            const wrap = card.querySelector('.day-exam-questions');
            const qIdx = wrap.querySelectorAll('.exam-question-row').length;
            wrap.insertAdjacentHTML('beforeend', questionHtml(card.dataset.examIndex, qIdx, {}));
            syncPassScoreMaxes();
            return;
        }

        const removeQ = e.target.closest('.remove-day-exam-question');
        if (removeQ) {
            const card = removeQ.closest('.day-exam-card');
            const rows = card.querySelectorAll('.exam-question-row');
            if (rows.length <= 1) return;
            removeQ.closest('.exam-question-row')?.remove();
            card.querySelectorAll('.exam-question-row').forEach((row, i) => {
                row.dataset.qIndex = String(i);
                row.querySelector('.question-number').textContent = String(i + 1);
                const examIdx = card.dataset.examIndex;
                row.querySelector(`input[name*="[question]"]`).name = `day_exams[${examIdx}][questions][${i}][question]`;
                row.querySelectorAll('input[name*="[answers][]"]').forEach((inp) => {
                    inp.name = `day_exams[${examIdx}][questions][${i}][answers][]`;
                });
                reindexRadios(row);
            });
            syncPassScoreMaxes();
            return;
        }

        const addA = e.target.closest('.add-day-exam-answer');
        if (addA) {
            const qRow = addA.closest('.exam-question-row');
            const answers = qRow.querySelector('.exam-answers');
            if (answers.querySelectorAll('.exam-answer-row').length >= 6) return;
            const examIdx = qRow.closest('.day-exam-card').dataset.examIndex;
            const qIdx = qRow.dataset.qIndex;
            answers.insertAdjacentHTML('beforeend', `
                <div class="exam-answer-row flex items-center gap-2">
                    <input type="radio" name="day_exams[${examIdx}][questions][${qIdx}][correct]" value="0" class="w-4 h-4 text-green-600">
                    <input type="text" name="day_exams[${examIdx}][questions][${qIdx}][answers][]" value=""
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" placeholder="نص الإجابة">
                    <button type="button" class="remove-day-exam-answer px-2 py-2 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-times"></i></button>
                </div>`);
            reindexRadios(qRow);
            return;
        }

        const removeA = e.target.closest('.remove-day-exam-answer');
        if (removeA) {
            const qRow = removeA.closest('.exam-question-row');
            if (qRow.querySelectorAll('.exam-answer-row').length <= 1) return;
            removeA.closest('.exam-answer-row')?.remove();
            reindexRadios(qRow);
        }
    });

    function refreshFromSchedule() {
        if (locked) return;
        const current = collectCurrentExams();
        const dayCount = teachingDayCountFromForm();
        const filtered = current.filter((ex) => parseInt(ex.day_index, 10) <= dayCount);
        render(filtered);
    }

    ['start_date', 'end_date'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', refreshFromSchedule);
    });
    document.getElementById('rest-days-container')?.addEventListener('change', refreshFromSchedule);

    // Initial render
    render(initialData.length ? initialData : []);

    function notifyDraft() {
        if (typeof window.__saveCourseDraft === 'function') {
            window.__saveCourseDraft();
        }
    }

    // After structural edits (clicks don't always fire form "input")
    daysWrap.addEventListener('click', () => setTimeout(notifyDraft, 0));
    daysWrap.addEventListener('change', notifyDraft);
    hasExamToggle?.addEventListener('change', notifyDraft);
    requiredInput?.addEventListener('input', notifyDraft);

    window.collectDayExamsDraft = function () {
        return {
            has_exam: !!(hasExamToggle && hasExamToggle.checked),
            required_exam_pass_count: requiredInput ? (requiredInput.value || '1') : '1',
            day_exams: collectCurrentExams(),
        };
    };

    window.restoreDayExamsDraft = function (data) {
        if (!data || typeof data !== 'object') return;
        if (hasExamToggle) {
            hasExamToggle.checked = !!data.has_exam;
            hasExamToggle.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (requiredInput && data.required_exam_pass_count != null && data.required_exam_pass_count !== '') {
            requiredInput.value = String(data.required_exam_pass_count);
        }
        render(Array.isArray(data.day_exams) ? data.day_exams : []);
        syncHasExamVisibility();
        updateRequiredMax();
    };

    // Expose for location_type toggle
    window.refreshDayExamsBuilder = refreshFromSchedule;
    window.setDayExamsSectionVisible = function (visible) {
        section.classList.toggle('hidden', !visible);
        if (!visible) {
            section.querySelectorAll('input, button, select, textarea').forEach((el) => {
                if (el.id === 'has_exam_toggle' && el.type === 'checkbox') {
                    // keep toggle state but prevent submit when whole section is for recorded
                }
                el.setAttribute('disabled', 'disabled');
            });
            return;
        }
        if (hasExamToggle && !locked) {
            hasExamToggle.removeAttribute('disabled');
        }
        syncHasExamVisibility();
        if (hasExamToggle?.checked && !locked) {
            updateRequiredMax();
        }
    };
})();
</script>

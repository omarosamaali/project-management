{{-- Exam builder — expects optional $course for edit --}}
@php
    $hasExam = (bool) old('has_exam', isset($course) ? $course->has_exam : false);
    $passScore = old('exam_pass_score', isset($course) ? $course->exam_pass_score : 1);
    $examDuration = old('exam_duration_minutes', isset($course) ? ($course->exam_duration_minutes ?? 30) : 30);
    $examLocked = isset($course) && $course->exam_started_at;
    $oldQuestions = old('exam_questions');
    if ($oldQuestions === null && isset($course) && $course->relationLoaded('examQuestions')) {
        $oldQuestions = $course->examQuestions->map(function ($q) {
            return [
                'question' => $q->question,
                'answers' => $q->answers->pluck('answer')->values()->all(),
                'correct' => $q->answers->search(fn ($a) => $a->is_correct),
            ];
        })->values()->all();
        // search returns false if not found — normalize
        foreach ($oldQuestions as &$oq) {
            $oq['correct'] = $oq['correct'] === false ? 0 : $oq['correct'];
        }
        unset($oq);
    }
    $oldQuestions = $oldQuestions ?: [];
@endphp

<div class="border rounded-lg p-5 bg-gray-50">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">هل تحتوي الدورة على اختبار؟</label>
            <p class="text-xs text-gray-500">عند التفعيل يمكن بناء أسئلة الاختبار وتحديد درجة النجاح</p>
        </div>
        <label class="inline-flex items-center cursor-pointer {{ $examLocked ? 'opacity-60 pointer-events-none' : '' }}">
            @if($examLocked)
            <input type="hidden" name="has_exam" value="1">
            @endif
            <input type="checkbox" name="has_exam" value="1" id="has_exam_toggle" class="sr-only peer"
                {{ $hasExam ? 'checked' : '' }} {{ $examLocked ? 'disabled' : '' }}>
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-700 select-none">نعم</span>
        </label>
    </div>

    @if($examLocked)
    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
        <i class="fas fa-lock ml-1"></i>
        تم بدء الاختبار — لا يمكن تعديل الأسئلة بعد الآن.
    </div>
    @endif

    <div id="exam-builder" class="{{ $hasExam ? '' : 'hidden' }} space-y-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-indigo-600"></i>
            بناء الاختبار
        </h3>

        <div id="exam-questions-container" class="space-y-4">
            @forelse($oldQuestions as $qi => $q)
            <div class="exam-question-row border border-indigo-100 rounded-lg p-4 bg-white" data-q-index="{{ $qi }}">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <label class="block text-sm font-medium text-gray-700 flex-1">
                        سؤال <span class="question-number">{{ $qi + 1 }}</span>
                        <input type="text" name="exam_questions[{{ $qi }}][question]"
                            value="{{ $q['question'] ?? '' }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="اكتب نص السؤال" {{ $examLocked ? 'readonly' : '' }}>
                    </label>
                    @unless($examLocked)
                    <button type="button"
                        class="remove-exam-question mt-6 px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endunless
                </div>
                <div class="exam-answers space-y-2 mb-3">
                    @php $answers = $q['answers'] ?? ['']; @endphp
                    @foreach($answers as $ai => $answer)
                    <div class="exam-answer-row flex items-center gap-2">
                        <input type="radio" name="exam_questions[{{ $qi }}][correct]" value="{{ $ai }}"
                            class="w-4 h-4 text-green-600" title="الإجابة الصحيحة"
                            {{ (int)($q['correct'] ?? 0) === (int)$ai ? 'checked' : '' }}
                            {{ $examLocked ? 'disabled' : '' }}>
                        <input type="text" name="exam_questions[{{ $qi }}][answers][]" value="{{ $answer }}"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="نص الإجابة" {{ $examLocked ? 'readonly' : '' }}>
                        @unless($examLocked)
                        <button type="button"
                            class="remove-exam-answer px-2 py-2 text-red-500 hover:bg-red-50 rounded disabled:opacity-40"
                            {{ count($answers) <= 1 ? 'disabled' : '' }}>
                            <i class="fas fa-times"></i>
                        </button>
                        @endunless
                    </div>
                    @endforeach
                </div>
                @unless($examLocked)
                <button type="button"
                    class="add-exam-answer text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-plus ml-1"></i> إضافة إجابة (حد أقصى 6)
                </button>
                @endunless
            </div>
            @empty
            @unless($examLocked)
            {{-- Default empty question injected by JS if needed --}}
            @endunless
            @endforelse
        </div>

        @unless($examLocked)
        <button type="button" id="add-exam-question"
            class="flex items-center gap-2 px-5 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus"></i>
            إضافة سؤال
        </button>
        @endunless

        <div class="pt-4 border-t border-gray-200 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    مدة الاختبار (بالدقائق) <span class="text-red-600">*</span>
                </label>
                <input type="number" name="exam_duration_minutes" id="exam_duration_minutes" min="1" max="600"
                    value="{{ $examDuration ?: 30 }}"
                    class="w-40 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    {{ $examLocked ? 'readonly' : '' }} required>
                <p class="text-xs text-gray-500 mt-1">الزمن المسموح للطالب لإكمال الاختبار</p>
                @error('exam_duration_minutes')
                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    درجة النجاح (عدد الإجابات الصحيحة المطلوبة)
                </label>
                <input type="number" name="exam_pass_score" id="exam_pass_score" min="1"
                    value="{{ $passScore ?: 1 }}"
                    class="w-40 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    {{ $examLocked ? 'readonly' : '' }}>
                <p class="text-xs text-gray-500 mt-1">
                    من أصل <span id="exam-questions-count">{{ max(count($oldQuestions), 1) }}</span> أسئلة
                </p>
                @error('exam_pass_score')
                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                @enderror
                @error('exam_questions')
                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

@unless($examLocked)
<script>
(function () {
    const toggle = document.getElementById('has_exam_toggle');
    const builder = document.getElementById('exam-builder');
    const container = document.getElementById('exam-questions-container');
    const addQuestionBtn = document.getElementById('add-exam-question');
    const passScoreInput = document.getElementById('exam_pass_score');
    const countEl = document.getElementById('exam-questions-count');

    if (!toggle || !builder || !container) return;

    const syncVisibility = () => {
        builder.classList.toggle('hidden', !toggle.checked);
        if (toggle.checked && container.children.length === 0) {
            addQuestion();
        }
        updateMeta();
    };

    const updateMeta = () => {
        const rows = container.querySelectorAll('.exam-question-row');
        rows.forEach((row, i) => {
            const num = row.querySelector('.question-number');
            if (num) num.textContent = i + 1;
            row.dataset.qIndex = i;
            reindexQuestion(row, i);
        });
        const count = rows.length || 1;
        if (countEl) countEl.textContent = rows.length;
        if (passScoreInput) {
            passScoreInput.max = Math.max(rows.length, 1);
            if (parseInt(passScoreInput.value, 10) > rows.length && rows.length > 0) {
                passScoreInput.value = rows.length;
            }
        }
    };

    const reindexQuestion = (row, qi) => {
        const qInput = row.querySelector('input[name*="[question]"]');
        if (qInput) qInput.name = `exam_questions[${qi}][question]`;

        row.querySelectorAll('.exam-answer-row').forEach((aRow, ai) => {
            const radio = aRow.querySelector('input[type="radio"]');
            const text = aRow.querySelector('input[type="text"]');
            if (radio) {
                radio.name = `exam_questions[${qi}][correct]`;
                radio.value = ai;
            }
            if (text) text.name = `exam_questions[${qi}][answers][]`;
        });

        const removeBtns = row.querySelectorAll('.remove-exam-answer');
        const answerCount = row.querySelectorAll('.exam-answer-row').length;
        removeBtns.forEach(btn => { btn.disabled = answerCount <= 1; });
    };

    const answerRowHtml = (qi, ai, checked = false) => `
        <div class="exam-answer-row flex items-center gap-2">
            <input type="radio" name="exam_questions[${qi}][correct]" value="${ai}"
                class="w-4 h-4 text-green-600" title="الإجابة الصحيحة" ${checked ? 'checked' : ''}>
            <input type="text" name="exam_questions[${qi}][answers][]"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                placeholder="نص الإجابة">
            <button type="button" class="remove-exam-answer px-2 py-2 text-red-500 hover:bg-red-50 rounded">
                <i class="fas fa-times"></i>
            </button>
        </div>`;

    const questionRowHtml = (qi) => `
        <div class="exam-question-row border border-indigo-100 rounded-lg p-4 bg-white" data-q-index="${qi}">
            <div class="flex items-start justify-between gap-3 mb-3">
                <label class="block text-sm font-medium text-gray-700 flex-1">
                    سؤال <span class="question-number">${qi + 1}</span>
                    <input type="text" name="exam_questions[${qi}][question]"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="اكتب نص السؤال">
                </label>
                <button type="button" class="remove-exam-question mt-6 px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="exam-answers space-y-2 mb-3">
                ${answerRowHtml(qi, 0, true)}
            </div>
            <button type="button" class="add-exam-answer text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-plus ml-1"></i> إضافة إجابة (حد أقصى 6)
            </button>
        </div>`;

    const addQuestion = () => {
        const qi = container.querySelectorAll('.exam-question-row').length;
        container.insertAdjacentHTML('beforeend', questionRowHtml(qi));
        updateMeta();
    };

    toggle.addEventListener('change', syncVisibility);
    if (addQuestionBtn) addQuestionBtn.addEventListener('click', addQuestion);

    container.addEventListener('click', (e) => {
        const addAns = e.target.closest('.add-exam-answer');
        const remAns = e.target.closest('.remove-exam-answer');
        const remQ = e.target.closest('.remove-exam-question');

        if (addAns) {
            const row = addAns.closest('.exam-question-row');
            const answers = row.querySelector('.exam-answers');
            if (answers.children.length >= 6) return;
            const qi = Array.from(container.querySelectorAll('.exam-question-row')).indexOf(row);
            answers.insertAdjacentHTML('beforeend', answerRowHtml(qi, answers.children.length, false));
            updateMeta();
        }

        if (remAns) {
            const row = remAns.closest('.exam-question-row');
            const answers = row.querySelector('.exam-answers');
            if (answers.children.length <= 1) return;
            const aRow = remAns.closest('.exam-answer-row');
            const wasChecked = aRow.querySelector('input[type="radio"]')?.checked;
            aRow.remove();
            if (wasChecked) {
                const first = answers.querySelector('input[type="radio"]');
                if (first) first.checked = true;
            }
            updateMeta();
        }

        if (remQ) {
            const rows = container.querySelectorAll('.exam-question-row');
            if (rows.length <= 1) return;
            remQ.closest('.exam-question-row').remove();
            updateMeta();
        }
    });

    // Ensure at least one question when visible on load
    document.addEventListener('DOMContentLoaded', () => {
        syncVisibility();
    });
    syncVisibility();
})();
</script>
@endunless

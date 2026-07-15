@extends('layouts.app')

@section('title', 'اختبار الدورة')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-indigo-600 text-white p-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold">اختبار: {{ $course->name_ar }}</h1>
                    <p class="text-indigo-100 text-sm mt-1">محاولة واحدة فقط — ترتيب الأسئلة والإجابات مختلف لكل طالب</p>
                </div>
                <div id="exam-timer"
                    class="px-4 py-2 bg-white/15 rounded-lg text-center min-w-[7rem] border border-white/20"
                    data-remaining="{{ (int) $remainingSeconds }}">
                    <div class="text-[10px] uppercase tracking-wide text-indigo-100">الوقت المتبقي</div>
                    <div id="exam-timer-display" class="text-xl font-bold tabular-nums">--:--</div>
                </div>
            </div>

            <form id="exam-form" method="POST" action="{{ route('dashboard.courses.exam.submit', $course) }}" class="p-6">
                @csrf
                <input type="hidden" name="timed_out" id="timed_out" value="0">

                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>سؤال <strong id="current-num">1</strong> من {{ $questions->count() }}</span>
                        <span id="progress-pct">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                @foreach($questions as $index => $question)
                <div class="exam-step {{ $index === 0 ? '' : 'hidden' }}" data-step="{{ $index }}">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 leading-relaxed">
                        {{ $question->question }}
                    </h2>
                    <div class="space-y-3">
                        @foreach($question->answers as $answer)
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}"
                                class="w-5 h-5 text-indigo-600 exam-answer-radio">
                            <span class="text-gray-800 dark:text-gray-200">{{ $answer->answer }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="flex justify-between items-center mt-8 pt-4 border-t">
                    <button type="button" id="exam-prev"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition hidden">
                        السابق
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" id="exam-next"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-bold">
                        التالي
                    </button>
                    <button type="submit" id="exam-submit"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold hidden">
                        تسليم الاختبار
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
(function () {
    const steps = Array.from(document.querySelectorAll('.exam-step'));
    const total = steps.length;
    let current = 0;
    const prevBtn = document.getElementById('exam-prev');
    const nextBtn = document.getElementById('exam-next');
    const submitBtn = document.getElementById('exam-submit');
    const form = document.getElementById('exam-form');
    const bar = document.getElementById('progress-bar');
    const pct = document.getElementById('progress-pct');
    const num = document.getElementById('current-num');
    const timerEl = document.getElementById('exam-timer');
    const timerDisplay = document.getElementById('exam-timer-display');
    const timedOutInput = document.getElementById('timed_out');
    let remaining = parseInt(timerEl?.dataset.remaining || '0', 10);
    let submitting = false;

    if (prevBtn) prevBtn.style.display = 'none';

    function formatTime(sec) {
        sec = Math.max(0, Math.floor(sec));
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function updateTimerUi() {
        if (!timerDisplay) return;
        timerDisplay.textContent = formatTime(remaining);
        if (remaining <= 60) {
            timerEl.classList.add('bg-red-500/30', 'border-red-200');
            timerEl.classList.remove('bg-white/15');
        }
    }

    function autoSubmitOnTimeout() {
        if (submitting) return;
        submitting = true;
        timedOutInput.value = '1';
        Swal.fire({
            title: 'انتهى الوقت',
            text: 'يتم تسليم إجاباتك تلقائياً...',
            icon: 'warning',
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 1500,
        }).then(() => form.submit());
    }

    updateTimerUi();
    const tick = setInterval(() => {
        remaining -= 1;
        updateTimerUi();
        if (remaining <= 0) {
            clearInterval(tick);
            autoSubmitOnTimeout();
        }
    }, 1000);

    function showStep(i) {
        steps.forEach((s, idx) => s.classList.toggle('hidden', idx !== i));
        current = i;
        num.textContent = i + 1;
        const progress = Math.round((i / total) * 100);
        bar.style.width = progress + '%';
        pct.textContent = progress + '%';
        nextBtn.classList.toggle('hidden', i === total - 1);
        submitBtn.classList.toggle('hidden', i !== total - 1);
    }

    function currentAnswered() {
        return !!steps[current].querySelector('input[type="radio"]:checked');
    }

    nextBtn.addEventListener('click', () => {
        if (!currentAnswered()) {
            Swal.fire({ icon: 'warning', title: 'اختر إجابة', text: 'يجب اختيار إجابة قبل الانتقال للسؤال التالي' });
            return;
        }
        if (current < total - 1) showStep(current + 1);
    });

    form.addEventListener('submit', (e) => {
        if (timedOutInput.value === '1' || submitting) return;
        if (!currentAnswered()) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'اختر إجابة', text: 'يجب اختيار إجابة قبل التسليم' });
            return;
        }
        const unanswered = steps.some(s => !s.querySelector('input[type="radio"]:checked'));
        if (unanswered) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'إجابات ناقصة', text: 'يجب الإجابة على جميع الأسئلة' });
            return;
        }
        e.preventDefault();
        Swal.fire({
            title: 'تسليم الاختبار؟',
            text: 'لا يمكن إعادة المحاولة بعد التسليم',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'تسليم',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#16a34a',
        }).then((result) => {
            if (result.isConfirmed) {
                submitting = true;
                form.submit();
            }
        });
    });

    showStep(0);
})();
</script>
@endsection

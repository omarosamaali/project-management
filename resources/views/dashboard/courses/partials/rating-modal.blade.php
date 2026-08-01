{{--
    Rating modal — one question at a time.
    Required variables: $ratingCourse, $ratingPayment, $ratingQuestions
    Optional: $ratingAutoOpen (bool) — auto-show the modal on page load
--}}
@php
    $ratingAutoOpen = $ratingAutoOpen ?? false;
    $ratingQuestions = $ratingQuestions ?? [];
@endphp

@if(count($ratingQuestions))
<div id="ratingModal"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 transition-opacity duration-300 {{ $ratingAutoOpen ? '' : 'hidden' }}"
    style="background: rgba(0,0,0,.6); backdrop-filter: blur(4px);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative" id="ratingCard">
        {{-- Header --}}
        <div class="bg-gradient-to-l from-amber-500 to-amber-600 text-white p-5">
            <h2 class="text-lg font-bold">تقييم الدورة</h2>
            <p class="text-amber-100 text-sm mt-1">{{ $ratingCourse->name_ar }}</p>
            <p class="text-amber-50 text-xs mt-2">
                <i class="fas fa-info-circle ml-1"></i>
                يجب إكمال جميع أسئلة التقييم قبل استخراج الشهادة
            </p>
        </div>

        {{-- Progress bar --}}
        <div class="h-1 bg-amber-100">
            <div id="ratingProgress" class="h-full bg-amber-500 transition-all duration-300" style="width:0%"></div>
        </div>

        {{-- Question container --}}
        <div class="p-6 min-h-[280px] flex flex-col" id="ratingBody">
            {{-- Section label --}}
            <p class="text-xs text-amber-600 font-bold mb-1" id="ratingSectionLabel"></p>
            {{-- Counter --}}
            <p class="text-xs text-gray-400 mb-3" id="ratingCounter"></p>
            {{-- Question text --}}
            <p class="text-base font-semibold text-gray-800 mb-5 leading-relaxed" id="ratingQuestionText"></p>

            {{-- Scale stars (hidden when not scale) --}}
            <div id="ratingStarsWrap" class="flex-1 flex items-center justify-center gap-3 hidden">
                @for($s = 1; $s <= 5; $s++)
                <button type="button" data-star="{{ $s }}"
                    class="rating-star w-14 h-14 rounded-xl border-2 border-gray-200 flex items-center justify-center text-2xl text-gray-300 hover:border-amber-400 hover:text-amber-400 transition-all duration-150 focus:outline-none">
                    <i class="fas fa-star"></i>
                </button>
                @endfor
            </div>

            {{-- Boolean yes/no (hidden when not boolean) --}}
            <div id="ratingBoolWrap" class="flex-1 flex items-center justify-center gap-4 hidden">
                <button type="button" data-bool="1"
                    class="rating-bool-btn px-8 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold hover:border-green-500 hover:text-green-600 transition-all focus:outline-none">
                    نعم
                </button>
                <button type="button" data-bool="0"
                    class="rating-bool-btn px-8 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold hover:border-red-400 hover:text-red-500 transition-all focus:outline-none">
                    لا
                </button>
            </div>

            {{-- Text area (hidden when not text) --}}
            <div id="ratingTextWrap" class="flex-1 hidden">
                <textarea id="ratingTextInput" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none text-sm"
                    placeholder="اكتب إجابتك هنا..."></textarea>
            </div>

            {{-- Validation message --}}
            <p id="ratingValidation" class="text-red-500 text-xs mt-2 hidden">هذا السؤال مطلوب</p>
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t flex items-center justify-between gap-3">
            <button type="button" id="ratingPrevBtn"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition hidden">
                <i class="fas fa-arrow-right ml-1"></i> السابق
            </button>
            <button type="button" id="ratingNextBtn"
                class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 transition mr-auto">
                التالي <i class="fas fa-arrow-left mr-1"></i>
            </button>
        </div>
    </div>
</div>

<form id="ratingHiddenForm" method="POST" action="{{ route('courses.rating.store', $ratingCourse) }}" class="hidden">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const questions = @json($ratingQuestions);
    const modal = document.getElementById('ratingModal');
    const form = document.getElementById('ratingHiddenForm');
    if (!questions.length || !modal || !form) return;

    const progress = document.getElementById('ratingProgress');
    const sectionLabel = document.getElementById('ratingSectionLabel');
    const counter = document.getElementById('ratingCounter');
    const qText = document.getElementById('ratingQuestionText');
    const starsWrap = document.getElementById('ratingStarsWrap');
    const boolWrap = document.getElementById('ratingBoolWrap');
    const textWrap = document.getElementById('ratingTextWrap');
    const textInput = document.getElementById('ratingTextInput');
    const validation = document.getElementById('ratingValidation');
    const prevBtn = document.getElementById('ratingPrevBtn');
    const nextBtn = document.getElementById('ratingNextBtn');
    const starBtns = starsWrap.querySelectorAll('.rating-star');
    const boolBtns = boolWrap.querySelectorAll('.rating-bool-btn');

    let current = 0;
    const answers = {};

    function render() {
        const q = questions[current];
        const total = questions.length;
        progress.style.width = Math.round(((current + 1) / total) * 100) + '%';
        sectionLabel.textContent = q.section || '';
        counter.textContent = 'السؤال ' + (current + 1) + ' من ' + total;
        qText.textContent = q.label_ar || q.id;
        validation.classList.add('hidden');

        starsWrap.classList.add('hidden');
        boolWrap.classList.add('hidden');
        textWrap.classList.add('hidden');

        if (q.type === 'scale') {
            starsWrap.classList.remove('hidden');
            const val = answers[q.id] || 0;
            starBtns.forEach(btn => {
                const s = parseInt(btn.dataset.star);
                const active = s <= val;
                btn.classList.toggle('border-amber-500', active);
                btn.classList.toggle('bg-amber-50', active);
                btn.classList.toggle('text-amber-500', active);
                btn.classList.toggle('border-gray-200', !active);
                btn.classList.toggle('text-gray-300', !active);
                btn.classList.toggle('bg-white', !active);
            });
        } else if (q.type === 'boolean') {
            boolWrap.classList.remove('hidden');
            const val = answers[q.id];
            boolBtns.forEach(btn => {
                const bv = btn.dataset.bool;
                const active = val !== undefined && String(val) === bv;
                btn.classList.toggle('border-amber-500', active);
                btn.classList.toggle('bg-amber-50', active);
                btn.classList.toggle('text-amber-700', active);
                btn.classList.toggle('border-gray-200', !active);
                btn.classList.toggle('text-gray-600', !active);
            });
        } else {
            textWrap.classList.remove('hidden');
            textInput.value = answers[q.id] || '';
            textInput.focus();
        }

        prevBtn.classList.toggle('hidden', current === 0);
        if (current === total - 1) {
            nextBtn.innerHTML = '<i class="fas fa-paper-plane ml-1"></i> إرسال التقييم';
        } else {
            nextBtn.innerHTML = 'التالي <i class="fas fa-arrow-left mr-1"></i>';
        }
    }

    starBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const q = questions[current];
            answers[q.id] = parseInt(btn.dataset.star);
            render();
        });
    });

    boolBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const q = questions[current];
            answers[q.id] = btn.dataset.bool;
            render();
        });
    });

    function validate() {
        const q = questions[current];
        if (!q.required) return true;
        if (q.type === 'text') {
            answers[q.id] = (textInput.value || '').trim();
        }
        if (answers[q.id] === undefined || answers[q.id] === '' || answers[q.id] === null) {
            validation.classList.remove('hidden');
            return false;
        }
        return true;
    }

    nextBtn.addEventListener('click', () => {
        if (!validate()) return;
        if (current < questions.length - 1) {
            current++;
            render();
        } else {
            submitRating();
        }
    });

    prevBtn.addEventListener('click', () => {
        const q = questions[current];
        if (q.type === 'text') {
            answers[q.id] = (textInput.value || '').trim();
        }
        if (current > 0) {
            current--;
            render();
        }
    });

    function submitRating() {
        nextBtn.disabled = true;
        nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الإرسال...';
        for (const [key, val] of Object.entries(answers)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'answers[' + key + ']';
            input.value = val;
            form.appendChild(input);
        }
        form.submit();
    }

    window.openRatingModal = function () {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        render();
    };

    @if($ratingAutoOpen)
    openRatingModal();
    @endif
});
</script>
@endif

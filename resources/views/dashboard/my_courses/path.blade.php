@extends('layouts.app')

@section('title', 'المسار التعليمي — ' . ($course->name_ar ?? ''))

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.css">
<style>
    .path-layout { display:grid; gap:1rem; }
    @media (min-width: 1024px) {
        .path-layout { grid-template-columns: 320px minmax(0,1fr); }
    }
    .path-sidebar { background:#fff; border:1px solid #e5e7eb; border-radius:.75rem; overflow:hidden; max-height: calc(100dvh - 10rem); overflow-y:auto; }
    .path-unit-head {
        padding: .75rem 1rem;
        background: #f8fafc;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: .5rem;
        align-items: center;
        width: 100%;
        cursor: pointer;
        user-select: none;
        text-align: inherit;
        color: inherit;
        transition: background .15s ease;
    }
    .path-unit-head:hover { background: #f1f5f9; }
    .path-unit-head .path-unit-chevron {
        margin-inline-start: auto;
        color: #94a3b8;
        font-size: .75rem;
        transition: transform .2s ease;
    }
    .path-unit.is-open > .path-unit-head .path-unit-chevron {
        transform: rotate(180deg);
    }
    .path-unit-body {
        overflow: hidden;
        height: 0;
        transition: height .28s ease;
    }
    .path-unit.is-open > .path-unit-body {
        /* height is managed in JS for smooth expand/collapse */
    }
    .path-unit-badge { width:1.75rem; height:1.75rem; border-radius:9999px; background:#f59e0b; color:#fff; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; }
    .path-item-link { display:flex; align-items:center; gap:.5rem; padding:.65rem 1rem; border-bottom:1px solid #f1f5f9; font-size:.875rem; color:#334155; }
    .path-item-link.locked { opacity:.45; pointer-events:none; }
    .path-item-link.active { background:#eff6ff; color:#1d4ed8; font-weight:600; }
    .path-item-link.done { color:#15803d; }
    .player-card { background:#fff; border:1px solid #e5e7eb; border-radius:.75rem; overflow:hidden; }
    /* YouTube-style responsive 16:9 player (desktop + mobile) */
    .player-shell {
        position: relative;
        width: 100%;
        max-width: 100%;
        background: #000;
        overflow: hidden;
    }
    .player-shell::before {
        content: '';
        display: block;
        padding-top: 56.25%; /* 16:9 — same as YouTube */
    }
    /* Watermark wrap must fill the shell; height:100% alone collapses to 0 with the padding trick. */
    .player-shell .wm-media-wrap {
        position: absolute !important;
        inset: 0;
        width: 100% !important;
        height: 100% !important;
    }
    .player-shell .plyr,
    .player-shell .wm-media-wrap > video,
    .player-shell .wm-media-wrap > #lessonVideo,
    .player-shell > video,
    .player-shell > #lessonVideo {
        position: absolute !important;
        inset: 0;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
    }
    .player-shell .plyr {
        --plyr-color-main: #2563eb;
    }
    .player-shell .plyr__video-wrapper,
    .player-shell .plyr__video-embed,
    .player-shell .plyr__video-embed iframe {
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        padding-bottom: 0 !important;
    }
    .player-shell video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center center;
        background: #000;
    }
    .player-shell .plyr__poster {
        background-size: contain !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-color: #000;
    }
    .player-shell .plyr--playing .plyr__poster,
    .player-shell .plyr--loading .plyr__poster {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    .player-shell .ac-media-wm {
        position: absolute;
        top: 12px;
        inset-inline-end: 12px;
        inset-inline-start: auto;
        width: min(18%, 120px);
        height: auto;
        pointer-events: none;
        z-index: 5;
        user-select: none;
    }
    .path-completion-ring {
        --pct: 0;
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 9999px;
        background: conic-gradient(#2563eb calc(var(--pct) * 1%), #e5e7eb 0);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        position: relative;
    }
    .path-completion-ring::before {
        content: '';
        position: absolute;
        inset: 7px;
        border-radius: 9999px;
        background: #fff;
    }
    .path-completion-ring > span {
        position: relative;
        z-index: 1;
        font-size: .85rem;
        font-weight: 800;
        color: #1e3a8a;
        font-variant-numeric: tabular-nums;
    }
</style>

@php
    $pathCompletion = $course->pathCompletionForUser(auth()->id());
    $currentDone = $current && ($progress->get($current->id)?->is_completed);
@endphp

<section class="p-3 sm:p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-gray-900 truncate">{{ $course->name_ar }}</h1>
                <p class="text-xs text-gray-500">
                    المسار التعليمي
                    @php $pathTotalDuration = $course->formattedTotalContentDuration(); @endphp
                    @if($pathTotalDuration !== '—')
                     — المدة الكلية {{ $pathTotalDuration }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3"
            id="pathCompletionWidget"
            data-completed="{{ $pathCompletion['completed'] }}"
            data-total="{{ $pathCompletion['total'] }}"
            data-current-done="{{ $currentDone ? '1' : '0' }}">
            <div class="text-left hidden sm:block">
                <p class="text-[11px] text-gray-500">نسبة الإنجاز</p>
                <p class="text-xs text-gray-700 font-medium">
                    <span data-ring-completed>{{ $pathCompletion['completed'] }}</span>
                    /
                    <span data-ring-total>{{ $pathCompletion['total'] }}</span>
                    خطوة
                </p>
            </div>
            <div class="path-completion-ring" id="pathCompletionRing" style="--pct: {{ $pathCompletion['percent'] }}">
                <span data-ring-percent>{{ $pathCompletion['percent'] }}%</span>
            </div>
        </div>
    </div>

    <div class="path-layout"
        id="pathPlayer"
        data-course-id="{{ $course->id }}"
        data-csrf="{{ csrf_token() }}"
        data-require-complete="{{ $course->requiresPathLessonComplete() ? '1' : '0' }}"
        data-complete-ratio="{{ $course->pathLessonCompleteRatio() }}"
        data-progress-url="{{ url('/dashboard/courses/'.$course->id.'/path/items') }}"
        data-exam-url="{{ url('/dashboard/courses/'.$course->id.'/path/items') }}">
        <aside class="path-sidebar shadow-sm">
            @php
                $flatItems = $items->values();
                $idToIndex = $flatItems->pluck('id')->flip();
                $currentIndex = ($current && isset($idToIndex[$current->id]))
                    ? (int) $idToIndex[$current->id]
                    : false;
            @endphp
            @foreach($course->units as $uIndex => $unit)
            @php
                $unitHasCurrent = $current && $unit->items->contains(fn ($i) => (int) $i->id === (int) $current->id);
                // Open the active unit; if none, open the first unit by default
                $unitOpen = $unitHasCurrent || (!$current && $uIndex === 0);
            @endphp
            <div class="path-unit {{ $unitOpen ? 'is-open' : '' }}" data-path-unit>
                <button type="button" class="path-unit-head" data-path-unit-toggle aria-expanded="{{ $unitOpen ? 'true' : 'false' }}">
                    <span class="path-unit-badge">{{ $uIndex + 1 }}</span>
                    <span class="truncate">{{ $unit->localizedTitle() }}</span>
                    <i class="fas fa-chevron-down path-unit-chevron" aria-hidden="true"></i>
                </button>
                <div class="path-unit-body" data-path-unit-body>
                @foreach($unit->items as $item)
                @php
                    $p = $progress->get($item->id);
                    $done = $p && $p->is_completed;
                    $itemIndex = isset($idToIndex[$item->id]) ? (int) $idToIndex[$item->id] : false;
                    // Always allow returning to any previous step once reached
                    $canAccess = $course->canUserAccessPathItem(auth()->user(), $item)
                        || ($currentIndex !== false && $itemIndex !== false && $itemIndex < $currentIndex);
                    $isCurrent = $current && (int)$current->id === (int)$item->id;
                @endphp
                <a href="{{ route('dashboard.my_courses.path', ['payment' => $payment->id, 'item' => $item->id]) }}"
                    data-item-id="{{ $item->id }}"
                    data-item-type="{{ $item->isExam() ? 'exam' : 'lesson' }}"
                    class="path-item-link {{ $done ? 'done' : '' }} {{ $isCurrent ? 'active' : '' }} {{ !$canAccess ? 'locked' : '' }}">
                    @if(!$canAccess)
                        <i class="fas fa-lock text-gray-400"></i>
                    @elseif($done)
                        <i class="fas fa-check-circle text-green-600"></i>
                    @elseif($item->isExam())
                        <i class="fas fa-clipboard-list text-indigo-600"></i>
                    @else
                        <i class="fas fa-play-circle text-blue-600"></i>
                    @endif
                    <span class="truncate flex-1">{{ $item->localizedTitle() }}</span>
                    @if($item->isLesson() && $item->video_duration_seconds)
                    <span class="text-[10px] text-gray-400">{{ $item->formattedDuration() }}</span>
                    @elseif($item->isExam() && $item->exam_duration_minutes)
                    <span class="text-[10px] text-indigo-500">{{ $item->formattedExamDuration() }}</span>
                    @endif
                </a>
                @endforeach
                </div>
            </div>
            @endforeach
        </aside>

        <div class="player-card shadow-sm">
            @if(!$current)
            <div class="p-10 text-center text-gray-500">
                <i class="fas fa-route text-4xl mb-3 text-amber-400"></i>
                <p>لا يوجد محتوى في المسار التعليمي بعد.</p>
            </div>
            @elseif($current->isLesson())
            @php $playback = $current->playbackSource(); @endphp
            <div class="p-4 border-b flex items-center justify-between gap-2">
                <div>
                    <p class="text-xs text-blue-600 font-medium">درس</p>
                    <h2 class="text-lg font-bold text-gray-900">{{ $current->localizedTitle() }}</h2>
                </div>
                <span id="lessonStatus" class="text-xs text-green-600 {{ ($progress->get($current->id)?->is_completed) ? '' : 'hidden' }}">
                    @if($progress->get($current->id)?->is_completed)
                        مكتمل
                    @endif
                </span>
            </div>
            @if($playback)
            @php
                $playerSrc = $playback['src'];
                if (($playback['provider'] ?? '') === 'html5' && $current->video_path) {
                    $playerSrc = \App\Http\Controllers\CoursePathController::signedStreamUrl($course, $current);
                }
            @endphp
            <div class="player-shell"
                id="lessonPlayerMount"
                data-ac-video-protect
                data-item-id="{{ $current->id }}"
                data-provider="{{ $playback['provider'] }}"
                data-src="{{ $playerSrc }}"
                data-embed-id="{{ $playback['embed_id'] ?? '' }}"
                data-poster="{{ $playback['poster'] ?? '' }}"
                data-duration="{{ (int) $current->video_duration_seconds }}"
                data-watched="{{ (int) ($progress->get($current->id)?->video_watched_seconds ?? 0) }}"
                data-played="{{ (int) ($progress->get($current->id)?->video_played_seconds ?? 0) }}"
                @if($progress->get($current->id)?->is_completed) data-completed="1" @endif>
                <div class="wm-media-wrap">
                @if($playback['provider'] === 'html5')
                <video id="lessonVideo" playsinline preload="metadata"
                    controlsList="nodownload noplaybackrate" disablePictureInPicture
                    oncontextmenu="return false;"
                    src="{{ $playerSrc }}"
                    @if(!empty($playback['poster'])) poster="{{ $playback['poster'] }}" @endif></video>
                @else
                <div id="lessonVideo" data-plyr-provider="{{ $playback['provider'] }}"
                    data-plyr-embed-id="{{ $playback['embed_id'] }}"
                    @if(!empty($playback['poster'])) data-poster="{{ $playback['poster'] }}" @endif></div>
                @endif
                <img src="{{ asset('assets/images/academy_watermark.png') }}" alt="" aria-hidden="true"
                    class="ac-media-wm"
                    style="opacity:{{ config('watermark.opacity', 0.38) }};">
                </div>
            </div>
            @else
            <div class="p-8 text-center text-red-600">لا يوجد فيديو لهذا الدرس.</div>
            @endif
            @else
            {{-- Exam: start → timed one-by-one → result / retake --}}
            @php
                $examProgress = $progress->get($current->id);
                $hasExamAttempt = $examProgress && $examProgress->exam_score !== null;
                $examDurationMinutes = max(1, (int) ($current->exam_duration_minutes ?? 30));
                $examPassScore = (int) ($current->exam_pass_score ?? max(1, $current->examQuestions->count()));
                $examTotal = $current->examQuestions->count();
                $lastTimeSpent = (int) ($examProgress->exam_time_spent_seconds ?? 0);
            @endphp
            <div class="p-4 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-indigo-600 font-medium">اختبار</p>
                    <h2 class="text-lg font-bold text-gray-900">{{ $current->localizedTitle() }}</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        درجة النجاح: {{ $examPassScore }} من {{ $examTotal }}
                        — المدة: {{ $examDurationMinutes }} دقيقة
                    </p>
                </div>
                <div id="examTimerBar" class="hidden items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 text-sm font-bold tabular-nums">
                    <i class="fas fa-clock"></i>
                    <span id="examTimerLabel">{{ sprintf('%02d:00', $examDurationMinutes) }}</span>
                </div>
            </div>
            <div class="p-4" id="pathExam"
                data-item-id="{{ $current->id }}"
                data-payment-id="{{ $payment->id }}"
                data-duration-seconds="{{ $examDurationMinutes * 60 }}"
                data-pass-score="{{ $examPassScore }}"
                data-total="{{ $examTotal }}"
                data-has-attempt="{{ $hasExamAttempt ? '1' : '0' }}"
                data-last-passed="{{ ($examProgress && $examProgress->exam_passed) ? '1' : '0' }}"
                data-last-score="{{ (int) ($examProgress->exam_score ?? 0) }}"
                data-last-time="{{ $lastTimeSpent }}">

                {{-- Intro: first start OR last result + retake --}}
                <div id="examIntro">
                    @if($hasExamAttempt)
                    <div class="rounded-xl border p-5 {{ $examProgress->exam_passed ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                        <div class="flex items-start gap-3">
                            <i class="fas {{ $examProgress->exam_passed ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }} text-2xl mt-0.5"></i>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-lg">
                                    {{ $examProgress->exam_passed ? 'نجحت في الاختبار' : 'لم تجتز الاختبار' }}
                                </p>
                                <p class="text-sm text-gray-700 mt-1">
                                    النتيجة: {{ (int) $examProgress->exam_score }} / {{ $examTotal }}
                                    (درجة النجاح {{ $examPassScore }})
                                </p>
                                @if($lastTimeSpent > 0)
                                <p class="text-sm text-gray-600 mt-1">
                                    الوقت المستغرق:
                                    <span class="font-semibold tabular-nums">{{ sprintf('%02d:%02d', intdiv($lastTimeSpent, 60), $lastTimeSpent % 60) }}</span>
                                    من {{ $examDurationMinutes }} دقيقة
                                </p>
                                @endif
                            </div>
                        </div>
                        <button type="button" id="examRetakeBtn"
                            class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium"
                            style="background-color:#4f46e5;color:#fff;">
                            <i class="fas fa-redo"></i>
                            إعادة المحاولة
                        </button>
                    </div>
                    @else
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-8 text-center">
                        <i class="fas fa-clipboard-list text-indigo-600 text-4xl mb-3"></i>
                        <p class="text-gray-800 font-semibold mb-1">جاهز لبدء الاختبار؟</p>
                        <p class="text-sm text-gray-600 mb-5">
                            لديك {{ $examDurationMinutes }} دقيقة و {{ $examTotal }} {{ $examTotal == 1 ? 'سؤال' : 'أسئلة' }}.
                            يبدأ العدّ التنازلي فور الضغط على ابدأ.
                        </p>
                        <button type="button" id="examStartBtn"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold"
                            style="background-color:#4f46e5;color:#fff;">
                            <i class="fas fa-play"></i>
                            ابدأ الاختبار
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Taking --}}
                <div id="examTaking" class="hidden">
                    <p id="examProgressLabel" class="text-xs text-gray-500 mb-3">سؤال 1 من {{ $examTotal }}</p>
                    <div id="examQuestionMount" class="space-y-3"></div>
                    <div class="mt-4 flex flex-wrap gap-2 justify-between items-center">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="examPrevBtn"
                                class="hidden px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                                <i class="fas fa-arrow-right ml-1"></i> السابق
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="examNextBtn"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium"
                                style="background-color:#4f46e5;color:#fff;">
                                التالي <i class="fas fa-arrow-left mr-1"></i>
                            </button>
                            <button type="button" id="examFinishBtn"
                                class="hidden px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium"
                                style="background-color:#15803d;color:#fff;">
                                <i class="fas fa-flag-checkered ml-1"></i> إنهاء الاختبار
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Live result after submit (before reload optional) --}}
                <div id="examResultView" class="hidden"></div>

                {{-- Unanswered questions confirm --}}
                <div id="examUnansweredModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" style="background:rgba(15,23,42,.55);">
                    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 text-right" role="dialog" aria-modal="true">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mt-0.5"></i>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-lg">أسئلة بدون إجابة</h3>
                                <p class="text-sm text-gray-600 mt-2">
                                    يوجد أسئلة لم تتم الإجابة عليها. إن أكملت التسليم ستُحسب بدرجة
                                    <strong>0</strong> لكل سؤال غير مُجاب.
                                </p>
                                <p class="text-sm text-gray-800 mt-3 font-medium">أرقام الأسئلة:</p>
                                <p id="examUnansweredList" class="text-sm text-amber-800 font-bold mt-1 tabular-nums"></p>
                            </div>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2 justify-end">
                            <button type="button" id="examUnansweredCancel"
                                class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700">
                                العودة للاختبار
                            </button>
                            <button type="button" id="examUnansweredConfirm"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-700 hover:bg-green-800"
                                style="background-color:#15803d;color:#fff;">
                                تأكيد الإنهاء
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Question templates (hidden) --}}
                <div id="examQuestionsData" class="hidden" aria-hidden="true">
                    @foreach($current->examQuestions as $qi => $q)
                    <div class="exam-q-template" data-q-index="{{ $qi }}" data-question-id="{{ $q->id }}">
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <p class="font-medium text-gray-800 mb-3">{{ $qi + 1 }}. {{ $q->question }}</p>
                            <div class="space-y-2">
                                @foreach($q->answers as $answer)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="exam_answer_live" value="{{ $answer->id }}"
                                        class="w-4 h-4 text-indigo-600 exam-answer-radio"
                                        data-question-id="{{ $q->id }}">
                                    <span>{{ $answer->answer }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.polyfilled.min.js"></script>
<script>
(function () {
    const root = document.getElementById('pathPlayer');
    if (!root) return;
    const csrf = root.dataset.csrf;
    const progressBase = root.dataset.progressUrl;
    const examBase = root.dataset.examUrl;
    const requireComplete = root.dataset.requireComplete !== '0';
    const completeRatio = Math.min(1, Math.max(0.1, parseFloat(root.dataset.completeRatio || '0.9') || 0.9));

    // Unit accordion with smooth height transition
    function setUnitBodyHeight(body, open, animate) {
        if (!body) return;
        body.style.overflow = 'hidden';
        if (!animate) {
            body.style.transition = 'none';
            body.style.height = open ? 'auto' : '0px';
            // force reflow then restore transition
            void body.offsetHeight;
            body.style.transition = '';
            return;
        }
        if (open) {
            body.style.height = body.scrollHeight + 'px';
            const onEnd = (e) => {
                if (e.propertyName !== 'height') return;
                body.style.height = 'auto';
                body.removeEventListener('transitionend', onEnd);
            };
            body.addEventListener('transitionend', onEnd);
        } else {
            body.style.height = body.scrollHeight + 'px';
            void body.offsetHeight;
            body.style.height = '0px';
        }
    }

    root.querySelectorAll('[data-path-unit]').forEach((unit) => {
        const body = unit.querySelector('[data-path-unit-body]');
        setUnitBodyHeight(body, unit.classList.contains('is-open'), false);
    });

    root.querySelectorAll('[data-path-unit-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const unit = btn.closest('[data-path-unit]');
            if (!unit) return;
            const body = unit.querySelector('[data-path-unit-body]');
            const open = !unit.classList.contains('is-open');
            unit.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            setUnitBodyHeight(body, open, true);
        });
    });

    const completionWidget = document.getElementById('pathCompletionWidget');
    function syncPathCompletionRing(markCurrentDone) {
        if (!completionWidget) return;
        let completed = parseInt(completionWidget.dataset.completed || '0', 10);
        const total = parseInt(completionWidget.dataset.total || '0', 10);
        const alreadyDone = completionWidget.dataset.currentDone === '1';
        if (markCurrentDone && !alreadyDone) {
            completed += 1;
            completionWidget.dataset.currentDone = '1';
            completionWidget.dataset.completed = String(completed);
        }
        const percent = total > 0 ? Math.round((completed / total) * 100) : 0;
        const ring = document.getElementById('pathCompletionRing');
        if (ring) ring.style.setProperty('--pct', String(percent));
        const pctEl = completionWidget.querySelector('[data-ring-percent]');
        const doneEl = completionWidget.querySelector('[data-ring-completed]');
        if (pctEl) pctEl.textContent = percent + '%';
        if (doneEl) doneEl.textContent = String(completed);
    }

    function markSidebarStepComplete(itemId) {
        const link = root.querySelector('.path-item-link[data-item-id="' + itemId + '"]');
        if (!link) return;

        link.classList.add('done');
        link.classList.remove('locked');
        const icon = link.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-check-circle text-green-600';
        }

        if (!requireComplete) return;

        const links = Array.from(root.querySelectorAll('.path-item-link'));
        const idx = links.indexOf(link);
        if (idx < 0 || idx >= links.length - 1) return;

        const next = links[idx + 1];
        if (!next.classList.contains('locked')) return;

        next.classList.remove('locked');
        const nextIcon = next.querySelector('i');
        if (!nextIcon) return;
        nextIcon.className = next.dataset.itemType === 'exam'
            ? 'fas fa-clipboard-list text-indigo-600'
            : 'fas fa-play-circle text-blue-600';

        const nextUnit = next.closest('[data-path-unit]');
        if (nextUnit && !nextUnit.classList.contains('is-open')) {
            const body = nextUnit.querySelector('[data-path-unit-body]');
            const btn = nextUnit.querySelector('[data-path-unit-toggle]');
            nextUnit.classList.add('is-open');
            if (btn) btn.setAttribute('aria-expanded', 'true');
            if (body && typeof setUnitBodyHeight === 'function') {
                setUnitBodyHeight(body, true, true);
            }
        }
    }

    const mount = document.getElementById('lessonPlayerMount');
    if (mount && typeof Plyr !== 'undefined') {
        const itemId = mount.dataset.itemId;
        const provider = mount.dataset.provider || 'html5';
        const duration = parseInt(mount.dataset.duration || '0', 10);
        let maxPosition = Math.max(0, parseInt(mount.dataset.watched || '0', 10));
        let playedSeconds = Math.max(0, parseFloat(mount.dataset.played || '0') || 0);
        let lastSentPosition = maxPosition;
        let lastSentPlayed = Math.floor(playedSeconds);
        let completed = mount.dataset.completed === '1';
        const statusEl = document.getElementById('lessonStatus');
        let poster = mount.dataset.poster || '';
        let lastTickAt = 0;
        let lastTickTime = 0;
        let hasResumed = false;
        // Embeds only: YouTube/Vimeo often reset to 0 on first play
        let resumeAgainOnPlay = maxPosition > 0 && provider !== 'html5';

        const playerOpts = {
            controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            seekTime: 5,
            ratio: '16:9',
            keyboard: { focused: true, global: false },
            youtube: { noCookie: true, rel: 0, showinfo: 0, iv_load_policy: 3, modestbranding: 1, controls: 0 },
            vimeo: { byline: false, portrait: false, title: false, speed: true, transparent: false },
        };
        if (poster) {
            playerOpts.poster = poster;
        }

        const player = new Plyr('#lessonVideo', playerOpts);

        function hidePosterOverlay() {
            const el = mount.querySelector('.plyr__poster');
            if (el) {
                el.style.opacity = '0';
                el.style.visibility = 'hidden';
                el.style.pointerEvents = 'none';
            }
            const media = player.media;
            if (media && media.tagName === 'VIDEO') {
                media.removeAttribute('poster');
            }
        }

        function resumePlaybackPosition() {
            // One-time restore of saved progress — never after user seeks
            if (hasResumed || maxPosition <= 0) return;
            const d = player.duration || duration || 0;
            if (!d || !isFinite(d) || d < 1) return;

            const resumeAt = Math.min(Math.max(0, maxPosition), Math.max(0, d - 1));
            if (resumeAt <= 0) return;

            try {
                player.currentTime = resumeAt;
                hasResumed = true;
            } catch (_) {}
        }

        function maybeSendProgress(force) {
            const position = Math.floor(maxPosition);
            const played = Math.floor(playedSeconds);
            const effectiveDuration = duration || Math.floor(player.duration || 0);
            const threshold = effectiveDuration > 0 ? Math.floor(effectiveDuration * completeRatio) : 0;
            const nearComplete = threshold > 0 && position >= threshold && played >= threshold;
            const moved = (position - lastSentPosition) >= 5 || (played - lastSentPlayed) >= 5;

            if (!force && !nearComplete && !moved) return;
            lastSentPosition = Math.max(lastSentPosition, position);
            lastSentPlayed = Math.max(lastSentPlayed, played);
            sendProgress(position, played);
        }

        async function sendProgress(position, played) {
            try {
                const res = await fetch(`${progressBase}/${itemId}/progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        position_seconds: Math.floor(position),
                        played_seconds: Math.floor(played),
                        watched_seconds: Math.floor(position)
                    })
                });
                const data = await res.json();
                if (data.is_completed) {
                    const wasComplete = completed;
                    completed = true;
                    if (!wasComplete) {
                        syncPathCompletionRing(true);
                        markSidebarStepComplete(itemId);
                    }
                    if (statusEl) {
                        statusEl.textContent = requireComplete
                            ? 'مكتمل — يمكنك الانتقال للخطوة التالية'
                            : 'مكتمل';
                        statusEl.className = 'text-xs text-green-600';
                        statusEl.classList.remove('hidden');
                    }
                }
            } catch (e) {}
        }

        player.on('ready', () => {
            // Resume once after media is ready (html5 + embeds)
            setTimeout(resumePlaybackPosition, provider === 'html5' ? 100 : 400);
        });

        player.on('loadedmetadata', () => {
            if (provider === 'html5' && !hasResumed) {
                resumePlaybackPosition();
            }
        });

        player.on('play', () => {
            hidePosterOverlay();
            lastTickAt = Date.now();
            lastTickTime = player.currentTime || 0;
            // Only re-apply resume for embeds that jump to 0 on first play
            if (resumeAgainOnPlay) {
                resumeAgainOnPlay = false;
                hasResumed = false;
                setTimeout(resumePlaybackPosition, 200);
            }
        });

        player.on('playing', () => {
            hidePosterOverlay();
            lastTickAt = Date.now();
            lastTickTime = player.currentTime || 0;
        });

        player.on('pause', () => {
            lastTickAt = 0;
            maybeSendProgress(true);
        });

        player.on('seeking', () => {
            // Mark that the user took control — do not auto-resume afterward
            hasResumed = true;
            lastTickAt = 0;
        });

        player.on('seeked', () => {
            hasResumed = true;
            lastTickAt = player.playing ? Date.now() : 0;
            lastTickTime = player.currentTime || 0;
            const t = player.currentTime || 0;
            if (t > maxPosition) maxPosition = t;
        });

        player.on('timeupdate', () => {
            const t = player.currentTime || 0;
            if (t > maxPosition) maxPosition = t;

            if (player.playing) {
                const now = Date.now();
                if (lastTickAt > 0) {
                    const wallDelta = (now - lastTickAt) / 1000;
                    const mediaDelta = t - lastTickTime;
                    if (wallDelta > 0 && wallDelta < 2.5 && mediaDelta > 0 && mediaDelta < 2.5) {
                        playedSeconds += Math.min(wallDelta, mediaDelta);
                    }
                }
                lastTickAt = now;
                lastTickTime = t;
            }

            maybeSendProgress(false);
        });

        player.on('ended', () => {
            const d = Math.max(duration, Math.floor(player.duration || 0));
            maxPosition = Math.max(maxPosition, d);
            maybeSendProgress(true);
        });
    }

    const examBox = document.getElementById('pathExam');
    if (examBox) {
        const itemId = examBox.dataset.itemId;
        const paymentId = examBox.dataset.paymentId || '0';
        const durationSeconds = Math.max(60, parseInt(examBox.dataset.durationSeconds || '1800', 10));
        const totalQs = parseInt(examBox.dataset.total || '0', 10);
        const templates = Array.from(examBox.querySelectorAll('.exam-q-template'));
        const introEl = document.getElementById('examIntro');
        const takingEl = document.getElementById('examTaking');
        const resultEl = document.getElementById('examResultView');
        const mountEl = document.getElementById('examQuestionMount');
        const progressLabel = document.getElementById('examProgressLabel');
        const nextBtn = document.getElementById('examNextBtn');
        const prevBtn = document.getElementById('examPrevBtn');
        const finishBtn = document.getElementById('examFinishBtn');
        const startBtn = document.getElementById('examStartBtn');
        const retakeBtn = document.getElementById('examRetakeBtn');
        const timerBar = document.getElementById('examTimerBar');
        const timerLabel = document.getElementById('examTimerLabel');
        const sessionKey = 'path_exam_session_' + paymentId + '_' + itemId;

        let qIndex = 0;
        let answers = {};
        let remaining = durationSeconds;
        let startedAt = 0;
        let endsAt = 0;
        let timerId = null;
        let deadlineId = null;
        let submitting = false;

        function formatMmSs(sec) {
            const s = Math.max(0, Math.floor(sec));
            const m = Math.floor(s / 60);
            const r = s % 60;
            return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
        }

        function usedSeconds() {
            if (!startedAt) return 0;
            return Math.min(durationSeconds, Math.max(0, Math.round((Date.now() - startedAt) / 1000)));
        }

        function persistExamSession() {
            if (!startedAt || submitting) return;
            try {
                captureCurrentAnswer();
                sessionStorage.setItem(sessionKey, JSON.stringify({
                    itemId: String(itemId),
                    paymentId: String(paymentId),
                    startedAt,
                    endsAt,
                    qIndex,
                    answers,
                    active: true
                }));
            } catch (_) {}
        }

        function clearExamSession() {
            try { sessionStorage.removeItem(sessionKey); } catch (_) {}
        }

        function loadExamSession() {
            try {
                const raw = sessionStorage.getItem(sessionKey);
                if (!raw) return null;
                const data = JSON.parse(raw);
                if (!data || !data.active || String(data.itemId) !== String(itemId)) return null;
                if (!data.endsAt || !data.startedAt) return null;
                return data;
            } catch (_) {
                return null;
            }
        }

        function stopTimer() {
            if (timerId) {
                clearInterval(timerId);
                timerId = null;
            }
            if (deadlineId) {
                clearTimeout(deadlineId);
                deadlineId = null;
            }
        }

        function captureCurrentAnswer() {
            if (!mountEl) return;
            const checked = mountEl.querySelector('.exam-answer-radio:checked');
            if (!checked) return;
            const qid = checked.dataset.questionId;
            if (qid) answers[qid] = parseInt(checked.value, 10);
        }

        function updateTimerUi() {
            remaining = Math.max(0, endsAt ? Math.ceil((endsAt - Date.now()) / 1000) : durationSeconds - usedSeconds());
            if (timerLabel) timerLabel.textContent = formatMmSs(remaining);
            if (timerBar) {
                if (remaining <= 60) {
                    timerBar.classList.remove('bg-indigo-50', 'border-indigo-200', 'text-indigo-800');
                    timerBar.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                } else {
                    timerBar.classList.add('bg-indigo-50', 'border-indigo-200', 'text-indigo-800');
                    timerBar.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
                }
            }
            return remaining;
        }

        function tickTimer() {
            if (submitting) return;
            persistExamSession();
            if (updateTimerUi() <= 0) {
                stopTimer();
                submitExam(true);
            }
        }

        function showTimer() {
            if (!timerBar) return;
            timerBar.classList.remove('hidden');
            timerBar.classList.add('flex');
            updateTimerUi();
        }

        function startTimerFromDeadline() {
            stopTimer();
            const msLeft = Math.max(0, endsAt - Date.now());
            timerId = setInterval(tickTimer, 200);
            deadlineId = setTimeout(() => {
                if (!submitting) submitExam(true);
            }, msLeft + 50);
            tickTimer();
        }

        function renderQuestion() {
            if (!mountEl) return;
            mountEl.innerHTML = '';
            const tpl = templates[qIndex];
            if (!tpl) return;
            const clone = tpl.cloneNode(true);
            clone.classList.remove('hidden');
            clone.removeAttribute('aria-hidden');
            const qid = clone.dataset.questionId;
            clone.querySelectorAll('.exam-answer-radio').forEach((radio) => {
                radio.name = 'exam_answer_current';
                if (answers[qid] && String(answers[qid]) === String(radio.value)) {
                    radio.checked = true;
                }
                radio.addEventListener('change', () => {
                    if (radio.checked) {
                        answers[qid] = parseInt(radio.value, 10);
                        persistExamSession();
                    }
                });
            });
            mountEl.appendChild(clone);
            if (progressLabel) {
                progressLabel.textContent = 'سؤال ' + (qIndex + 1) + ' من ' + totalQs;
            }
            const isLast = qIndex >= totalQs - 1;
            const isFirst = qIndex <= 0;
            if (prevBtn) {
                prevBtn.classList.toggle('hidden', isFirst);
                prevBtn.disabled = false;
            }
            if (nextBtn) nextBtn.classList.toggle('hidden', isLast);
            if (finishBtn) finishBtn.classList.toggle('hidden', !isLast);
            persistExamSession();
        }

        function enterExamUi() {
            submitting = false;
            if (finishBtn) finishBtn.disabled = false;
            if (nextBtn) nextBtn.disabled = false;
            if (prevBtn) prevBtn.disabled = false;
            if (introEl) introEl.classList.add('hidden');
            if (resultEl) {
                resultEl.classList.add('hidden');
                resultEl.innerHTML = '';
            }
            if (takingEl) takingEl.classList.remove('hidden');
            showTimer();
            startTimerFromDeadline();
            renderQuestion();
        }

        function startExam() {
            answers = {};
            qIndex = 0;
            remaining = durationSeconds;
            startedAt = Date.now();
            endsAt = startedAt + (durationSeconds * 1000);
            persistExamSession();
            enterExamUi();
        }

        function resumeExam(session) {
            answers = session.answers && typeof session.answers === 'object' ? session.answers : {};
            qIndex = Math.max(0, Math.min(totalQs - 1, parseInt(session.qIndex, 10) || 0));
            startedAt = parseInt(session.startedAt, 10) || Date.now();
            endsAt = parseInt(session.endsAt, 10) || (startedAt + durationSeconds * 1000);
            // Clamp endsAt to original duration window
            const maxEnds = startedAt + durationSeconds * 1000;
            if (endsAt > maxEnds) endsAt = maxEnds;
            enterExamUi();
        }

        function tryResumeExamSession() {
            const session = loadExamSession();
            if (!session) return false;

            const left = endsAtFromSession(session) - Date.now();
            if (left <= 0) {
                // Time already ended while away — auto-submit saved answers (not a fresh start)
                answers = session.answers && typeof session.answers === 'object' ? session.answers : {};
                qIndex = Math.max(0, Math.min(totalQs - 1, parseInt(session.qIndex, 10) || 0));
                startedAt = parseInt(session.startedAt, 10) || Date.now();
                endsAt = parseInt(session.endsAt, 10) || startedAt;
                if (introEl) introEl.classList.add('hidden');
                submitExam(true);
                return true;
            }

            resumeExam(session);
            return true;
        }

        function endsAtFromSession(session) {
            return parseInt(session.endsAt, 10) || 0;
        }

        function showResult(data) {
            stopTimer();
            clearExamSession();
            if (takingEl) takingEl.classList.add('hidden');
            if (introEl) introEl.classList.add('hidden');
            if (timerBar) {
                timerBar.classList.add('hidden');
                timerBar.classList.remove('flex');
            }
            if (!resultEl) return;
            const spent = data.time_spent_seconds ?? (data.timed_out ? durationSeconds : usedSeconds());
            const ok = !!data.passed;
            resultEl.classList.remove('hidden');
            resultEl.innerHTML =
                '<div class="rounded-xl border p-5 ' + (ok ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') + '">' +
                    '<div class="flex items-start gap-3">' +
                        '<i class="fas ' + (ok ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600') + ' text-2xl mt-0.5"></i>' +
                        '<div class="flex-1">' +
                            '<p class="font-bold text-gray-900 text-lg">' + (ok ? 'نجحت في الاختبار' : 'لم تجتز الاختبار') + '</p>' +
                            (data.timed_out
                                ? '<p class="text-sm text-amber-700 mt-1 font-medium">انتهى الوقت وتم تسليم الإجابات تلقائياً واحتساب النتيجة.</p>'
                                : '') +
                            '<p class="text-sm text-gray-700 mt-1">النتيجة: ' + data.score + ' / ' + data.total +
                                ' (درجة النجاح ' + data.pass_score + ')</p>' +
                            '<p class="text-sm text-gray-600 mt-1">الوقت المستغرق: <span class="font-semibold tabular-nums">' +
                                formatMmSs(spent) + '</span> من ' + formatMmSs(durationSeconds) + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<button type="button" id="examRetakeAfterResult" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium" style="background-color:#4f46e5;color:#fff;">' +
                        '<i class="fas fa-redo"></i> إعادة المحاولة' +
                    '</button>' +
                '</div>';
            if (data.is_completed) syncPathCompletionRing(true);
            const again = document.getElementById('examRetakeAfterResult');
            if (again) again.addEventListener('click', startExam);
            setTimeout(() => location.reload(), data.timed_out ? 4500 : 2500);
        }

        function showSubmittingState(timedOut) {
            if (!takingEl) return;
            takingEl.classList.remove('hidden');
            if (mountEl) {
                mountEl.innerHTML =
                    '<div class="rounded-lg border border-amber-200 bg-amber-50 p-6 text-center text-amber-900">' +
                        '<i class="fas fa-spinner fa-spin text-2xl mb-3"></i>' +
                        '<p class="font-semibold">' + (timedOut ? 'انتهى الوقت — جاري تسليم الإجابات...' : 'جاري تسليم الاختبار...') + '</p>' +
                        '<p class="text-sm mt-1 text-amber-800">سيتم احتساب النتيجة وعرضها مباشرة.</p>' +
                    '</div>';
            }
            if (progressLabel) progressLabel.textContent = '';
            if (prevBtn) prevBtn.classList.add('hidden');
            if (nextBtn) nextBtn.classList.add('hidden');
            if (finishBtn) finishBtn.classList.add('hidden');
        }

        function getUnansweredQuestionNumbers() {
            captureCurrentAnswer();
            const missing = [];
            templates.forEach((tpl, idx) => {
                const qid = tpl.dataset.questionId;
                if (!qid) return;
                const chosen = answers[qid];
                if (chosen === undefined || chosen === null || chosen === '') {
                    missing.push(idx + 1);
                }
            });
            return missing;
        }

        function showUnansweredModal(numbers) {
            const modal = document.getElementById('examUnansweredModal');
            const listEl = document.getElementById('examUnansweredList');
            if (!modal || !listEl) return;
            listEl.textContent = numbers.join('، ');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideUnansweredModal() {
            const modal = document.getElementById('examUnansweredModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function requestFinishExam() {
            if (submitting) return;
            const missing = getUnansweredQuestionNumbers();
            if (missing.length === 0) {
                submitExam(false);
                return;
            }
            showUnansweredModal(missing);
        }

        async function submitExam(timedOut) {
            if (submitting) return;
            submitting = true;
            hideUnansweredModal();
            stopTimer();
            captureCurrentAnswer();
            if (finishBtn) finishBtn.disabled = true;
            if (nextBtn) nextBtn.disabled = true;
            if (prevBtn) prevBtn.disabled = true;
            if (timerLabel) timerLabel.textContent = timedOut ? '00:00' : formatMmSs(Math.max(0, remaining));
            showSubmittingState(!!timedOut);

            const payload = {
                answers,
                time_spent_seconds: timedOut ? durationSeconds : usedSeconds(),
                timed_out: !!timedOut
            };

            try {
                const res = await fetch(`${examBase}/${itemId}/exam`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'submit failed');
                clearExamSession();
                showResult(data);
            } catch (err) {
                submitting = false;
                persistExamSession();
                if (finishBtn) {
                    finishBtn.disabled = false;
                    finishBtn.classList.remove('hidden');
                }
                if (nextBtn) nextBtn.disabled = false;
                if (prevBtn) prevBtn.disabled = false;
                renderQuestion();
                alert(timedOut
                    ? 'انتهى الوقت وتعذر تسليم الاختبار تلقائياً. حاول مرة أخرى.'
                    : 'تعذر تسليم الاختبار');
            }
        }

        if (startBtn) startBtn.addEventListener('click', startExam);
        if (retakeBtn) retakeBtn.addEventListener('click', startExam);
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (submitting) return;
                captureCurrentAnswer();
                if (qIndex > 0) {
                    qIndex -= 1;
                    renderQuestion();
                }
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (submitting) return;
                captureCurrentAnswer();
                if (qIndex < totalQs - 1) {
                    qIndex += 1;
                    renderQuestion();
                }
            });
        }
        if (finishBtn) {
            finishBtn.addEventListener('click', () => requestFinishExam());
        }
        const unansweredCancel = document.getElementById('examUnansweredCancel');
        const unansweredConfirm = document.getElementById('examUnansweredConfirm');
        if (unansweredCancel) {
            unansweredCancel.addEventListener('click', () => {
                if (submitting) return;
                hideUnansweredModal();
            });
        }
        if (unansweredConfirm) {
            unansweredConfirm.addEventListener('click', () => {
                if (submitting) return;
                submitExam(false);
            });
        }

        window.addEventListener('beforeunload', () => {
            if (startedAt && !submitting) persistExamSession();
        });

        // Resume in-progress attempt after refresh (same question + remaining time)
        tryResumeExamSession();
    }
})();
</script>

@php
    $pathNeedsRating = $course->userNeedsRating(auth()->id());
@endphp
@if($pathNeedsRating)
@include('dashboard.courses.partials.rating-modal', [
    'ratingCourse' => $course,
    'ratingPayment' => $payment,
    'ratingQuestions' => config("course_rating.{$course->location_type}", []),
    'ratingAutoOpen' => ($pathCompletion['percent'] >= 100),
])
@endif
<x-video-protect />
@endsection

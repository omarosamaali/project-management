@extends('layouts.app')

@section('title', 'محاضرة — ' . ($course->name_ar ?? ''))

@section('content')
<style>
    /* Use full content column width (break out of academy shell max-width) */
    body.academy-shell:has(.lecture-room) .ac-main {
        max-width: none !important;
        width: 100%;
        margin: 0;
        padding: .5rem .5rem 5.75rem !important;
    }
    body.academy-shell:has(.lecture-room) .ac-page-title {
        display: none !important;
    }
    body.academy-shell:has(.lecture-room) .ac-navbar {
        padding-bottom: .25rem;
    }
    @media (min-width: 768px) {
        body.academy-shell:has(.lecture-room) .ac-main {
            padding: .65rem .75rem 1rem !important;
        }
    }
    @media (min-width: 1024px) {
        body.academy-shell:has(.lecture-room) .ac-main {
            padding: .65rem .75rem 1rem !important;
        }
        body.academy-shell:has(.lecture-room) .lecture-room {
            height: calc(100dvh - 1.5rem);
        }
    }

    .lecture-page {
        width: 100%;
        max-width: none;
    }
    .lecture-room {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
        width: 100%;
        height: calc(100dvh - 9.5rem);
        min-height: 420px;
    }
    @media (min-width: 768px) {
        .lecture-room {
            height: calc(100dvh - 8.5rem);
        }
    }
    @media (min-width: 1024px) {
        .lecture-room {
            grid-template-columns: minmax(0, 3fr) minmax(240px, 1fr);
            height: calc(100dvh - 1.5rem);
        }
        .lecture-room.is-chat-only {
            grid-template-columns: 1fr;
            max-width: 520px;
            margin-inline: auto;
        }
    }
    .lecture-room.is-chat-only .lecture-iframe-wrap {
        display: none;
    }
    .lecture-iframe-wrap {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 280px;
        overflow: hidden;
        border-radius: 0.75rem;
        background: #0f172a;
    }
    .lecture-iframe-wrap iframe,
    .lecture-iframe-wrap > div {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    .lecture-waiting {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        color: #e2e8f0;
        background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 100%);
    }
    .lecture-waiting .countdown {
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        font-variant-numeric: tabular-nums;
        color: #fff;
    }
    .lecture-chat {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 320px;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e5e7eb;
    }
    .lecture-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        background: #f8fafc;
    }
    .lecture-msg {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
        gap: 0.5rem;
        margin-bottom: 0.65rem;
        max-width: 92%;
        width: fit-content;
    }
    .lecture-msg.mine {
        margin-inline-start: auto;
        flex-direction: row-reverse;
    }
    .lecture-msg .avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        color: #fff;
        overflow: hidden;
        letter-spacing: 0.02em;
    }
    .lecture-msg .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lecture-msg .msg-body {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        max-width: 100%;
    }
    .lecture-msg.mine .msg-body { align-items: flex-end; }
    .lecture-msg .bubble {
        padding: 0.5rem 0.75rem;
        border-radius: 0.75rem;
        background: #fff;
        border: 1px solid transparent;
        font-size: 0.875rem;
        line-height: 1.5;
        word-break: break-word;
        width: fit-content;
        max-width: 100%;
        color: #1e293b;
    }
    .lecture-msg.hidden-msg .bubble {
        opacity: 0.45;
        text-decoration: line-through;
    }
    .lecture-msg .meta {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.2rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .lecture-msg.mine .meta { justify-content: flex-end; }
    .chat-lock-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        user-select: none;
    }
    .chat-lock-label {
        font-size: 0.7rem;
        color: #475569;
        white-space: nowrap;
    }
    .chat-lock-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .chat-lock-slider {
        width: 2.25rem;
        height: 1.2rem;
        border-radius: 9999px;
        background: #cbd5e1;
        position: relative;
        transition: background .2s;
        flex-shrink: 0;
    }
    .chat-lock-slider::after {
        content: '';
        position: absolute;
        top: 2px;
        inset-inline-start: 2px;
        width: 0.9rem;
        height: 0.9rem;
        border-radius: 9999px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.2);
        transition: inset-inline-start .2s;
    }
    .chat-lock-toggle input:checked + .chat-lock-slider {
        background: #dc2626;
    }
    .chat-lock-toggle input:checked + .chat-lock-slider::after {
        inset-inline-start: calc(100% - 0.9rem - 2px);
    }
</style>

<section class="lecture-page p-0">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3 px-1">
        <div class="flex items-center gap-3 min-w-0">
            @if($payment)
            <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            @else
            <a href="{{ route('dashboard.courses.show', $course) }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            @endif
            @if($course->main_image)
            <img src="{{ asset('storage/' . ltrim($course->main_image, '/')) }}"
                alt="{{ $course->name_ar }}"
                class="w-11 h-11 rounded-full object-cover border border-gray-200 shadow-sm shrink-0">
            @else
            <div class="w-11 h-11 rounded-full bg-slate-200 border border-gray-200 shadow-sm shrink-0 flex items-center justify-center text-slate-500 text-sm font-bold"
                aria-hidden="true">
                {{ mb_substr($course->name_ar ?? 'د', 0, 1) }}
            </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-gray-900 truncate">{{ $course->name_ar }}</h1>
                <p class="text-xs text-gray-500">
                    غرفة المحاضرة
                    @if($meeting['platform_label'])
                        — {{ $meeting['platform_label'] }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $course->online_link }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-external-link-alt"></i>
                {{ ($showEmbed ?? false) ? 'فتح البث في نافذة جديدة' : 'فتح الاجتماع في نافذة جديدة' }}
            </a>
            <a href="{{ route('dashboard.courses.chat-archive', $course) }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-black">
                <i class="fas fa-comments"></i>
                أرشيف النقاش
            </a>
        </div>
    </div>

    @if($openExternalTab ?? false)
    <div class="mb-3 p-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg">
        <i class="fas fa-info-circle ml-1"></i>
        الاجتماع الخارجي يُفتح في تبويب جديد تلقائياً. أبقِ هذه الصفحة مفتوحة للنقاش المباشر.
        إن لم يُفتح التبويب، استخدم زر <strong>فتح الاجتماع في نافذة جديدة</strong>.
    </div>
    @elseif(($meeting['platform'] ?? null) === 'youtube' && $meetingAvailable)
    <div class="mb-3 p-3 text-sm text-teal-900 bg-teal-50 border border-teal-200 rounded-lg">
        <i class="fas fa-broadcast-tower ml-1"></i>
        بث يوتيوب مباشر مضمّن داخل المنصة — يمكنك متابعة النقاش بجانب الفيديو.
    </div>
    @endif

    <div class="lecture-room {{ ($showEmbed ?? false) ? '' : 'is-chat-only' }}"
        id="lectureMeetingShell"
        data-meeting-available="{{ $meetingAvailable ? '1' : '0' }}"
        data-seconds-until-open="{{ (int) $secondsUntilOpen }}"
        data-open-at="{{ $meetingOpenAt->toIso8601String() }}"
        data-show-embed="{{ ($showEmbed ?? false) ? '1' : '0' }}"
        data-open-external="{{ ($openExternalTab ?? false) ? '1' : '0' }}"
        data-meeting-url="{{ $course->online_link }}"
        data-course-id="{{ $course->id }}">

        @if($showEmbed ?? false)
        <div class="lecture-iframe-wrap shadow border border-gray-200">
            @unless($meetingAvailable)
            <div class="lecture-waiting" id="lectureWaiting">
                <i class="fas fa-clock text-3xl text-teal-300"></i>
                <p class="text-base font-semibold text-white">المحاضرة لم تبدأ بعد</p>
                <p class="text-sm text-slate-300">
                    يفتح البث تلقائياً قبل الموعد بـ {{ (int) config('courses.meeting.open_before_minutes', 30) }} دقيقة
                </p>
                <p class="text-xs text-slate-400">
                    موعد البداية: {{ \Carbon\Carbon::parse($course->start_date)->format('Y-m-d h:i A') }}
                </p>
                <div class="countdown" id="lectureCountdown">--:--:--</div>
            </div>
            @else
            <iframe
                src="{{ $youtubeEmbed }}"
                title="محاضرة {{ $course->name_ar }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"
            ></iframe>
            @endunless
        </div>
        @elseif(!$meetingAvailable)
        <div class="mb-0 p-4 text-sm text-slate-700 bg-slate-100 border border-slate-200 rounded-lg" id="lectureWaitingBanner">
            <p class="font-semibold text-slate-900 mb-1">المحاضرة لم تبدأ بعد</p>
            <p class="text-xs text-slate-500 mb-2">
                يفتح رابط الاجتماع تلقائياً قبل الموعد بـ {{ (int) config('courses.meeting.open_before_minutes', 30) }} دقيقة
            </p>
            <p class="text-lg font-bold tabular-nums" id="lectureCountdown">--:--:--</p>
        </div>
        @endif

        <div class="lecture-chat shadow" id="lectureChat"
            data-course-id="{{ $course->id }}"
            data-can-moderate="{{ $canModerate ? '1' : '0' }}"
            data-is-blocked="{{ $isBlocked ? '1' : '0' }}"
            data-can-chat="{{ $canChat ? '1' : '0' }}"
            data-chat-locked="{{ $chatLocked ? '1' : '0' }}"
            data-poll-url="{{ route('dashboard.courses.chat.messages', $course) }}"
            data-store-url="{{ route('dashboard.courses.chat.store', $course) }}"
            data-hide-url="{{ url('/dashboard/courses/'.$course->id.'/chat/messages') }}"
            data-block-url="{{ route('dashboard.courses.chat.block', $course) }}"
            data-unblock-url="{{ url('/dashboard/courses/'.$course->id.'/chat/unblock') }}"
            data-lock-url="{{ route('dashboard.courses.chat.lock', $course) }}"
            data-csrf="{{ csrf_token() }}">
            <div class="px-3 py-2 border-b bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800">نقاش المحاضرة</p>
                    <p class="text-[11px] text-slate-500">مباشر — للمتدربين الحاضرين</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if($canModerate)
                    <label class="chat-lock-toggle" title="منع المتدربين من إرسال الرسائل">
                        <span class="chat-lock-label" id="chatLockLabel">{{ $chatLocked ? 'المتدربون ممنوعون' : 'السماح للمتدربين' }}</span>
                        <input type="checkbox" id="chatLockToggle" {{ $chatLocked ? 'checked' : '' }}>
                        <span class="chat-lock-slider"></span>
                    </label>
                    @endif
                    <span id="chatStatus" class="text-[11px] text-green-600">متصل</span>
                </div>
            </div>

            <div class="lecture-chat-messages" id="chatMessages">
                <p class="text-center text-xs text-gray-400 py-6" id="chatEmpty">لا رسائل بعد — ابدأ النقاش.</p>
            </div>

            <div class="p-2 border-t bg-white">
                @if(!$canChat)
                <p class="text-xs text-center text-amber-700 py-2">النقاش متاح بعد تسجيل الحضور.</p>
                @elseif($isBlocked)
                <p class="text-xs text-center text-red-600 py-2">تم حظرك من المشاركة في هذا النقاش.</p>
                @else
                <p id="chatLockedNotice" class="text-xs text-center text-amber-700 py-2"
                    style="display:{{ (!$canModerate && $chatLocked) ? 'block' : 'none' }};">
                    تم إيقاف إرسال الرسائل من قبل المحاضر.
                </p>
                <form id="chatForm" class="flex gap-2"
                    style="display:{{ (!$canModerate && $chatLocked) ? 'none' : 'flex' }};">
                    <input type="text" id="chatInput" maxlength="1000" autocomplete="off"
                        placeholder="اكتب رسالتك..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium shrink-0"
                        style="background:#0D2444;color:#fff;border:none;">
                        إرسال
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>

@include('dashboard.courses.partials.course-chat-script')

<script>
(function () {
    const shell = document.getElementById('lectureMeetingShell');
    if (!shell) return;

    const available = shell.dataset.meetingAvailable === '1';
    const openExternal = shell.dataset.openExternal === '1';
    const showEmbed = shell.dataset.showEmbed === '1';
    const meetingUrl = shell.dataset.meetingUrl || '';
    const courseId = shell.dataset.courseId || '0';
    let secondsLeft = parseInt(shell.dataset.secondsUntilOpen || '0', 10) || 0;
    const countdownEl = document.getElementById('lectureCountdown');

    function formatRemain(sec) {
        sec = Math.max(0, Math.floor(sec));
        const h = String(Math.floor(sec / 3600)).padStart(2, '0');
        const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        return h + ':' + m + ':' + s;
    }

    function openExternalMeeting() {
        if (!openExternal || !meetingUrl) return;
        const key = 'lecture-ext-opened-' + courseId;
        try {
            if (sessionStorage.getItem(key) === '1') return;
            sessionStorage.setItem(key, '1');
        } catch (e) {}
        window.open(meetingUrl, '_blank', 'noopener,noreferrer');
    }

    function onMeetingOpen() {
        if (openExternal) {
            openExternalMeeting();
            return;
        }
        window.location.reload();
    }

    if (available && openExternal) {
        openExternalMeeting();
    } else if (!available && countdownEl) {
        countdownEl.textContent = formatRemain(secondsLeft);
        const tick = setInterval(function () {
            secondsLeft -= 1;
            if (secondsLeft <= 0) {
                clearInterval(tick);
                countdownEl.textContent = '00:00:00';
                onMeetingOpen();
                return;
            }
            countdownEl.textContent = formatRemain(secondsLeft);
        }, 1000);
    }
})();
</script>
@endsection

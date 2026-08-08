@extends('layouts.app')

@section('title', 'أرشيف نقاش — ' . $course->name_ar)

@section('content')
<style>
    .lecture-chat { display:flex; flex-direction:column; height: calc(100dvh - 12rem); min-height: 420px; border-radius: .75rem; overflow:hidden; background:#fff; border:1px solid #e5e7eb; }
    .lecture-chat-messages { flex:1; overflow-y:auto; padding:.75rem; background:#f8fafc; }
    .lecture-msg { display:flex; flex-direction:row; align-items:flex-end; gap:.5rem; margin-bottom:.65rem; max-width:92%; width:fit-content; }
    .lecture-msg.mine { margin-inline-start:auto; flex-direction:row-reverse; }
    .lecture-msg .avatar { width:2rem; height:2rem; border-radius:9999px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; color:#fff; overflow:hidden; letter-spacing:.02em; }
    .lecture-msg .avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .lecture-msg .msg-body { display:flex; flex-direction:column; align-items:flex-start; min-width:0; max-width:100%; }
    .lecture-msg.mine .msg-body { align-items:flex-end; }
    .lecture-msg .bubble { padding:.5rem .75rem; border-radius:.75rem; background:#fff; border:1px solid transparent; font-size:.875rem; line-height:1.5; word-break:break-word; width:fit-content; max-width:100%; color:#1e293b; }
    .lecture-msg.hidden-msg .bubble { opacity:.55; text-decoration:line-through; text-decoration-thickness:1px; }
    .lecture-msg.hidden-msg .meta > span:not([data-hidden-author-label]):not([data-hidden-badge]) { text-decoration:line-through; text-decoration-thickness:1px; opacity:.7; }
    .lecture-msg .hidden-author-label { display:inline-flex; align-items:center; gap:.25rem; font-size:.68rem; font-weight:700; color:#b45309; background:#fffbeb; border:1px solid #fde68a; border-radius:9999px; padding:.1rem .45rem; text-decoration:none !important; opacity:1 !important; }
    .lecture-msg .meta { font-size:.7rem; color:#94a3b8; margin-top:.2rem; display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
    .lecture-msg.mine .meta { justify-content:flex-end; }
    .chat-lock-toggle { display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; user-select:none; }
    .chat-lock-label { font-size:.7rem; color:#475569; white-space:nowrap; }
    .chat-lock-toggle input { position:absolute; opacity:0; width:0; height:0; }
    .chat-lock-slider { width:2.25rem; height:1.2rem; border-radius:9999px; background:#cbd5e1; position:relative; transition:background .2s; flex-shrink:0; }
    .chat-lock-slider::after { content:''; position:absolute; top:2px; inset-inline-start:2px; width:.9rem; height:.9rem; border-radius:9999px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.2); transition:inset-inline-start .2s; }
    .chat-lock-toggle input:checked + .chat-lock-slider { background:#0b8f7f; }
    .chat-lock-toggle input:checked + .chat-lock-slider::after { inset-inline-start:calc(100% - .9rem - 2px); }
</style>

<section class="p-3 sm:p-5">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        @if($payment)
        <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm">
            <i class="fas fa-arrow-right"></i> رجوع للدورة
        </a>
        @elseif($canModerate)
        <a href="{{ route('dashboard.courses.show', $course) }}"
            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm">
            <i class="fas fa-arrow-right"></i> رجوع لإدارة الدورة
        </a>
        @endif
        <div>
            <h1 class="text-xl font-bold text-gray-900">أرشيف نقاش المحاضرة</h1>
            <p class="text-sm text-gray-500">{{ $course->name_ar }}</p>
        </div>
    </div>

    <div class="lecture-chat shadow" id="archiveChat"
        data-live="0"
        data-course-id="{{ $course->id }}"
        data-auth-user-id="{{ (int) auth()->id() }}"
        data-can-moderate="{{ $canModerate ? '1' : '0' }}"
        data-is-blocked="{{ $course->isUserChatBlocked(auth()->id()) ? '1' : '0' }}"
        data-can-chat="1"
        data-chat-locked="{{ $chatLocked ? '1' : '0' }}"
        data-poll-url="{{ route('dashboard.courses.chat.messages', $course) }}"
        data-store-url="{{ route('dashboard.courses.chat.store', $course) }}"
        data-hide-url="{{ url('/dashboard/courses/'.$course->id.'/chat/messages') }}"
        data-block-url="{{ route('dashboard.courses.chat.block', $course) }}"
        data-unblock-url="{{ url('/dashboard/courses/'.$course->id.'/chat/unblock') }}"
        data-lock-url="{{ route('dashboard.courses.chat.lock', $course) }}"
        data-csrf="{{ csrf_token() }}">
        <div class="px-3 py-2 border-b bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
            <p class="text-sm font-bold text-slate-800">سجل النقاش المحفوظ</p>
            <div class="flex items-center gap-3 shrink-0">
                @if($canModerate)
                <label class="chat-lock-toggle" title="السماح أو منع المتدربين من إرسال الرسائل">
                    <span class="chat-lock-label" id="chatLockLabel">{{ $chatLocked ? 'المتدربون ممنوعون' : 'السماح للمتدربين' }}</span>
                    <input type="checkbox" id="chatLockToggle" {{ $chatLocked ? '' : 'checked' }}>
                    <span class="chat-lock-slider"></span>
                </label>
                @endif
                <span id="chatStatus" class="text-[11px] text-green-600">جاهز</span>
            </div>
        </div>
        <div class="lecture-chat-messages" id="chatMessages">
            <p class="text-center text-xs text-gray-400 py-6" id="chatEmpty">لا توجد رسائل محفوظة.</p>
        </div>
        <div class="p-2 border-t bg-white">
            @if($course->isUserChatBlocked(auth()->id()) && !$canModerate)
            <p class="text-xs text-center text-red-600 py-2">تم حظرك من المشاركة في هذا النقاش.</p>
            @else
            <p id="chatLockedNotice" class="text-xs text-center text-amber-700 py-2"
                style="display:{{ (!$canModerate && $chatLocked) ? 'block' : 'none' }};">
                تم إيقاف إرسال الرسائل من قبل المحاضر.
            </p>
            <form id="chatForm" class="flex gap-2"
                style="display:{{ (!$canModerate && $chatLocked) ? 'none' : 'flex' }};">
                <input type="text" id="chatInput" maxlength="1000" autocomplete="off"
                    placeholder="أضف رسالة للأرشيف..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium shrink-0"
                    style="background:#0D2444;color:#fff;border:none;">إرسال</button>
            </form>
            @endif
        </div>
    </div>
</section>

@include('dashboard.courses.partials.course-chat-script')
@endsection

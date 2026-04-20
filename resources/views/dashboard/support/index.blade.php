@extends('layouts.app')

@section('title', 'الدعم')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.support.index') }}" second="المشاريع" />
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            @if(Auth::user()->role == 'admin')
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.support.index') }}" method="GET" class="flex items-center">
                        <label for="search" class="sr-only">بحث</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                @if(request()->search == null)
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                                @else
                                <a href="{{ route('dashboard.requests.index') }}">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="إبحث باسم العميل" required="">
                        </div>
                    </form>
                </div>
                {{-- <div class="visible w-full md:w-auto flex flex-col md:flex-row md:items-center justify-end !ml-0">
                    <a href="{{ route('dashboard.requests.create') }}"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        إضافة طلب
                    </a>
                </div> --}}
            </div>
            @endif
            <div class="overflow-x-auto">
                @if(session('success'))
                <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mx-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-times-circle"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">رقم المشروع</th>
                            <th scope="col" class="px-4 py-3">العميل</th>
                            <th scope="col" class="px-4 py-3">اسم النظام</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">رسائل غير مقروءة</th>
                            <th scope="col" class="px-4 py-3">آخر رسالة</th>
                            <th scope="col" class="px-4 py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supports as $support)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $support->id }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $support->subject }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $support->user->name }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.requests.show', $support->request_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $support->request->system->name_ar }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @if($support->status == 'open')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @elseif($support->status == 'in_progress')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($support->unreadMessages->count() > 0)
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">
                                    {{ $support->unreadMessages->count() }} جديدة
                                </span>
                                @else
                                <span class="text-gray-500">لا يوجد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($support->messages->count() > 0)
                                {{ $support->last_message_at?->diffForHumans() }}
                                @else
                                لا يوجد
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.support.show', $support->id) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                                    فتح المحادثة
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center px-4 py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>لا توجد تذاكر دعم</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <div class="p-4">
                        {{-- {{ $support->links() }} --}}
                    </div>
            </div>

        </div>
    </div>
</section>

{{-- ===== قسم رسائل التواصل من API ===== --}}
<section class="!pl-0 p-3 sm:p-5 mt-2">
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">رسائل التواصل</h2>
                    <span id="api-unread-badge" class="hidden px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"></span>
                    <span id="api-total-badge" class="hidden px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs"></span>
                </div>
                <button onclick="deleteAllMessages()"
                    class="flex items-center gap-1 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs">
                    <i class="fas fa-trash-alt"></i> حذف الكل
                </button>
            </div>

            <div id="api-alert" class="hidden mx-4 mt-3 p-3 rounded-lg text-sm"></div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">الاسم</th>
                            <th class="px-4 py-3">البريد الإلكتروني</th>
                            <th class="px-4 py-3">الموضوع</th>
                            <th class="px-4 py-3">الرسالة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">الرد</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="api-messages-body">
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl"></i>
                                <p class="mt-2 text-sm">جاري تحميل الرسائل...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Modal الرد --}}
<div id="reply-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">الرد على الرسالة</h3>
            <button onclick="closeReplyModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="reply-original-msg" class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300"></div>
        <textarea id="reply-text" rows="4"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-sm dark:bg-gray-700 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="اكتب ردك هنا..."></textarea>
        <div class="flex justify-end gap-3 mt-4">
            <button onclick="closeReplyModal()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">إلغاء</button>
            <button onclick="submitReply()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-paper-plane ml-1"></i> إرسال الرد
            </button>
        </div>
    </div>
</div>

<script>
const PROXY_BASE = '{{ route("dashboard.support.api.messages") }}';
const CSRF = '{{ csrf_token() }}';
let currentReplyId = null;

async function loadMessages() {
    try {
        const res = await fetch(PROXY_BASE);
        const data = await res.json();

        const badge = document.getElementById('api-unread-badge');
        const totalBadge = document.getElementById('api-total-badge');
        badge.textContent = `${data.unread} غير مقروءة`;
        badge.classList.remove('hidden');
        totalBadge.textContent = `الإجمالي: ${data.total}`;
        totalBadge.classList.remove('hidden');

        const tbody = document.getElementById('api-messages-body');
        if (!data.messages || data.messages.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-3xl mb-2"></i><p>لا توجد رسائل</p></td></tr>`;
            return;
        }

        tbody.innerHTML = data.messages.map(msg => `
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 ${msg.read == 0 ? 'bg-blue-50 dark:bg-blue-900/10' : ''}">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${msg.id}</td>
                <td class="px-4 py-3 text-gray-800 dark:text-white font-medium">${escHtml(msg.name)}</td>
                <td class="px-4 py-3 text-xs">${escHtml(msg.email)}</td>
                <td class="px-4 py-3">${escHtml(msg.subject)}</td>
                <td class="px-4 py-3 max-w-xs truncate" title="${escHtml(msg.message)}">${escHtml(msg.message)}</td>
                <td class="px-4 py-3">
                    ${msg.read == 0
                        ? '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">غير مقروءة</span>'
                        : '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">مقروءة</span>'}
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="${msg.reply ? escHtml(msg.reply) : ''}">
                    ${msg.reply ? escHtml(msg.reply) : '<span class="text-gray-400">لا يوجد رد</span>'}
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">${msg.createdAt}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button onclick="openReplyModal(${msg.id}, '${escHtml(msg.name)}', \`${escHtml(msg.message)}\`)"
                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700">
                            <i class="fas fa-reply ml-1"></i>رد
                        </button>
                        <button onclick="deleteMessage(${msg.id})"
                            class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs hover:bg-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        document.getElementById('api-messages-body').innerHTML =
            `<tr><td colspan="9" class="text-center py-8 text-red-500">فشل تحميل الرسائل</td></tr>`;
    }
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

async function deleteMessage(id) {
    if (!confirm('هل تريد حذف هذه الرسالة؟')) return;
    try {
        const res = await fetch(PROXY_BASE.replace('/api-messages', `/api-messages/${id}`), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (res.ok) {
            showAlert('تم حذف الرسالة بنجاح', 'success');
            loadMessages();
        } else {
            showAlert('فشل في حذف الرسالة', 'error');
        }
    } catch { showAlert('حدث خطأ أثناء الحذف', 'error'); }
}

async function deleteAllMessages() {
    if (!confirm('هل تريد حذف جميع الرسائل؟ لا يمكن التراجع عن هذا الإجراء.')) return;
    try {
        const res = await fetch(PROXY_BASE.replace('/api-messages', '/api-messages/all'), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (res.ok) {
            showAlert('تم حذف جميع الرسائل بنجاح', 'success');
            loadMessages();
        } else {
            showAlert('فشل في حذف الرسائل', 'error');
        }
    } catch { showAlert('حدث خطأ أثناء الحذف', 'error'); }
}

function openReplyModal(id, name, message) {
    currentReplyId = id;
    document.getElementById('reply-original-msg').innerHTML =
        `<strong>${escHtml(name)}:</strong> ${escHtml(message)}`;
    document.getElementById('reply-text').value = '';
    document.getElementById('reply-modal').classList.remove('hidden');
}

function closeReplyModal() {
    document.getElementById('reply-modal').classList.add('hidden');
    currentReplyId = null;
}

async function submitReply() {
    const reply = document.getElementById('reply-text').value.trim();
    if (!reply) { alert('يرجى كتابة نص الرد'); return; }
    try {
        const res = await fetch(PROXY_BASE.replace('/api-messages', `/api-messages/${currentReplyId}/reply`), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ reply })
        });
        if (res.ok) {
            closeReplyModal();
            showAlert('تم إرسال الرد بنجاح', 'success');
            loadMessages();
        } else {
            showAlert('فشل في إرسال الرد', 'error');
        }
    } catch { showAlert('حدث خطأ أثناء إرسال الرد', 'error'); }
}

function showAlert(msg, type) {
    const el = document.getElementById('api-alert');
    el.textContent = msg;
    el.className = type === 'success'
        ? 'mx-4 mt-3 p-3 rounded-lg text-sm bg-green-50 text-green-800 border border-green-200'
        : 'mx-4 mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-200';
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

document.addEventListener('DOMContentLoaded', loadMessages);
</script>

@endsection
@extends('layouts.app')

@section('content')
@php
$myStatus = $session->getParticipantStatus(auth()->user()->email);
@endphp
<section class="p-6 text-right" dir="rtl">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.sessions.index') }}" second="تفاصيل الاجتماع" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- الجزء الأيمن: تفاصيل الاجتماع وإدارة الأدمن --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. لوحة تحكم الأدمن (تظهر للأدمن فقط) --}}
            @if(auth()->user()->role === 'admin')
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border-2 border-blue-100 dark:border-blue-900/30">
                <h3 class="text-lg font-bold mb-4 text-blue-700 dark:text-blue-400 flex items-center">
                    <i class="fas fa-user-shield ml-2"></i>
                    إدارة موعد الاجتماع والمدعوين
                </h3>

                <form action="{{ route('dashboard.sessions.update', $session->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- تحديد الموعد --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">وقت
                                الاجتماع</label>
                            <input type="datetime-local" name="session_time"
                                value="{{ $session->session_time ? $session->session_time->format('Y-m-d\TH:i') : '' }}"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500"
                                required>
                        </div>

                        {{-- رابط الاجتماع --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">رابط الاجتماع
                                (Zoom/Meet)</label>
                            <input type="url" name="session_link" value="{{ $session->session_link }}"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500"
                                placeholder="https://..." required>
                        </div>
                    </div>

                    {{-- اختيار المدعوين --}}
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">دعوة
                            الأشخاص</label>
                        <select name="user_ids[]" multiple
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white select2" required>
                            @foreach($users as $user)
                            @php
                            $isAlreadyInvited = collect($session->invitees)->contains('email', $user->email);
                            @endphp
                            <option value="{{ $user->id }}" {{ $isAlreadyInvited ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1 italic">اضغط Ctrl لاختيار أكثر من شخص</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold transition-colors shadow-lg shadow-blue-200 dark:shadow-none">
                        تحديث بيانات الاجتماع وإرسال الدعوات
                    </button>
                </form>
            </div>
            @endif

            {{-- 2. بطاقة معلومات الاجتماع (تظهر للجميع) --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $session->title }}</h1>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium {{ $session->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                        {{ $session->status_name }}
                    </span>
                </div>
                {{-- ملخص الحضور المحدث --}}
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4 italic text-sm border-b pb-2">إحصائيات
                        الاجتماع</h3>
                    <div class="space-y-4">
                        {{-- الحضور الفعلي (attended) --}}
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-blue-700 font-bold">حضروا الاجتماع بالفعل:</span>
                            <span class="bg-blue-600 text-white px-3 py-0.5 rounded-full text-xs font-bold shadow-sm">
                                {{ $session->attended_count }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-green-600 font-medium">وافقوا على الحضور:</span>
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $session->accepted_count }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-black font-medium">رافضون/غائبون:</span>
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ collect($session->invitees)->whereIn('status', ['rejected', 'absent'])->count() }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-50">
                            <span class="text-gray-500">إجمالي المدعوين:</span>
                            <span class="font-bold">{{ count($session->invitees ?? []) }}</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm"> {{-- تم تغيير grid-cols-2 إلى 3 --}}
                    {{-- موعد الاجتماع --}}
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-alt ml-2 text-blue-500"></i>
                        <strong>الموعد:</strong>
                        <span class="mr-1">{{ $session->session_time ? $session->session_time->format('Y-m-d H:i') : 'لم
                            يحدد بعد'
                            }}</span>
                    </div>

                    {{-- منظم الاجتماع --}}
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-user-tie ml-2 text-blue-500"></i>
                        <strong>المنظم:</strong>
                        <span class="mr-1">{{ $session->user->name }}</span>
                    </div>

                    {{-- وقت إنشاء الاجتماع (الإضافة الجديدة) --}}
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-clock ml-2 text-green-500"></i>
                        <strong>تاريخ الإنشاء:</strong>
                        <span class="mr-1" title="{{ $session->created_at->format('Y-m-d H:i:s') }}">
                            {{ $session->created_at->diffForHumans() }}
                            {{-- استخدمت diffForHumans لإظهارها بشكل ودي مثل "منذ ساعتين" --}}
                        </span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-1">السبب:</h3>
                        <p
                            class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg leading-relaxed">
                            {{ $session->reason }}</p>
                    </div>
                    @if($session->details)
                    <div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-1">التفاصيل الإضافية:</h3>
                        <p
                            class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg leading-relaxed">
                            {{ $session->details }}</p>
                    </div>
                    @endif
                </div>

                {{-- تعديل زر الانضمام --}}
                {{-- تعديل زر الانضمام --}}
                @if($session->session_link)
                @php
                $canJoin = $session->canJoinNow();
                // الشرط الجديد: مسموح بالدخول إذا كان المستخدم أدمن "أو" إذا قبل الدعوة وحالتها accepted/attended
                $isAdmin = auth()->user()->role === 'admin';
                $isAccepted = ($myStatus === 'accepted' || $myStatus === 'attended');
                $hasPermissionToJoin = $isAdmin || $isAccepted;
                @endphp

                <div
                    class="mt-6 p-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="font-bold text-gray-700 dark:text-white">رابط الاجتماع:</span>

                    {{-- الحالة 1: لديه صلاحية (أدمن أو قبل الدعوة) والوقت حان --}}
                    @if($hasPermissionToJoin && $canJoin)
                    <a href="{{ $session->session_link }}" target="_blank"
                        class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-all shadow-md animate-pulse">
                        <i class="fas fa-video ml-2"></i>
                        الانضمام للاجتماع الآن
                    </a>

                    {{-- الحالة 2: مستخدم عادي ولم يقبل الدعوة بعد --}}
                    @elseif(!$isAdmin && !$isAccepted)
                    <span class="text-sm text-amber-600 bg-amber-50 px-3 py-1 rounded-lg border border-amber-200">
                        <i class="fas fa-exclamation-triangle ml-1"></i>
                        يجب قبول الدعوة أولاً لتتمكن من الانضمام
                    </span>

                    {{-- الحالة 3: لديه صلاحية (أدمن أو قبل الدعوة) لكن الوقت لم يحن بعد --}}
                    @else
                    <span class="text-sm text-gray-500 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200">
                        <i class="fas fa-clock ml-1"></i>
                        سيظهر زر الانضمام قبل الموعد بـ 10 دقائق
                    </span>
                    @endif
                </div>
                @endif
            </div>
            {{-- 3. جدول الحضور (تظهر للجميع) --}}
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">قائمة الحضور وحالاتهم</h3>
                    <span class="text-xs text-gray-400">إجمالي المدعوين: {{ count($session->invitees ?? []) }}</span>
                </div>
                <table class="w-full text-right">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-500 text-sm">
                        <tr>
                            <th class="p-4">المدعو</th>
                            <th class="p-4">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($session->invitees ?? [] as $invitee)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                            @php
                            $isNewSystem = is_array($invitee) && isset($invitee['email']);
                            $email = $isNewSystem ? $invitee['email'] : $invitee;
                            $status = $isNewSystem ? ($invitee['status'] ?? 'pending') : 'pending';
                            @endphp
                            <td class="p-4 text-gray-700 dark:text-gray-300">{{ $email }}</td>
                            <td class="p-4">
                                @php
                                $badgeStyle = match($status) {
                                'accepted' => 'bg-green-100 text-green-700 ring-green-600/20',
                                'attended' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
                                'rejected' => 'bg-red-100 text-red-700 ring-red-600/20',
                                'absent' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                default => 'bg-amber-50 text-amber-600 ring-amber-500/10'
                                };
                                $statusText = match($status) {
                                'accepted' => 'تم القبول',
                                'attended' => 'حضر الاجتماع',
                                'rejected' => 'تم الرفض',
                                'absent' => 'غائب/معتذر',
                                default => 'قيد الانتظار'
                                };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-bold ring-1 ring-inset {{ $badgeStyle }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="p-8 text-center text-gray-400 italic">لا يوجد مدعوين لهذا الاجتماع
                                بعد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- الجزء الأيسر: الإجراءات الشخصية والإحصائيات --}}
        <div class="space-y-6">
            @php
            $myStatus = $session->getParticipantStatus(auth()->user()->email);
            @endphp

            {{-- صندوق رد المستخدم على الدعوة --}}
            @if($myStatus)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border-t-4 border-blue-600">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">ردك على الدعوة</h3>
                <div class="mb-4">
                    <span class="text-xs text-gray-500 block mb-1">حالتك الحالية:</span>
                    <span
                        class="font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-lg block text-center">
                        {{ match($myStatus) {
                        'pending' => 'بانتظار ردك',
                        'accepted' => 'تم القبول',
                        'rejected' => 'تم الرفض',
                        'attended' => 'حاضر الآن',
                        'absent' => 'معتذر عن الحضور',
                        default => $myStatus
                        } }}
                    </span>
                </div>

                <form action="{{ route('dashboard.sessions.updateStatus', $session->id) }}" method="POST"
                    class="space-y-4">
                    @csrf
                    <label class="text-xs font-semibold text-gray-500 block mb-2">تحديث الحالة:</label>

                    <input type="hidden" name="status" id="status-input">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        {{-- زر قبول الاجتماع --}}
                        <button type="button" onclick="submitStatusForm('accepted')" {{ $myStatus=='accepted'
                            ? 'disabled' : '' }}
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border transition-all
            {{ $myStatus == 'accepted' ? 'bg-green-50 text-green-400 border-green-200 cursor-not-allowed' : 'bg-white text-green-600 border-green-600 hover:bg-green-600 hover:text-white' }}">
                            <i class="fas fa-check-circle ml-2"></i> قبول الاجتماع
                        </button>

                        {{-- زر رفض الاجتماع --}}
                        <button type="button" onclick="submitStatusForm('rejected')" {{ $myStatus=='rejected'
                            ? 'disabled' : '' }}
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border transition-all
            {{ $myStatus == 'rejected' ? 'bg-red-50 text-red-400 border-red-200 cursor-not-allowed' : 'bg-white text-black border-red-600 hover:bg-black hover:text-white' }}">
                            <i class="fas fa-times-circle ml-2"></i> رفض الاجتماع
                        </button>

                        {{-- زر تسجيل حضور --}}
                        <button type="button" onclick="submitStatusForm('attended')" {{ $myStatus=='attended'
                            ? 'disabled' : '' }}
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border transition-all
            {{ $myStatus == 'attended' ? 'bg-blue-50 text-blue-400 border-blue-200 cursor-not-allowed' : 'bg-white text-blue-600 border-blue-600 hover:bg-blue-600 hover:text-white' }}">
                            <i class="fas fa-user-check ml-2"></i> تسجيل حضور
                        </button>

                        {{-- زر اعتذار --}}
                        <button type="button" onclick="submitStatusForm('absent')" {{ $myStatus=='absent' ? 'disabled'
                            : '' }}
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border transition-all
            {{ $myStatus == 'absent' ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-600 border-gray-600 hover:bg-gray-600 hover:text-white' }}">
                            <i class="fas fa-user-slash ml-2"></i> اعتذار عن الحضور
                        </button>
                    </div>

                    <p class="text-[10px] text-gray-400 text-center italic mt-2">سيتم تحديث الحالة فور النقر على الزر.
                    </p>
                </form>

                <script>
                    function submitStatusForm(status) {
        // تعيين القيمة في الحقل المخفي
        document.getElementById('status-input').value = status;
        // إرسال الفورم
        document.getElementById('status-input').closest('form').submit();
    }
                </script>
            </div>
            @endif

            {{-- ملخص الحضور (إحصائيات) --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 italic text-sm border-b pb-2">ملخص استجابة
                    المدعوين</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-green-600 font-medium">موافقون:</span>
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold">
                            {{ collect($session->invitees)->where('status', 'accepted')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-black font-medium">رافضون/غائبون:</span>
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">
                            {{ collect($session->invitees)->whereIn('status', ['rejected', 'absent'])->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-amber-600 font-medium">بانتظار الرد:</span>
                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-bold">
                            {{ collect($session->invitees)->where('status', 'pending')->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
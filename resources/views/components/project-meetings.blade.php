@props(['SpecialRequest'])

{{-- 1. تجهيز قائمة الحضور المتاحة --}}
@php
$allPossibleAttendees = collect();
if ($SpecialRequest->user) {
$allPossibleAttendees->push($SpecialRequest->user);
}
if ($SpecialRequest->partners) {
foreach ($SpecialRequest->partners as $partner) {
$allPossibleAttendees->push($partner);
}
}
if ($SpecialRequest->projectManager && $SpecialRequest->projectManager->user) {
$allPossibleAttendees->push($SpecialRequest->projectManager->user);
}
$allPossibleAttendees = $allPossibleAttendees->unique('id');
@endphp

<div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold flex items-center gap-2 text-gray-800 dark:text-white">
            <i class="fas fa-video text-black"></i> الاجتماعات المجدولة
        </h2>

        {{-- @if (auth()->user()->role === 'admin' || $SpecialRequest->projectManager?->user_id == auth()->id()) --}}
        <button type="button" onclick="toggleModal('addMeetingModal', true)"
            class="bg-black text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-lg">
            <i class="fas fa-plus ml-1"></i> إضافة اجتماع جديد
        </button>
        {{-- @endif --}}
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- قائمة الاجتماعات --}}
{{-- قائمة الاجتماعات --}}
<div class="grid grid-cols-1 gap-6">
    @forelse($SpecialRequest->projectMeetings as $meeting)
    @php
    // 1. جلب المدعوين لهذا الاجتماع تحديداً
    $participants = $meeting->participants;

    // 2. التحقق هل المستخدم الحالي مدعو أم لا؟
    $isInvited = $participants->contains(auth()->id());
    $isCreator = $meeting->created_by == auth()->id();
    $isAdmin = auth()->user()->role === 'admin';

    // 3. جلب بيانات المستخدم الحالي من جدول الربط (لمعرفة حالته: موافق/معتذر)
    $myParticipant = $participants->firstWhere('id', auth()->id());
    $currentUserStatus = $myParticipant ? $myParticipant->pivot->status : 'pending';
    @endphp

    {{-- حماية: لا يظهر الاجتماع إلا للمدعو، المنشئ، أو الأدمن --}}
    @if ($isInvited || $isCreator || $isAdmin)
    <div
        class="border dark:border-gray-700 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-900/30 transition-all relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1 {{ $meeting->status_color }}"></div>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6" dir="rtl">
            <div class="flex flex-1 items-start gap-4 text-right">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm text-black">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white">{{ $meeting->title }}</h4>
                    <p class="text-xs text-gray-500 italic">
                        {{ $meeting->start_at->format('Y-m-d') }} | من {{ $meeting->start_at->format('h:i A') }}
                    </p>

                    {{-- عرض كافة الحضور وحالاتهم --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        <p class="w-full text-xs font-bold text-gray-400 mb-1">الحضور الموجهة لهم الدعوة:</p>
                        @foreach ($participants as $participant)
                        <div
                            class="flex items-center gap-2 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full border dark:border-gray-700 shadow-sm">
                            <span
                                class="text-[11px] font-bold {{ $participant->id == auth()->id() ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $participant->name }}
                            </span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500">
                                {{-- تحويل الحالة للعربية --}}
                                @lang('status.' . $participant->pivot->status)
                                {{-- ملاحظة: يفضل استخدام مصفوفة الترجمة التي وضعتها سابقاً هنا --}}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- أزرار الموافقة والاعتذار --}}
            <div class="flex flex-col gap-2">
                @if (!$isCreator && $currentUserStatus === 'pending' && now() < $meeting->end_at)
                    <div class="flex gap-2">
                        <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="accepted">
                            <button
                                class="bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold">موافقة</button>
                        </form>
                        <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="declined">
                            <button
                                class="bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold">اعتذار</button>
                        </form>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif
    @empty
    <div class="text-center py-12 text-gray-400 border-2 border-dashed rounded-3xl dark:border-gray-700">
        لا توجد اجتماعات مدعو إليها حالياً
    </div>
    @endforelse
</div>
    {{-- مودال الإضافة --}}
    <div id="addMeetingModal"
        class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-right"
        dir="rtl">
        <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl p-6">
            <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
                <h3 class="text-xl font-bold dark:text-white">جدولة اجتماع جديد</h3>
                <button onclick="toggleModal('addMeetingModal', false)"
                    class="text-gray-400 hover:text-black text-2xl">&times;</button>
            </div>
            <form action="{{ route('meetings.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">

                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان الاجتماع</label>
                    <input type="text" name="title" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">اختيار الحضور</label>
                    <div
                        class="grid grid-cols-2 gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl max-h-40 overflow-y-auto border dark:border-gray-700">
                        @foreach ($allPossibleAttendees as $person)
                        <label
                            class="flex items-center gap-2 cursor-pointer hover:bg-white dark:hover:bg-gray-800 p-1 rounded transition">
                            <input type="checkbox" name="attendees[]" value="{{ $person->id }}"
                                class="rounded text-black">
                            <span class="text-xs dark:text-gray-300">{{ $person->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">رابط الاجتماع</label>
                    <input type="url" name="meeting_link"
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-gray-400">الوقت</label>
                        <input type="datetime-local" name="start_at" required
                            class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت الانتهاء</label>
                        <input type="datetime-local" name="end_at" required
                            class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit"
                        class="flex-1 bg-black text-white py-3 rounded-xl font-bold hover:bg-red-700">حفظ
                        الاجتماع</button>
                    <button type="button" onclick="toggleModal('addMeetingModal', false)"
                        class="flex-1 bg-gray-100 dark:bg-gray-700 dark:text-white py-3 rounded-xl">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function joinMeeting(meetingId, url) {
        fetch(`/project-meetings/${meetingId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: 'attended'
            })
        }).then(() => {
            window.open(url, '_blank');
            location.reload();
        });
    }
    </script>
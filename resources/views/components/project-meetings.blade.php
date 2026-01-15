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

        @if (auth()->user()->role === 'admin' || $SpecialRequest->projectManager?->user_id == auth()->id())
            <button type="button" onclick="toggleModal('addMeetingModal', true)"
                class="bg-black text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-lg">
                <i class="fas fa-plus ml-1"></i> إضافة اجتماع جديد
            </button>
        @endif
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
    <div class="grid grid-cols-1 gap-6">
        @forelse($SpecialRequest->projectMeetings as $meeting)
            @php
                // حماية: تحويل العلاقة إلى collection إذا كانت null لمنع خطأ member function where() on null
                $participants = collect($meeting->participants);
                $myParticipant = $participants->firstWhere('id', auth()->id());

                $currentUserStatus =
                    $myParticipant && $myParticipant->pivot ? $myParticipant->pivot->status : 'pending';

                $diffInMinutes = now()->diffInMinutes($meeting->start_at, false);
                $isTimeForLink = $diffInMinutes <= 10 && now() <= $meeting->end_at;
                $canSeeLink = $isTimeForLink && $currentUserStatus === 'accepted';
                $isCreator = $meeting->created_by == auth()->id();
            @endphp

            <div
                class="border dark:border-gray-700 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-900/30 transition-all relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-1 {{ $meeting->status_color }}"></div>

                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6" dir="rtl">
                    <div class="flex flex-1 items-start gap-4 text-right">
                        <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm text-black">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white">{{ $meeting->title }}</h4>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $meeting->status_color }}">
                                    {{ $meeting->status_label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 italic">
                                <i class="far fa-clock ml-1"></i>
                                {{ $meeting->start_at->format('Y-m-d') }} | من {{ $meeting->start_at->format('h:i A') }}
                                إلى {{ $meeting->end_at->format('h:i A') }}
                            </p>

                            {{-- جدول حالات الحضور --}}
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($participants as $participant)
                                    {{-- شرط: إظهار الجميع للمنشئ، وإظهار الشخص لنفسه فقط للآخرين --}}
                                    @if ($isCreator || auth()->user()->role === 'admin' || $participant->id == auth()->id())
                                        <div
                                            class="flex items-center justify-between bg-white/50 dark:bg-gray-800/50 p-2 rounded-xl border {{ $participant->id == auth()->id() ? 'border-blue-200' : 'border-gray-100' }}">
                                            <span
                                                class="text-[11px] font-bold {{ $participant->id == auth()->id() ? 'text-blue-600' : 'text-gray-600' }}">
                                                {{ $participant->name }}
                                                {{ $participant->id == auth()->id() ? '(أنت)' : '' }}
                                            </span>

                                            @php
                                                $statusAr =
                                                    [
                                                        'pending' => 'بانتظار الرد',
                                                        'accepted' => 'موافق',
                                                        'declined' => 'يعتذر',
                                                        'attended' => 'حضر الاجتماع',
                                                        'absent' => 'غائب',
                                                    ][$participant->pivot->status] ?? 'غير محدد';
                                            @endphp

                                            <span class="text-[9px] px-2 py-0.5 rounded-md font-bold bg-gray-100">
                                                {{ $statusAr }}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- الأكشن --}}
                    <div class="flex flex-col gap-3 w-full lg:w-auto">
                        @if (!$isCreator && $currentUserStatus === 'pending')
                            <div class="flex gap-2">
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST"
                                    class="flex-1">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-700">موافقة</button>
                                </form>
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST"
                                    class="flex-1">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="declined">
                                    <button
                                        class="w-full bg-red-100 text-black px-4 py-2 rounded-xl text-xs font-bold">اعتذار</button>
                                </form>
                            </div>
                        @endif

                        @if ($canSeeLink)
                            <a href="{{ $meeting->meeting_link }}" target="_blank"
                                class="bg-blue-600 text-white px-6 py-2 rounded-xl text-center text-sm font-bold animate-pulse shadow-lg">
                                <i class="fas fa-link ml-1"></i> دخول الاجتماع
                            </a>
                        @elseif($currentUserStatus === 'accepted' && !$isTimeForLink && now() < $meeting->start_at)
                            <span
                                class="text-[10px] text-orange-600 bg-orange-50 p-2 rounded-lg text-center border border-orange-100">
                                الرابط يفتح قبل البدء بـ 10 دقائق
                            </span>
                        @endif

                        @if ($isCreator || auth()->user()->role === 'admin')
                            <div class="flex gap-2 justify-end mt-2">
                                <button type="button"
                                    onclick="openEditMeetingModal('{{ $meeting->id }}', '{{ $meeting->title }}', {{ json_encode($participants->pluck('id')) }}, '{{ $meeting->meeting_link }}', '{{ $meeting->start_at->format('Y-m-d\TH:i') }}', '{{ $meeting->end_at->format('Y-m-d\TH:i') }}')"
                                    class="p-2 text-blue-500 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('meetings.destroy', $meeting->id) }}" method="POST"
                                    onsubmit="return confirm('هل تريد حذف الاجتماع؟')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="p-2 text-black bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400 border-2 border-dashed rounded-3xl dark:border-gray-700">
                لا توجد اجتماعات مسجلة حالياً
            </div>
        @endforelse
    </div>
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

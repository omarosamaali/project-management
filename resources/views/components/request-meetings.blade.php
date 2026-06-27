@props(['SpecialRequest' => null])

@php
$currentRequest = $SpecialRequest;

$allPossibleAttendees = collect();
if ($currentRequest) {
    $allPossibleAttendees = $currentRequest->allProjectClients();
    foreach ($currentRequest->assignableTeamMembers() as $member) {
        $allPossibleAttendees->push($member);
    }
}
$allPossibleAttendees = $allPossibleAttendees
    ->filter(fn ($u) => ($u->status ?? 'active') !== 'blocked')
    ->unique('id');

$meetings = $currentRequest ? $currentRequest->projectMeetings : collect();
@endphp

<div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold flex items-center gap-2 text-gray-800 dark:text-white">
            <i class="fas fa-video text-black"></i> الاجتماعات المجدولة
        </h2>
        <button type="button" onclick="toggleModal('addMeetingModal', true)"
            class="bg-black text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-lg">
            <i class="fas fa-plus ml-1"></i> إضافة اجتماع جديد
        </button>
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

    <div class="grid grid-cols-1 gap-6">
        @forelse($meetings as $meeting)
        @php
        $participants = collect($meeting->participants);
        $myParticipant = $participants->firstWhere('id', auth()->id());
        $currentUserStatus = ($myParticipant && $myParticipant->pivot) ? $myParticipant->pivot->status : 'pending';

        $diffInMinutes = now()->diffInMinutes($meeting->start_at, false);
        $isTimeForLink = $diffInMinutes <= 10 && now() <= $meeting->end_at;
        $canSeeLink = $isTimeForLink && $currentUserStatus === 'accepted';
        $isCreator = $meeting->created_by == auth()->id();
        $isInvited = $participants->contains('id', auth()->id());
        $isAdmin = auth()->user()->role === 'admin';
        $isProjectClient = $currentRequest
            ? $currentRequest->userCanViewAllProjectIssues(auth()->id(), auth()->user()->role)
            : false;

        $isOnline = ($meeting->meeting_type ?? 'online') !== 'in_person';
        @endphp

        @if ($isInvited || $isCreator || $isAdmin || $isProjectClient)
            <div class="border dark:border-gray-700 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-900/30 transition-all relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-1 {{ $meeting->status_color }}"></div>

                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6" dir="rtl">
                    <div class="flex flex-1 items-start gap-4 text-right">
                        <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm text-black">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            {{-- العنوان + الحالة + نوع الاجتماع --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white">{{ $meeting->title }}</h4>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $meeting->status_color }}">
                                    {{ $meeting->status_label }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $meeting->meeting_type_badge }}">
                                    <i class="fas {{ $isOnline ? 'fa-video' : 'fa-map-marker-alt' }} ml-0.5"></i>
                                    {{ $meeting->meeting_type_label }}
                                </span>
                            </div>

                            {{-- التاريخ والوقت بالصيغة العربية --}}
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 font-medium">
                                {{ $meeting->formattedDateRange() }}
                            </p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                <i class="fas fa-globe ml-1"></i>المنطقة الزمنية: Asia/Dubai
                            </p>

                            {{-- جدول حالات الحضور --}}
                            <div class="mt-4 flex flex-wrap gap-2">
                                <p class="w-full text-xs font-bold text-gray-400 mb-1">الحضور الموجهة لهم الدعوة:</p>
                                @foreach ($participants as $participant)
                                @if ($isCreator || $isAdmin || $isProjectClient || $participant->id == auth()->id())
                                <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full border dark:border-gray-700 shadow-sm">
                                    <span class="text-[11px] font-bold {{ $participant->id == auth()->id() ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $participant->display_name }}
                                        {{ $participant->id == auth()->id() ? '(أنت)' : '' }}
                                    </span>
                                    @php
                                    $statusAr = [
                                        'pending'  => 'بانتظار الرد',
                                        'accepted' => 'موافق',
                                        'declined' => 'يعتذر',
                                        'attended' => 'حضر الاجتماع',
                                        'absent'   => 'غائب',
                                    ][$participant->pivot->status] ?? 'غير محدد';
                                    @endphp
                                    <span class="text-[9px] px-2 py-0.5 rounded-md font-bold bg-gray-100 dark:bg-gray-700">
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

                        {{-- زر إلغاء الاجتماع للمنشئ --}}
                        @if (($isCreator || $isAdmin) && now() < $meeting->end_at)
                            <form action="{{ route('meetings.destroy', $meeting->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الاجتماع؟ سيتم حذفه نهائياً.')">
                                @csrf @method('DELETE')
                                <button class="w-full bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-700">
                                    <i class="fas fa-trash-alt ml-1"></i> إلغاء الاجتماع
                                </button>
                            </form>
                        @endif

                        {{-- أزرار الرد للمدعو --}}
                        @if (!$isCreator && ($isInvited || $isProjectClient) && now() < $meeting->end_at)
                            @if ($currentUserStatus === 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <input type="hidden" name="request_id" value="{{ $currentRequest?->id }}">
                                        <button class="w-full bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-700">موافقة</button>
                                    </form>
                                    <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="declined">
                                        <button class="w-full bg-red-100 text-black px-4 py-2 rounded-xl text-xs font-bold">اعتذار</button>
                                    </form>
                                </div>
                            @elseif ($currentUserStatus === 'accepted')
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST"
                                    onsubmit="return confirm('هل تريد إلغاء موافقتك على هذا الاجتماع؟')">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="declined">
                                    <button class="w-full bg-orange-100 text-orange-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-200">
                                        <i class="fas fa-times ml-1"></i> إلغاء الموافقة
                                    </button>
                                </form>
                            @elseif ($currentUserStatus === 'declined')
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <input type="hidden" name="request_id" value="{{ $currentRequest?->id }}">
                                    <button class="w-full bg-green-100 text-green-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-200">
                                        <i class="fas fa-check ml-1"></i> الموافقة مجدداً
                                    </button>
                                </form>
                            @endif
                        @elseif (!$isCreator && $currentUserStatus === 'pending' && now() >= $meeting->end_at)
                            <span class="text-[10px] text-gray-400 bg-gray-50 p-2 rounded-lg text-center border border-gray-100">
                                انتهى وقت الرد على هذا الاجتماع
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        @empty
        <div class="text-center py-12 text-gray-400 border-2 border-dashed rounded-3xl dark:border-gray-700">
            لا توجد اجتماعات مسجلة حالياً
        </div>
        @endforelse
    </div>
</div>

{{-- مودال الإضافة --}}
<div id="addMeetingModal"
    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-right"
    dir="rtl">
    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
            <h3 class="text-xl font-bold dark:text-white">جدولة اجتماع جديد</h3>
            <button onclick="toggleModal('addMeetingModal', false)"
                class="text-gray-400 hover:text-black text-2xl">&times;</button>
        </div>
        <form action="{{ route('meetings.store') }}" method="POST" class="space-y-4">
            @csrf
            @if($SpecialRequest)
            <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
            @endif

            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان الاجتماع</label>
                <input type="text" name="title" required
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- نوع الاجتماع --}}
            <div>
                <label class="block text-sm font-bold mb-2 dark:text-gray-300">نوع الاجتماع</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="meeting_type" value="online" checked class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl text-sm font-bold transition-all
                            peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700
                            border-gray-200 text-gray-500 dark:border-gray-600 dark:peer-checked:bg-blue-900/30">
                            <i class="fas fa-video"></i>
                            <span>أونلاين</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="meeting_type" value="in_person" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl text-sm font-bold transition-all
                            peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700
                            border-gray-200 text-gray-500 dark:border-gray-600 dark:peer-checked:bg-orange-900/30">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>حضوري</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 dark:text-gray-300">اختيار الحضور</label>
                <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl max-h-40 overflow-y-auto border dark:border-gray-700">
                    @foreach ($allPossibleAttendees as $person)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white dark:hover:bg-gray-800 p-1 rounded transition">
                        <input type="checkbox" name="attendees[]" value="{{ $person->id }}" class="rounded text-black">
                        <span class="text-xs dark:text-gray-300">{{ $person->display_name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div id="meeting_link_section">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">رابط الاجتماع</label>
                <input type="url" name="meeting_link" id="meeting_link_input"
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت البدء</label>
                    <input type="datetime-local" name="start_at" id="meeting_start_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت الانتهاء</label>
                    <input type="datetime-local" name="end_at" id="meeting_end_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('end_at') border-red-500 @enderror">
                    @error('end_at')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                    <p id="end_at_error" class="text-red-500 text-xs mt-1 font-bold hidden">يجب أن يكون وقت الانتهاء بعد وقت البدء</p>
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit"
                    class="flex-1 bg-black text-white py-3 rounded-xl font-bold hover:bg-red-700">حفظ الاجتماع</button>
                <button type="button" onclick="toggleModal('addMeetingModal', false)"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 dark:text-white py-3 rounded-xl">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    @if($errors->has('end_at') || $errors->has('start_at') || $errors->has('title'))
        document.addEventListener('DOMContentLoaded', function() {
            toggleModal('addMeetingModal', true);
        });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        // تغيير نوع الاجتماع يخفي/يظهر حقل الرابط
        const radios = document.querySelectorAll('input[name="meeting_type"]');
        const linkSection = document.getElementById('meeting_link_section');
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'in_person') {
                    linkSection.style.opacity = '0.5';
                } else {
                    linkSection.style.opacity = '1';
                }
            });
        });

        // client-side validation
        const form = document.getElementById('meeting_end_at')?.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const startAt = document.getElementById('meeting_start_at').value;
                const endAt = document.getElementById('meeting_end_at').value;
                const errorEl = document.getElementById('end_at_error');
                if (startAt && endAt && endAt <= startAt) {
                    e.preventDefault();
                    errorEl.classList.remove('hidden');
                    document.getElementById('meeting_end_at').classList.add('border-red-500');
                } else {
                    errorEl.classList.add('hidden');
                }
            });
        }
    });

    function joinMeeting(meetingId, url) {
        fetch(`/project-meetings/${meetingId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: 'attended' })
        }).then(() => {
            window.open(url, '_blank');
            location.reload();
        });
    }
</script>

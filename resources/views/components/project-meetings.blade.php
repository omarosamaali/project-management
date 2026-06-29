@props(['SpecialRequest'])

@php
$allPossibleAttendees = $SpecialRequest->allProjectClients();
foreach ($SpecialRequest->assignableTeamMembers() as $member) {
    $allPossibleAttendees->push($member);
}
$allPossibleAttendees = $allPossibleAttendees
    ->filter(fn ($u) => ($u->status ?? 'active') !== 'blocked')
    ->unique('id');
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
        @forelse($SpecialRequest->projectMeetings as $meeting)
        @php
        $participants = $meeting->participants;
        $isInvited = $participants->contains('id', auth()->id());
        $isCreator = $meeting->created_by == auth()->id();
        $isAdmin = auth()->user()->role === 'admin';
        $isProjectClient = $SpecialRequest->userCanViewAllProjectIssues(auth()->id(), auth()->user()->role);

        $myParticipant = $participants->firstWhere('id', auth()->id());
        $currentUserStatus = $myParticipant ? $myParticipant->pivot->status : 'pending';

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
                        @if (!$isOnline && $meeting->location)
                        <p class="text-[12px] text-orange-600 dark:text-orange-400 mt-1 font-medium">
                            <i class="fas fa-map-marker-alt ml-1"></i>{{ $meeting->location }}
                        </p>
                        @endif

                        {{-- الحضور --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <p class="w-full text-xs font-bold text-gray-400 mb-1">الحضور الموجهة لهم الدعوة:</p>
                            @foreach ($participants as $participant)
                            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full border dark:border-gray-700 shadow-sm">
                                <span class="text-[11px] font-bold {{ $participant->id == auth()->id() ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $participant->display_name }}
                                </span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500">
                                    {{ ['pending' => 'بانتظار الرد', 'accepted' => 'موافق', 'declined' => 'يعتذر', 'attended' => 'حضر الاجتماع', 'absent' => 'غائب'][$participant->pivot->status] ?? $participant->pivot->status }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- الأكشن --}}
                <div class="flex flex-col gap-2">

                    {{-- أزرار المنشئ / الأدمن --}}
                    @if (($isCreator || $isAdmin) && now() < $meeting->end_at)
                        <button type="button"
                            onclick="openPmEditModal(
                                {{ $meeting->id }},
                                '{{ addslashes($meeting->title) }}',
                                '{{ $meeting->meeting_type ?? 'online' }}',
                                '{{ $meeting->start_at->format('Y-m-d\TH:i') }}',
                                '{{ $meeting->end_at->format('Y-m-d\TH:i') }}',
                                '{{ addslashes($meeting->meeting_link ?? '') }}',
                                '{{ addslashes($meeting->location ?? '') }}',
                                [{{ $participants->pluck('id')->join(',') }}]
                            )"
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-900">
                            <i class="fas fa-edit ml-1"></i> تعديل الاجتماع
                        </button>
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
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button class="bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold">موافقة</button>
                                </form>
                                <form action="{{ route('meetings.updateStatus', $meeting->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="declined">
                                    <button class="bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold">اعتذار</button>
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
                                <button class="w-full bg-green-100 text-green-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-200">
                                    <i class="fas fa-check ml-1"></i> الموافقة مجدداً
                                </button>
                            </form>
                        @endif
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
</div>

{{-- مودال الإضافة --}}
<div id="addMeetingModal"
    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-right"
    dir="rtl">
    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto mx-auto">
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

            {{-- رابط الاجتماع (يظهر للأونلاين) --}}
            <div id="pm_meeting_link_section">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">رابط الاجتماع</label>
                <input type="url" name="meeting_link"
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none">
            </div>

            {{-- مكان الاجتماع (يظهر للحضوري) --}}
            <div id="pm_meeting_location_section" class="hidden">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">مكان الاجتماع</label>
                <input type="text" name="location"
                    placeholder="مثال: مكتب الشركة - الطابق الثاني"
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت البدء</label>
                    <input type="datetime-local" name="start_at" id="pm_meeting_start_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت الانتهاء</label>
                    <input type="datetime-local" name="end_at" id="pm_meeting_end_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p id="pm_end_at_error" class="text-red-500 text-xs mt-1 font-bold hidden">يجب أن يكون وقت الانتهاء بعد وقت البدء</p>
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

{{-- مودال تعديل الاجتماع --}}
<div id="pmEditMeetingModal"
    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-right"
    onclick="if(event.target===this) closePmEditMeetingModal()"
    dir="rtl">
    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto mx-auto" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
            <h3 class="text-xl font-bold dark:text-white">تعديل الاجتماع</h3>
            <button onclick="closePmEditMeetingModal()"
                class="text-gray-400 hover:text-black text-2xl">&times;</button>
        </div>
        <form id="pmEditMeetingForm" action="" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان الاجتماع</label>
                <input type="text" name="title" id="pm_edit_title" required
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-gray-500">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 dark:text-gray-300">نوع الاجتماع</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="meeting_type" id="pm_edit_type_online" value="online" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl text-sm font-bold transition-all
                            peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700
                            border-gray-200 text-gray-500 dark:border-gray-600 dark:peer-checked:bg-blue-900/30">
                            <i class="fas fa-video"></i><span>أونلاين</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="meeting_type" id="pm_edit_type_inperson" value="in_person" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl text-sm font-bold transition-all
                            peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700
                            border-gray-200 text-gray-500 dark:border-gray-600 dark:peer-checked:bg-orange-900/30">
                            <i class="fas fa-map-marker-alt"></i><span>حضوري</span>
                        </div>
                    </label>
                </div>
            </div>

            <div id="pm_edit_link_section">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">رابط الاجتماع</label>
                <input type="url" name="meeting_link" id="pm_edit_meeting_link"
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none">
            </div>
            <div id="pm_edit_location_section" class="hidden">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">مكان الاجتماع</label>
                <input type="text" name="location" id="pm_edit_location"
                    class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت البدء</label>
                    <input type="datetime-local" name="start_at" id="pm_edit_start_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1 dark:text-gray-400">وقت الانتهاء</label>
                    <input type="datetime-local" name="end_at" id="pm_edit_end_at" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 dark:text-gray-300">الحضور</label>
                <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl max-h-36 overflow-y-auto border dark:border-gray-700">
                    @foreach ($allPossibleAttendees as $person)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white dark:hover:bg-gray-800 p-1 rounded transition">
                        <input type="checkbox" name="attendees[]" value="{{ $person->id }}" class="pm-edit-attendee-check rounded text-black">
                        <span class="text-xs dark:text-gray-300">{{ $person->display_name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit"
                    class="flex-1 bg-gray-800 text-white py-3 rounded-xl font-bold hover:bg-black">حفظ التعديلات</button>
                <button type="button" onclick="closePmEditMeetingModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 dark:text-white py-3 rounded-xl">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('#addMeetingModal input[name="meeting_type"]');
        const linkSection = document.getElementById('pm_meeting_link_section');
        const locationSection = document.getElementById('pm_meeting_location_section');

        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'in_person') {
                    linkSection.classList.add('hidden');
                    locationSection.classList.remove('hidden');
                } else {
                    linkSection.classList.remove('hidden');
                    locationSection.classList.add('hidden');
                }
            });
        });

        const form = document.getElementById('pm_meeting_end_at')?.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const startAt = document.getElementById('pm_meeting_start_at').value;
                const endAt = document.getElementById('pm_meeting_end_at').value;
                const errorEl = document.getElementById('pm_end_at_error');
                if (startAt && endAt && endAt <= startAt) {
                    e.preventDefault();
                    errorEl.classList.remove('hidden');
                    document.getElementById('pm_meeting_end_at').classList.add('border-red-500');
                } else {
                    errorEl.classList.add('hidden');
                }
            });
        }
    });

    function openPmEditModal(id, title, type, startAt, endAt, link, location, attendeeIds) {
        document.getElementById('pmEditMeetingForm').action = '/project-meetings/' + id;
        document.getElementById('pm_edit_title').value = title;
        document.getElementById('pm_edit_start_at').value = startAt;
        document.getElementById('pm_edit_end_at').value = endAt;
        document.getElementById('pm_edit_meeting_link').value = link;
        document.getElementById('pm_edit_location').value = location;

        document.getElementById('pm_edit_type_online').checked = (type !== 'in_person');
        document.getElementById('pm_edit_type_inperson').checked = (type === 'in_person');

        var pmLinkSec = document.getElementById('pm_edit_link_section');
        var pmLocSec  = document.getElementById('pm_edit_location_section');
        if (type === 'in_person') {
            pmLinkSec.classList.add('hidden');
            pmLocSec.classList.remove('hidden');
        } else {
            pmLinkSec.classList.remove('hidden');
            pmLocSec.classList.add('hidden');
        }

        document.querySelectorAll('.pm-edit-attendee-check').forEach(function(cb) {
            cb.checked = attendeeIds.includes(parseInt(cb.value));
        });

        var modal = document.getElementById('pmEditMeetingModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePmEditMeetingModal() {
        var modal = document.getElementById('pmEditMeetingModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.querySelectorAll('#pmEditMeetingModal input[name="meeting_type"]').forEach(function(r) {
        r.addEventListener('change', function() {
            const pmLinkSec = document.getElementById('pm_edit_link_section');
            const pmLocSec  = document.getElementById('pm_edit_location_section');
            if (this.value === 'in_person') {
                pmLinkSec.classList.add('hidden');
                pmLocSec.classList.remove('hidden');
            } else {
                pmLinkSec.classList.remove('hidden');
                pmLocSec.classList.add('hidden');
            }
        });
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

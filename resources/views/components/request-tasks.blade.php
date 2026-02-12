@props(['SpecialRequest'])
@php
$allStages = $SpecialRequest->stages;
$totalStagesCount = $allStages->count();

$allTasks = $SpecialRequest->tasks;
$totalTasksCount = $allTasks->count();
$completedTasksCount = $allTasks->where('status', 'منتهية')->count();

$projectCompletion = $totalTasksCount > 0 ? round(($completedTasksCount / $totalTasksCount) * 100, 1) : 0;

$stats = [
'total' => $totalStagesCount,
'completed' => $allStages->where('status', 'completed')->count(),
'in_progress' => $allTasks->where('status', 'قيد الإنجاز')->count(),
'hours' => $allStages->sum('hours_count'),
];

// ✅ unique ID عشان مش يتعارض مع modals ثانية في نفس الـ page
$uid = 'tasks_' . $SpecialRequest->id . '_' . (get_class($SpecialRequest) === 'App\Models\SpecialRequest' ? 'sr' :
'rq');
@endphp
<div class="p-6 space-y-6">
    {{-- كروت إحصائيات المهام --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800">
            <div class="text-green-600 dark:text-green-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-check-circle"></i> المهام المنتهية
            </div>
            <div class="text-2xl font-black text-green-700 dark:text-green-300">
                {{ $SpecialRequest->tasks->where('status', 'منتهية')->count() }}
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800">
            <div class="text-black dark:text-red-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> المهام المتأخرة
            </div>
            <div class="text-2xl font-black text-red-700 dark:text-red-300">
                {{ $SpecialRequest->tasks->where('status', 'متأخرة')->count() }}
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-clock"></i> بانتظار البدء
            </div>
            <div class="text-2xl font-black text-gray-700 dark:text-gray-300">
                {{ $SpecialRequest->tasks->where('status', 'بالانتظار')->count() }}
            </div>
        </div>
    </div>

    {{-- نسبة الإنجاز --}}
    @php
    $totalTasks = $SpecialRequest->tasks->count();
    $completedTasks = $SpecialRequest->tasks->where('status', 'منتهية')->count();
    $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-bold dark:text-white">تقدم العمل في المهام</span>
            <span class="text-sm font-bold text-blue-600">{{ $percent }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%">
            </div>
        </div>
    </div>

    {{-- زر الإضافة --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']))
    <div class="flex justify-end">
        <button onclick="document.getElementById('{{ $uid }}_addModal').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-all shadow-md">
            <i class="fas fa-plus-circle"></i> إضافة مهمة جديدة
        </button>
    </div>
    @endif

    {{-- جدول المهام --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="p-4 text-xs font-bold uppercase">المهمة</th>
                        <th class="p-4 text-xs font-bold uppercase text-center">المرحلة</th>
                        <th class="p-4 text-xs font-bold uppercase text-center">المسؤول</th>
                        <th class="p-4 text-xs font-bold uppercase text-center">التاريخ</th>
                        <th class="p-4 text-xs font-bold uppercase text-center">الحالة</th>
                        <th class="p-4 text-xs font-bold uppercase text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($SpecialRequest->tasks as $task)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="p-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($task->details, 40) }}</div>
                        </td>
                        <td class="p-4 text-center">
                            <span
                                class="text-[10px] px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded text-gray-600 dark:text-gray-300">
                                {{ $task->requestStage->title ?? ($task->stage->title ?? 'مهمة عامة') }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-sm font-medium dark:text-gray-200">{{ $task->user->name }}</td>
                        <td class="p-4 text-center">
                            <div class="text-[10px] text-gray-500">{{ $task->start_date }}</div>
                            <div class="text-[10px] text-gray-400 font-bold">إلى {{ $task->end_date }}</div>
                        </td>
                        <td class="p-4 text-center">
                            @php
                            $statusClasses = [
                            'منتهية' => 'bg-green-100 text-green-700',
                            'قيد الإنجاز' => 'bg-blue-100 text-blue-700',
                            'متأخرة' => 'bg-red-100 text-red-700',
                            'بالانتظار' => 'bg-gray-100 text-gray-600',
                            ];
                            @endphp
                            <span
                                class="px-3 py-1 rounded-full text-[10px] font-bold {{ $statusClasses[$task->status] ?? 'bg-gray-100' }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- زر عرض التفاصيل --}}
                                <button onclick="taskModal_{{ $uid }}_show({{ $task->id }})"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if(Auth::user()->role == 'admin')
                                <button onclick="taskModal_{{ $uid }}_open({{ $task->id }})"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('حذف المهمة؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-black hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-400 italic">لا توجد مهام حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- مودال الإضافة --}}
<div id="{{ $uid }}_addModal"
    class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-blue-50 dark:bg-blue-900/20">
            <h3 class="text-lg font-bold dark:text-white">إضافة مهمة جديدة</h3>
            <button onclick="document.getElementById('{{ $uid }}_addModal').classList.add('hidden')"
                class="text-2xl hover:text-black">&times;</button>
        </div>

        <form action="{{ route('tasks.request-store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            @if(get_class($SpecialRequest) === 'App\Models\SpecialRequest')
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
            @else
            <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
            @endif
            <input type="hidden" name="status" value="بالانتظار">

            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">المرحلة</label>
                @php
                $isSpecial = get_class($SpecialRequest) === 'App\Models\SpecialRequest';
                $fieldName = $isSpecial ? 'project_stage_id' : 'request_stage_id';
                @endphp
                <select name="{{ $fieldName }}"
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">اختر المرحلة (اختياري)</option>
                    @forelse($SpecialRequest->stages as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                    @empty
                    <option value="" disabled>لا توجد مراحل متاحة</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">إسناد إلى</label>
                <select name="user_id" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">اختر المسؤول...</option>
                    @foreach ($SpecialRequest->partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="text" name="title" placeholder="عنوان المهمة" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2">
                <textarea name="details" rows="2" placeholder="تفاصيل إضافية..."
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ البداية</label>
                    <input type="date" name="start_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ التسليم</label>
                    <input type="date" name="end_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold">حفظ
                    المهمة</button>
                <button type="button" onclick="document.getElementById('{{ $uid }}_addModal').classList.add('hidden')"
                    class="px-8 py-3 rounded-xl bg-gray-100 text-gray-500">إلغاء</button>
            </div>
        </form>
    </div>
</div>
{{-- مودال عرض التفاصيل --}}
<div id="{{ $uid }}_showModal"
    class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="mx-auto bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-green-50 dark:bg-green-900/20">
            <h3 class="text-lg font-bold dark:text-white flex items-center gap-2">
                <i class="fas fa-eye text-green-600"></i> تفاصيل المهمة
            </h3>
            <button type="button" onclick="taskModal_{{ $uid }}_closeShow()"
                class="text-2xl hover:text-black text-gray-400">&times;</button>
        </div>

        <div class="p-6 space-y-4">
            {{-- العنوان --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان المهمة</label>
                <div id="{{ $uid }}_show_title" class="text-lg font-bold dark:text-white"></div>
            </div>

            {{-- المرحلة والمسؤول --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">المرحلة</label>
                    <div id="{{ $uid }}_show_stage" class="font-medium dark:text-white"></div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">المسؤول عن
                        المهمة</label>
                    <div id="{{ $uid }}_show_user" class="font-medium dark:text-white"></div>
                </div>
            </div>

            {{-- التواريخ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تاريخ البداية</label>
                    <div id="{{ $uid }}_show_start" class="font-medium dark:text-white"></div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تاريخ التسليم</label>
                    <div id="{{ $uid }}_show_end" class="font-medium dark:text-white"></div>
                </div>
            </div>

            {{-- الحالة --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">حالة المهمة</label>
                <span id="{{ $uid }}_show_status" class="inline-block px-4 py-2 rounded-full text-sm font-bold"></span>
            </div>

            {{-- التفاصيل --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">التفاصيل</label>
                <div id="{{ $uid }}_show_details" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"></div>
            </div>

            {{-- زر الإغلاق --}}
            <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                <button type="button" onclick="taskModal_{{ $uid }}_closeShow()"
                    class="px-8 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition-colors font-bold">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // الكود القديم هنا...

    // ✅ Function لعرض التفاصيل
    function taskModal_{{ $uid }}_show(taskId) {
        fetch('/tasks/' + taskId + '/edit', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('فشل الطلب: ' + response.status);
            return response.json();
        })
        .then(function(task) {
            // إظهار المودال
            var modal = document.getElementById('{{ $uid }}_showModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            // ملء البيانات
            document.getElementById('{{ $uid }}_show_title').textContent = task.title || 'غير محدد';
            document.getElementById('{{ $uid }}_show_details').textContent = task.details || 'لا توجد تفاصيل';
            document.getElementById('{{ $uid }}_show_user').textContent = task.user_name || 'غير محدد';
            document.getElementById('{{ $uid }}_show_stage').textContent = task.stage_title || 'مهمة عامة';
            document.getElementById('{{ $uid }}_show_start').textContent = task.start_date || 'غير محدد';
            document.getElementById('{{ $uid }}_show_end').textContent = task.end_date || 'غير محدد';

            // تلوين الحالة
            var statusElement = document.getElementById('{{ $uid }}_show_status');
            statusElement.textContent = task.status || 'غير محدد';
            
            var statusClasses = {
                'منتهية': 'bg-green-100 text-green-700',
                'قيد الإنجاز': 'bg-blue-100 text-blue-700',
                'متأخرة': 'bg-red-100 text-red-700',
                'بالانتظار': 'bg-gray-100 text-gray-600'
            };
            
            statusElement.className = 'inline-block px-4 py-2 rounded-full text-sm font-bold ' + 
                (statusClasses[task.status] || 'bg-gray-100 text-gray-600');
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('خطأ في جلب بيانات المهمة: ' + error.message);
        });
    }

    function taskModal_{{ $uid }}_closeShow() {
        var modal = document.getElementById('{{ $uid }}_showModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
</script>
{{-- مودال التعديل --}}
<div id="{{ $uid }}_editModal"
    class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="mx-auto bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-amber-50 dark:bg-amber-900/20">
            <h3 class="text-lg font-bold dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> تعديل بيانات المهمة
            </h3>
            <button type="button" onclick="taskModal_{{ $uid }}_close()"
                class="text-2xl hover:text-black text-gray-400">&times;</button>
        </div>

        <form id="{{ $uid }}_editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">

            {{-- المرحلة والمسؤول --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">المرحلة</label>
                    <select id="{{ $uid }}_edit_stage" name="project_stage_id"
                        class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">مهمة عامة</option>
                        @foreach ($SpecialRequest->stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">المسؤول</label>
                    <select id="{{ $uid }}_edit_user" name="user_id" required
                        class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        @foreach ($SpecialRequest->partners as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- العنوان والتفاصيل --}}
            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان المهمة</label>
                <input id="{{ $uid }}_edit_title" type="text" name="title" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2">
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">التفاصيل</label>
                <textarea id="{{ $uid }}_edit_details" name="details" rows="2"
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            {{-- التواريخ --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ البداية</label>
                    <input id="{{ $uid }}_edit_start" type="date" name="start_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ التسليم</label>
                    <input id="{{ $uid }}_edit_end" type="date" name="end_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            {{-- الحالة --}}
            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">الحالة الحالية</label>
                <select id="{{ $uid }}_edit_status" name="status"
                    class="w-full p-3 rounded-xl border border-amber-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-bold">
                    <option value="بالانتظار">بالانتظار</option>
                    <option value="قيد الإنجاز">قيد الإنجاز</option>
                    <option value="منتهية">منتهية</option>
                    <option value="متأخرة">متأخرة</option>
                </select>
            </div>

            {{-- الأزرار --}}
            <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                <button type="submit"
                    class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-xl font-bold transition-colors">
                    تحديث البيانات
                </button>
                <button type="button" onclick="taskModal_{{ $uid }}_close()"
                    class="px-8 py-3 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ✅ كل الـ functions بـ unique name بناءً على الـ $uid عشان مش تتعارض
function taskModal_{{ $uid }}_open(taskId) {
    fetch('/tasks/' + taskId + '/edit', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) throw new Error('فشل الطلب: ' + response.status);
        return response.json();
    })
    .then(function(task) {
        // خطوة 1: إظهار المودال أولاً
        var modal = document.getElementById('{{ $uid }}_editModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        // خطوة 2: نملأ الحقول بعد ما المودال يظهر
        setTimeout(function() {
            document.getElementById('{{ $uid }}_edit_title').value   = task.title || '';
            document.getElementById('{{ $uid }}_edit_details').value = task.details || '';
            document.getElementById('{{ $uid }}_edit_user').value    = task.user_id || '';
            document.getElementById('{{ $uid }}_edit_stage').value   = task.project_stage_id || '';
            document.getElementById('{{ $uid }}_edit_start').value   = task.start_date || '';
            document.getElementById('{{ $uid }}_edit_end').value     = task.end_date || '';
            document.getElementById('{{ $uid }}_edit_status').value  = task.status || 'بالانتظار';
            document.getElementById('{{ $uid }}_editForm').action    = '/tasks/' + taskId;
        }, 50);
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('خطأ في جلب بيانات المهمة: ' + error.message);
    });
}

function taskModal_{{ $uid }}_close() {
    var modal = document.getElementById('{{ $uid }}_editModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}
</script>
@props(['SpecialRequest'])
@php
$allStages = $SpecialRequest->stages;
$totalStagesCount = $allStages->count();

// 1. حساب النسبة الكلية للمشروع بناءً على إجمالي المهام في كل المراحل
$allTasks = $SpecialRequest->tasks; // افترضنا وجود علاقة tasks في موديل SpecialRequest
$totalTasksCount = $allTasks->count();
$completedTasksCount = $allTasks->where('status', 'منتهية')->count();

$projectCompletion = $totalTasksCount > 0
? round(($completedTasksCount / $totalTasksCount) * 100, 1)
: 0;

// إحصائيات سريعة
$stats = [
'total' => $totalStagesCount,
'completed' => $allStages->where('status', 'completed')->count(),
'in_progress' => $allTasks->where('status', 'قيد الإنجاز')->count(),
'hours' => $allStages->sum('hours_count'),
];
@endphp
<div class="p-6 space-y-6">
    {{-- 1. كروت إحصائيات المهام الجديدة --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- بوكس المنتهية --}}
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800">
            <div class="text-green-600 dark:text-green-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-check-circle"></i> المهام المنتهية
            </div>
            <div class="text-2xl font-black text-green-700 dark:text-green-300">
                {{ $SpecialRequest->tasks->where('status', 'منتهية')->count() }}
            </div>
        </div>

        {{-- بوكس المتأخرة --}}
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800">
            <div class="text-black dark:text-red-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> المهام المتأخرة
            </div>
            <div class="text-2xl font-black text-red-700 dark:text-red-300">
                {{ $SpecialRequest->tasks->where('status', 'متأخرة')->count() }}
            </div>
        </div>

        {{-- بوكس بالانتظار --}}
        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-clock"></i> بانتظار البدء
            </div>
            <div class="text-2xl font-black text-gray-700 dark:text-gray-300">
                {{ $SpecialRequest->tasks->where('status', 'بالانتظار')->count() }}
            </div>
        </div>
    </div>

    {{-- 2. مراجعة نسبة الإنجاز العامة للمهام --}}
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
    @if (in_array(auth()->user()->role, ['admin', 'partner']))
    <div class="flex justify-end">
        <button onclick="document.getElementById('addTaskModal').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-all shadow-md">
            <i class="fas fa-plus-circle"></i> إضافة مهمة جديدة
        </button>
    </div>
    @endif

    {{-- عرض كافة مهام المشروع --}}
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
                                {{ $task->stage->title ?? 'مهمة عامة' }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-sm font-medium dark:text-gray-200">{{ $task->user->name }}</td>
                        <td class="p-4 text-center">
                            <div class="text-[10px] text-gray-500">{{ $task->start_date }}</div>
                            <div class="text-[10px] text-gray-400 font-bold">إلى {{ $task->end_date ?? '-' }}</div>
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
                                <button onclick="openShowModal({{ $task->id }})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg"
                                    title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @php
                                // التحقق مما إذا كان المستخدم الحالي مديرًا لهذا المشروع
                                $isProjectManager = \App\Models\Project_Manager::where('user_id', auth()->id())
                                ->where('special_request_id', $SpecialRequest->id)
                                ->exists();
                                @endphp
                                @if(Auth::user()->role != 'client')
                                @if (in_array(auth()->user()->role, ['admin' || $isProjectManager]))
                                <button onclick="openEditModal({{ $task->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <i class="fas fa-edit"></i>
                                </button>
                        
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('حذف المهمة؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-black hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
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
{{-- مودال عرض التفاصيل --}}
<div id="showTaskModal"
    class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="mx-auto bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-green-50 dark:bg-green-900/20">
            <h3 class="text-lg font-bold dark:text-white flex items-center gap-2">
                <i class="fas fa-eye text-green-600"></i> تفاصيل المهمة
            </h3>
            <button type="button" onclick="closeShowModal()"
                class="text-2xl hover:text-black text-gray-400">&times;</button>
        </div>

        <div class="p-6 space-y-4">
            {{-- العنوان --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان المهمة</label>
                <div id="show_title" class="text-lg font-bold dark:text-white"></div>
            </div>

            {{-- المرحلة والمسؤول --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">المرحلة</label>
                    <div id="show_stage" class="font-medium dark:text-white"></div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">المسؤول عن
                        المهمة</label>
                    <div id="show_user" class="font-medium dark:text-white"></div>
                </div>
            </div>

            {{-- التواريخ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تاريخ البداية</label>
                    <div id="show_start_date" class="font-medium dark:text-white"></div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تاريخ التسليم</label>
                    <div id="show_end_date" class="font-medium dark:text-white">
                        {{ $task->end_date ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- الحالة --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">حالة المهمة</label>
                <span id="show_status" class="inline-block px-4 py-2 rounded-full text-sm font-bold"></span>
            </div>

            {{-- التفاصيل --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">التفاصيل</label>
                <div id="show_details" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $task->details ?? '-' }}</div>
            </div>

            {{-- زر الإغلاق --}}
            <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                <button type="button" onclick="closeShowModal()"
                    class="px-8 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition-colors font-bold">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
{{-- مودال الإضافة (تم إزالة خيار الحالة ليكون تلقائياً) --}}
<div id="addTaskModal"
    class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-blue-50 dark:bg-blue-900/20">
            <h3 class="text-lg font-bold dark:text-white">إضافة مهمة جديدة</h3>
            <button onclick="document.getElementById('addTaskModal').classList.add('hidden')"
                class="text-2xl hover:text-black">&times;</button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
            {{-- الحالة مخفية وتلقائية --}}
            <input type="hidden" name="status" value="بالانتظار">

            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">المرحلة</label>
                <select name="project_stage_id"
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">مهمة عامة للمشروع</option>
                    @foreach ($SpecialRequest->stages as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                    @endforeach
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
                <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')"
                    class="px-8 py-3 rounded-xl bg-gray-100 text-gray-500">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال التعديل (يسمح بتغيير الحالة هنا فقط) --}}
{{-- مودال التعديل الشامل --}}
<div id="editTaskModal"
    class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="mx-auto bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
        {{-- الرأس --}}
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-amber-50 dark:bg-amber-900/20">
            <h3 class="text-lg font-bold dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> تعديل بيانات المهمة
            </h3>
            <button type="button" onclick="closeEditModal()"
                class="text-2xl hover:text-black text-gray-400">&times;</button>
        </div>

        <form id="editTaskForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">

            {{-- 1. المرحلة والمسؤول --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">المرحلة</label>
                    <select id="edit_project_stage_id" name="project_stage_id"
                        class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">مهمة عامة</option>
                        @foreach ($SpecialRequest->stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">المسؤول</label>
                    <select id="edit_user_id" name="user_id" required
                        class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        @foreach ($SpecialRequest->partners as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. العنوان والتفاصيل --}}
            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان المهمة</label>
                <input id="edit_title" type="text" name="title" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2">

                <label class="block text-sm font-bold mb-1 dark:text-gray-300">التفاصيل</label>
                <textarea id="edit_details" name="details" rows="2"
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            {{-- 3. التواريخ --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ البداية</label>
                    <input id="edit_start_date" type="date" name="start_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold dark:text-gray-400">تاريخ التسليم</label>
                    <input id="edit_end_date" type="date" name="end_date" required
                        class="w-full p-2.5 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            {{-- 4. الحالة --}}
            <div>
                <label class="block text-sm font-bold mb-1 dark:text-gray-300">الحالة الحالية</label>
                <select id="edit_status" name="status"
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
                <button type="button" onclick="closeEditModal()"
                    class="px-8 py-3 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
{{-- استبدل الـ <script>
    كامله في آخر الـ file بهذا --}}
<script>
    // ✅ فتح modal التعديل
    function openEditModal(taskId) {
        fetch(`/tasks/${taskId}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('فشل الطلب: ' + response.status);
            return response.json();
        })
        .then(task => {
            console.log('Task data received:', task);

            const modal = document.getElementById('editTaskModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            requestAnimationFrame(() => {
                const titleInput = modal.querySelector('#edit_title');
                const detailsInput = modal.querySelector('#edit_details');
                if (titleInput) titleInput.value = task.title || '';
                if (detailsInput) detailsInput.value = task.details || '';

                const userSelect = modal.querySelector('#edit_user_id');
                const stageSelect = modal.querySelector('#edit_project_stage_id');
                if (userSelect) userSelect.value = task.user_id || '';
                if (stageSelect) stageSelect.value = task.project_stage_id || '';

                const startDate = modal.querySelector('#edit_start_date');
                const endDate = modal.querySelector('#edit_end_date');
                if (startDate) startDate.value = task.start_date || '';
                if (endDate) endDate.value = task.end_date || '';

                const statusSelect = modal.querySelector('#edit_status');
                if (statusSelect) statusSelect.value = task.status || 'بالانتظار';

                const form = modal.querySelector('#editTaskForm');
                if (form) form.action = `/tasks/${taskId}`;
            });
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('خطأ في جلب بيانات المهمة: ' + error.message);
        });
    }

    function closeEditModal() {
        const modal = document.getElementById('editTaskModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    // ✅ فتح modal العرض
function openShowModal(taskId) {
fetch(`/tasks/${taskId}/edit`, {
headers: {
'Accept': 'application/json',
'X-Requested-With': 'XMLHttpRequest'
}
})
.then(response => response.json())
.then(task => {
const modal = document.getElementById('showTaskModal');
modal.classList.remove('hidden');
modal.style.display = 'flex';

// 1. تعبئة النصوص الأساسية
document.getElementById('show_title').innerText = task.title || 'بدون عنوان';
document.getElementById('show_details').innerText = task.details || 'لا توجد تفاصيل مضافة.';

// 2. إصلاح التواريخ (التأكد من مطابقة الأسماء مع الكنترولر)
document.getElementById('show_start_date').innerText = task.start_date || 'غير محدد';
document.getElementById('show_end_date').innerText = task.end_date || 'غير محدد';

// 3. عرض الأسماء (المسؤول والمرحلة)
document.getElementById('show_user').innerText = task.user_name || 'غير محدد';
document.getElementById('show_stage').innerText = task.stage_title || 'مهمة عامة';

// 4. تحديث حالة المهمة ولونها
const statusElement = document.getElementById('show_status');
statusElement.innerText = task.status;

const statusClasses = {
'منتهية': 'bg-green-100 text-green-700',
'قيد الإنجاز': 'bg-blue-100 text-blue-700',
'متأخرة': 'bg-red-100 text-red-700',
'بالانتظار': 'bg-gray-100 text-gray-600'
};

statusElement.className = 'inline-block px-4 py-2 rounded-full text-sm font-bold ' +
(statusClasses[task.status] || 'bg-gray-100 text-gray-600');
})
.catch(error => {
console.error('Error:', error);
alert('حدث خطأ أثناء تحميل البيانات');
});
}    function closeShowModal() {
        const modal = document.getElementById('showTaskModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
</script>
<script>
    function openEditModal(taskId) {
        fetch(`/tasks/${taskId}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('فشل الطلب: ' + response.status);
            return response.json();
        })
        .then(task => {
            console.log('Task data received:', task);

            // ✅ أولاً: إظهار المودال
            const modal = document.getElementById('editTaskModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            // ✅ ثانياً: بعد ما المودال يظهر، املأ الحقول
            requestAnimationFrame(() => {
                // العنوان والتفاصيل
                const titleInput = modal.querySelector('#edit_title');
                const detailsInput = modal.querySelector('#edit_details');
                if (titleInput) titleInput.value = task.title || '';
                if (detailsInput) detailsInput.value = task.details || '';

                // المسؤول والمرحلة
                const userSelect = modal.querySelector('#edit_user_id');
                const stageSelect = modal.querySelector('#edit_project_stage_id');
                if (userSelect) userSelect.value = task.user_id || '';
                if (stageSelect) stageSelect.value = task.project_stage_id || '';

                // التواريخ
                const startDate = modal.querySelector('#edit_start_date');
                const endDate = modal.querySelector('#edit_end_date');
                if (startDate) startDate.value = task.start_date || '';
                if (endDate) endDate.value = task.end_date || '';

                // الحالة
                const statusSelect = modal.querySelector('#edit_status');
                if (statusSelect) statusSelect.value = task.status || 'بالانتظار';

                // تحديث الـ action
                const form = modal.querySelector('#editTaskForm');
                if (form) form.action = `/tasks/${taskId}`;

                // تأكيد إن كل شيء اتملأ
                console.log('Form filled:', {
                    title: titleInput?.value,
                    details: detailsInput?.value,
                    user_id: userSelect?.value,
                    stage_id: stageSelect?.value,
                    start_date: startDate?.value,
                    end_date: endDate?.value,
                    status: statusSelect?.value
                });
            });
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('خطأ في جلب بيانات المهمة: ' + error.message);
        });
    }

    function closeEditModal() {
        const modal = document.getElementById('editTaskModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
</script>
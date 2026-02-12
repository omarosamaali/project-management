@props(['SpecialRequest'])

<div class="p-6 space-y-6">
    {{-- كروت إحصائيات المراحل --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
        $allStages = $SpecialRequest->stages;
        $totalStages = $allStages->count();

        // حساب نسبة الإنجاز الإجمالية بناءً على إجمالي المهام المنتهية في كل المشروع
        $allTasks = $SpecialRequest->tasks; // تأكد من وجود علاقة tasks في موديل SpecialRequest
        $totalProjectTasks = $allTasks->count();
        $completedProjectTasks = $allTasks->where('status', 'منتهية')->count();
        $projectCompletion = $totalProjectTasks > 0 ? round(($completedProjectTasks / $totalProjectTasks) * 100, 1) : 0;

        $completedStagesCount = $allStages->where('status', 'completed')->count();
        $waitingStagesCount = $allStages->where('status', 'waiting')->count();
        $totalHours = $allStages->sum('hours_count');
        @endphp

        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800">
            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">إجمالي المراحل</div>
            <div class="text-2xl font-bold text-blue-900 dark:text-white">{{ $totalStages }}</div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">مراحل منتهية</div>
            <div class="text-2xl font-bold text-green-900 dark:text-white">{{ $completedStagesCount }}</div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">بانتظار البدء</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $waitingStagesCount }}</div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800">
            <div class="text-purple-600 dark:text-purple-400 text-sm font-medium">الوصف</div>
            <div class="text-2xl font-bold text-purple-900 dark:text-white">{{ $totalHours }} <span
                    class="text-sm">ساعة</span></div>
        </div>
    </div>

    {{-- رأس القسم مع شريط تقدم المشروع --}}
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex-1 w-full">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i> تقدم إنجاز المهام الكلي
                </h2>
                <div class="mt-3 flex items-center gap-4">
                    <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-blue-600 h-full transition-all duration-700 ease-out"
                            style="width: {{ $projectCompletion }}%"></div>
                    </div>
                    <span class="text-lg font-black text-blue-600">{{ $projectCompletion }}%</span>
                </div>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
            <button onclick="toggleModal('addStageModal', true)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all shadow-md shrink-0">
                <i class="fas fa-plus-circle"></i> إضافة مرحلة جديدة
            </button>
            @endif
        </div>
    </div>

    {{-- جدول المراحل --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-right border-collapse">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="p-4 text-sm font-bold">المرحلة</th>
                    <th class="p-4 text-sm font-bold text-center">المهام (منجزة/كلية)</th>
                    <th class="p-4 text-sm font-bold">نسبة إنجاز المرحلة</th>
                    <th class="p-4 text-sm font-bold">الحالة</th>
                    <th class="p-4 text-sm font-bold text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($SpecialRequest->stages as $stage)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-gray-900 dark:text-white">{{ $stage->title }}</div>
                        <div class="text-xs text-gray-400">تنتهي في: {{ $stage->end_date ?? 'لم يحدد' }}</div>
                    </td>

                    <td class="p-4 text-center">
                        @php
                        $stageTasks = $stage->tasks; // علاقة المهام داخل المرحلة
                        $totalTasks = $stageTasks->count();
                        $doneTasks = $stageTasks->where('status', 'منتهية')->count();

                        // حساب نسبة المرحلة بناءً على المهام
                        $stagePercent = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs font-bold">
                            {{ $doneTasks }} / {{ $totalTasks }}
                        </span>
                    </td>

                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 w-24 overflow-hidden">
                                <div class="bg-green-500 h-full transition-all duration-500"
                                    style="width: {{ $stagePercent }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold">{{ $stagePercent }}%</span>
                        </div>
                    </td>

                    <td class="p-4">
                        @php
                        $statusMap = [
                        'waiting' => ['label' => 'بالانتظار', 'css' => 'bg-gray-100 text-gray-600'],
                        'in_progress' => ['label' => 'قيد الإنجاز', 'css' => 'bg-amber-100 text-amber-600'],
                        'completed' => ['label' => 'منتهية', 'css' => 'bg-green-100 text-green-600'],
                        'delayed' => ['label' => 'متأخرة', 'css' => 'bg-red-100 text-black'],
                        ];
                        $curr = $statusMap[$stage->status] ?? $statusMap['waiting'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $curr['css'] }}">
                            {{ $curr['label'] }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                            @php
                            // التحقق مما إذا كان المستخدم الحالي مديرًا لهذا المشروع
                            $isProjectManager = \App\Models\Project_Manager::where('user_id', auth()->id())
                            ->where('special_request_id', $SpecialRequest->id)
                            ->exists();
                            @endphp
                            @if(Auth::user()->role != 'client')
                        @if (in_array(auth()->user()->role, ['admin' || $isProjectManager]))
                        <div class="flex justify-center gap-1">
                            <button onclick='openEditStageModal(@json($stage))'
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endif
                            @endif
                            <button onclick='openShowStageModal(@json($stage))'
                                class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </button>
                            {{-- Delete --}}

@if (auth()->user()->role === 'admin' || $isProjectManager)
<form action="{{ route('dashboard.special-request.destroy-stage', $stage->id) }}" method="POST" class="inline">
    @csrf @method('DELETE')
    <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذه المرحلة؟')"
        class="p-1.5 text-black hover:bg-red-50 rounded-md transition-colors">
        <i class="fas fa-trash-alt"></i>
    </button>
</form>
@endif                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400 italic">لا توجد مراحل عمل مضافة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div id="showStageModal" class="flex fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-700">
        <div
            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/50">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-500"></i> تفاصيل المرحلة
            </h3>
            <button onclick="toggleModal('showStageModal', false)"
                class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">عنوان المرحلة</label>
                <p id="show_title" class="text-lg font-bold text-gray-900 dark:text-white mt-1"></p>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">تفاصيل العمل</label>
                <div id="show_details"
                    class="mt-1 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-gray-700 dark:text-gray-300 text-sm leading-relaxed min-h-[80px]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400">الحالة</label>
                    <div id="show_status_badge" class="mt-1">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400">تاريخ الانتهاء</label>
                    <p id="show_end_date" class="mt-1 font-semibold text-gray-900 dark:text-white"></p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 flex justify-end">
            <button type="button" onclick="toggleModal('showStageModal', false)"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-bold hover:bg-gray-300 transition-all">
                إغلاق
            </button>
        </div>
    </div>
</div>
<div id="addStageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">إضافة مرحلة عمل جديدة</h3>
            <button onclick="toggleModal('addStageModal', false)" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- بداية النموذج --}}
        <form action="{{ route('dashboard.special-request.store1-stage', $SpecialRequest->id) }}" method="POST"
            class="p-6 space-y-4">
            @csrf
            {{-- حقل العنوان --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عنوان المرحلة</label>
                <input type="text" name="title" required
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500">
            </div>

            {{-- حقل التفاصيل (Details) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تفاصيل المرحلة</label>
                <textarea name="details" rows="3"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500"
                    placeholder="اشرح ما سيتم تنفيذه في هذه المرحلة..."></textarea>
            </div>

            {{-- تاريخ الانتهاء --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ الانتهاء
                    المتوقع</label>
                <input type="date" name="end_date"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500">
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-bold transition-all shadow-lg">حفظ
                    المرحلة</button>
                <button type="button" onclick="toggleModal('addStageModal', false)"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<div id="editStageModal" class="flex fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">تعديل المرحلة</h3>
            <button onclick="toggleModal('editStageModal', false)" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editStageForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عنوان المرحلة</label>
                <input type="text" id="edit_title" name="title" required
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">التفاصيل</label>
                <textarea id="edit_details" name="details" rows="3"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ
                        الانتهاء</label>
                    <input type="date" id="edit_end_date" name="end_date"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                    <select id="edit_status" name="status"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="waiting">بالانتظار</option>
                        <option value="in_progress">قيد الإنجاز</option>
                        <option value="completed">منتهية</option>
                        <option value="delayed">متأخرة</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-bold transition-all">تحديث
                    البيانات</button>
                <button type="button" onclick="toggleModal('editStageModal', false)"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleModal(modalId, show) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            } else {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        }
    }
function openShowStageModal(stage) {
// تعبئة النصوص البسيطة
document.getElementById('show_title').innerText = stage.title || 'بدون عنوان';
document.getElementById('show_details').innerText = stage.details || 'لا توجد تفاصيل مضافة لهذه المرحلة.';
document.getElementById('show_end_date').innerText = stage.end_date || 'غير محدد';

// تعبئة الشارة (Badge) بناءً على الحالة
const badgeContainer = document.getElementById('show_status_badge');
const statusMap = {
'waiting': { label: 'بالانتظار', css: 'bg-gray-100 text-gray-600' },
'in_progress': { label: 'قيد الإنجاز', css: 'bg-amber-100 text-amber-600' },
'completed': { label: 'منتهية', css: 'bg-green-100 text-green-600' },
'delayed': { label: 'متأخرة', css: 'bg-red-100 text-red-600' }
};

const status = statusMap[stage.status] || statusMap['waiting'];
badgeContainer.innerHTML = `<span
    class="px-3 py-1 rounded-full text-[12px] font-bold ${status.css}">${status.label}</span>`;

toggleModal('showStageModal', true);
}
function openEditStageModal(stage) {
const form = document.getElementById('editStageForm');

// تأكد من أن المسار هنا يطابق المسار الفعلي في ملف web.php
form.action = `/dashboard/stages/${stage.id}`;

document.getElementById('edit_title').value = stage.title || '';

// تم حذف سطر edit_hours_count لأنه غير موجود في الـ HTML الخاص بك
// إذا كنت تريد تعديل التفاصيل، أضف السطر التالي:
if(document.getElementById('edit_details')) {
document.getElementById('edit_details').value = stage.details || '';
}

document.getElementById('edit_end_date').value = stage.end_date || '';
document.getElementById('edit_status').value = stage.status || 'waiting';

toggleModal('editStageModal', true);

}
</script>
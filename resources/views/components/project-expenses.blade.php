@props(['SpecialRequest'])

<div class="p-6 space-y-6">
    {{-- رأس القسم --}}
    <div class="flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-green-600"></i> مصاريف المشروع
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                <i class="fas fa-calculator ml-1"></i>
                إجمالي المصاريف: <span class="ml-1">{{ number_format($SpecialRequest->expenses->sum('price'), 2) }}</span>
                <x-drhm-icon color="333" width="13" height="13" />
            </p>
        </div>
        {{-- زر إضافة مصروف --}}
        <button onclick="openAddExpenseModal()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-plus-circle"></i> إضافة مصروف
        </button>
    </div>

    {{-- قائمة المصاريف --}}
    <div class="space-y-4">
        @forelse($SpecialRequest->expenses as $expense)
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
            <div
                class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700 border-b dark:border-gray-600">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-receipt text-green-600"></i>
                            {{ $expense->title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1 font-bold text-green-700 dark:text-green-400">
                                <i class="fas fa-money-bill-wave"></i>
                                {{ number_format($expense->price, 2) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="far fa-calendar-alt"></i>
                                {{ $expense->date }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-circle"></i>
                                {{ $expense->user->name ?? 'غير محدد' }}
                            </span>
                        </div>
                    </div>

                    @if($expense->image)
                    <a href="{{ asset('storage/' . $expense->image) }}" target="_blank"
                        class="flex flex-col items-center gap-1 text-xs text-blue-600 hover:underline">
                        <i class="fas fa-image text-2xl"></i>
                        عرض المرفق
                    </a>
                    @endif
                </div>
            </div>

            <div class="p-4">
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $expense->description }}</p>
            </div>

            {{-- أزرار الإجراءات --}}
            @if(auth()->user()->role === 'admin' || auth()->id() === $expense->user_id)
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-600 flex justify-end gap-2">
                <button onclick='editExpense(@json($expense))'
                    class="px-3 py-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-edit ml-1"></i> تعديل
                </button>

                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-3 py-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-trash-alt ml-1"></i> حذف
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-file-invoice-dollar text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-400 dark:text-gray-500 italic text-lg">لا توجد مصاريف مسجلة حالياً</p>
            <button onclick="openAddExpenseModal()" class="mt-4 text-green-600 hover:text-green-700 font-medium">
                <i class="fas fa-plus-circle ml-1"></i> أضف أول مصروف
            </button>
        </div>
        @endforelse
    </div>
</div>

{{-- نافذة إضافة مصروف --}}
<div id="addExpenseModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-green-600"></i> إضافة مصروف جديد
            </h3>
            <button onclick="closeAddExpenseModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">العنوان</label>
                    <input type="text" name="title" required
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">المبلغ</label>
                    <input type="number" step="0.01" name="price" required
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">التاريخ</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">الصورة (إيصال)</label>
                    <input type="file" name="image"
                        class="w-full p-2 rounded-lg border dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">الوصف</label>
                <textarea name="description" rows="3"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition-all">حفظ</button>
                <button type="button" onclick="closeAddExpenseModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-3 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
{{-- نافذة تعديل مصروف --}}
<div id="editExpenseModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> تعديل المصروف
            </h3>
            <button onclick="closeEditExpenseModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <form id="editExpenseForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">العنوان</label>
                   <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">عنوان المصروف</label>
                    <input type="text" name="title" id="edit_expense_title" required
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                    </div>
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">المبلغ</label>
                    <input type="number" step="0.01" name="price" id="edit_price" required
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">التاريخ</label>
                    <input type="date" name="date" id="edit_date" required
                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">تحديث الصورة (اختياري)</label>
                    <input type="file" name="image"
                        class="w-full p-2 rounded-lg border dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">الوصف</label>
                <textarea name="description" id="edit_description" rows="3"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:text-white resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-all">حفظ
                    التعديلات</button>
                <button type="button" onclick="closeEditExpenseModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-3 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openAddExpenseModal() { document.getElementById('addExpenseModal').classList.remove('hidden'); }
function closeAddExpenseModal() { document.getElementById('addExpenseModal').classList.add('hidden'); }
// يمكنك إضافة وظيفة editExpense بشكل مشابه للملاحظات
</script><script>
    // فتح نافذة التعديل وتعبئة البيانات
function editExpense(expense) {
    const form = document.getElementById('editExpenseForm');
    
    // تحديث رابط الـ Action للفورم ليشمل الـ ID الخاص بالمصروف
    // تأكد أن الرابط يطابق الـ route في Laravel
    form.action = `/expenses/${expense.id}`; 

    // تعبئة الحقول بالبيانات القادمة من الزر
    document.getElementById('edit_title').value = expense.title;
    document.getElementById('edit_price').value = expense.price;
    document.getElementById('edit_date').value = expense.date;
    document.getElementById('edit_description').value = expense.description || '';

    // إظهار المودال
    document.getElementById('editExpenseModal').classList.remove('hidden');
}

// إغلاق نافذة التعديل
function closeEditExpenseModal() {
    document.getElementById('editExpenseModal').classList.add('hidden');
}

// لإغلاق المودال عند الضغط خارجه (تحسين تجربة المستخدم)
window.onclick = function(event) {
    let modal = document.getElementById('editExpenseModal');
    if (event.target == modal) {
        closeEditExpenseModal();
    }
}
</script>
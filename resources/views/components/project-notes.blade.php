@props(['SpecialRequest'])

<div class="p-6 space-y-6">
    {{-- رأس القسم --}}
    <div class="flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-sticky-note text-yellow-600"></i> ملاحظات المشروع
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <i class="fas fa-info-circle ml-1"></i>
                إجمالي الملاحظات: {{ $SpecialRequest->notes->count() }}
            </p>
        </div>
        {{-- زر إضافة ملاحظة --}}
        <button onclick="openAddNoteModal()"
            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-plus-circle"></i> إضافة ملاحظة
        </button>
    </div>

    {{-- قائمة الملاحظات --}}
    <div class="space-y-4">
        @forelse($SpecialRequest->notes as $note)
        {{-- التحقق من صلاحية العرض --}}
        @php
        $canView = auth()->user()->role === 'admin'
        || auth()->user()->role === 'manager'
        || auth()->id() === $note->user_id
        || (auth()->user()->role === 'client' && $note->visible_to_client);
        @endphp

        @if($canView)
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
            {{-- رأس الملاحظة --}}
            <div
                class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-gray-700 dark:to-gray-700 border-b dark:border-gray-600">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-sticky-note text-yellow-600"></i>
                            {{ $note->title }}
                        </h3>
                        <div class="flex items-center gap-3 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-circle"></i>
                                {{ $note->user->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="far fa-clock"></i>
                                {{ $note->created_at->diffForHumans() }}
                            </span>
                            @if($note->created_at != $note->updated_at)
                            <span class="text-xs text-blue-600 dark:text-blue-400">
                                <i class="fas fa-edit ml-1"></i>معدلة
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- شارة الظهور للعميل --}}
                    <div class="flex items-center gap-2">
                        @if(auth()->user()->role === 'admin')
                        <form action="{{ route('dashboard.special-request.toggle-note-visibility', $note->id) }}"
                            method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $note->visible_to_client ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                title="{{ $note->visible_to_client ? 'مرئية للعميل - اضغط للإخفاء' : 'مخفية عن العميل - اضغط للإظهار' }}">
                                <i class="fas {{ $note->visible_to_client ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                {{ $note->visible_to_client ? 'مرئية للعميل' : 'مخفية' }}
                            </button>
                        </form>
                        @else
                        @if($note->visible_to_client)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            <i class="fas fa-eye"></i> مرئية للعميل
                        </span>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- محتوى الملاحظة --}}
            <div class="p-4">
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{
                    $note->description }}</p>
            </div>

            {{-- أزرار الإجراءات --}}
            @if(auth()->user()->role === 'admin' || auth()->id() === $note->user_id)
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-600 flex justify-end gap-2">
                <button onclick='editNote(@json($note))'
                    class="px-3 py-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-edit ml-1"></i> تعديل
                </button>

                <form action="{{ route('dashboard.special-request.destroy-note', $note->id) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟');" class="inline">
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
        @endif
        @empty
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-sticky-note text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-400 dark:text-gray-500 italic text-lg">لا توجد ملاحظات حالياً</p>
            <button onclick="openAddNoteModal()" class="mt-4 text-yellow-600 hover:text-yellow-700 font-medium">
                <i class="fas fa-plus-circle ml-1"></i> أضف أول ملاحظة
            </button>
        </div>
        @endforelse
    </div>
</div>

{{-- نافذة إضافة ملاحظة --}}
<div id="addNoteModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-yellow-600"></i>
                إضافة ملاحظة جديدة
            </h3>
            <button onclick="closeAddNoteModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('dashboard.special-request.add-note', $SpecialRequest) }}" method="POST"
            class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    <i class="fas fa-heading text-gray-400 ml-1"></i>
                    عنوان الملاحظة
                </label>
                <input type="text" name="title" required placeholder="مثال: ملاحظة مهمة حول التصميم"
                    class="w-full p-3 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    <i class="fas fa-align-right text-gray-400 ml-1"></i>
                    الوصف
                </label>
                <textarea name="description" rows="5" required placeholder="اكتب تفاصيل الملاحظة هنا..."
                    class="w-full p-3 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-none"></textarea>
            </div>

            @if(auth()->user()->role === 'admin')
            <div
                class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <input type="checkbox" name="visible_to_client" id="visible_to_client" value="1"
                    class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <label for="visible_to_client"
                    class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-eye text-blue-600 ml-1"></i>
                    إظهار هذه الملاحظة للعميل
                </label>
            </div>
            @endif

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-yellow-600 text-white py-3 rounded-lg font-bold hover:bg-yellow-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-save ml-1"></i> حفظ الملاحظة
                </button>
                <button type="button" onclick="closeAddNoteModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-3 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة تعديل ملاحظة --}}
<div id="editNoteModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i>
                تعديل الملاحظة
            </h3>
            <button onclick="closeEditNoteModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form id="editNoteForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    <i class="fas fa-heading text-gray-400 ml-1"></i>
                    عنوان الملاحظة
                </label>
                <input type="text" name="title" id="edit_note_title" required
                    class="w-full p-3 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    <i class="fas fa-align-right text-gray-400 ml-1"></i>
                    الوصف
                </label>
                <textarea name="description" id="edit_note_description" rows="5" required
                    class="w-full p-3 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>

            @if(auth()->user()->role === 'admin')
            <div
                class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <input type="checkbox" name="visible_to_client" id="edit_visible_to_client" value="1"
                    class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <label for="edit_visible_to_client"
                    class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-eye text-blue-600 ml-1"></i>
                    إظهار هذه الملاحظة للعميل
                </label>
            </div>
            @endif

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-check-circle ml-1"></i> حفظ التعديلات
                </button>
                <button type="button" onclick="closeEditNoteModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-3 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // فتح نافذة إضافة ملاحظة
function openAddNoteModal() {
    document.getElementById('addNoteModal').classList.remove('hidden');
}

// إغلاق نافذة إضافة ملاحظة
function closeAddNoteModal() {
    document.getElementById('addNoteModal').classList.add('hidden');
}

// فتح نافذة تعديل ملاحظة
function editNote(note) {
    const form = document.getElementById('editNoteForm');
    form.action = `/notes/${note.id}`;
    
    document.getElementById('edit_note_title').value = note.title || '';
    document.getElementById('edit_note_description').value = note.description || '';
    
    @if(auth()->user()->role === 'admin')
        document.getElementById('edit_visible_to_client').checked = note.visible_to_client || false;
    @endif
    
    document.getElementById('editNoteModal').classList.remove('hidden');
}

// إغلاق نافذة تعديل ملاحظة
function closeEditNoteModal() {
    document.getElementById('editNoteModal').classList.add('hidden');
}

// إغلاق النوافذ عند الضغط على الخلفية
document.getElementById('addNoteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddNoteModal();
    }
});

document.getElementById('editNoteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditNoteModal();
    }
});

// إغلاق النوافذ عند الضغط على ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddNoteModal();
        closeEditNoteModal();
    }
});
</script>
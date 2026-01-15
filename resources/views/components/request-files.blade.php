@props(['SpecialRequest'])

<div class="p-6 space-y-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center border-b pb-4">
        <h2 class="text-xl font-bold flex items-center gap-2 text-gray-800 dark:text-white">
            <i class="fas fa-folder-open text-blue-500"></i> ملفات المشروع
        </h2>
        <button onclick="document.getElementById('addFileModal').classList.remove('hidden')"
            class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
            <i class="fas fa-upload ml-1"></i> رفع ملف جديد
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse(($SpecialRequest->requestFiles ?? collect()) as $file)
        <div
            class="group border dark:border-gray-700 p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all relative">
            <div class="flex items-start gap-3">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0 text-right" dir="rtl">
                    <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $file->title }}</h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $file->description ?? 'لا يوجد وصف' }}</p>

                    <div class="mt-2 flex flex-col gap-1">
                        <div class="flex items-center gap-1 text-[10px] text-blue-600 font-bold">
                            <i class="fas fa-user-edit"></i>
                            <span>بواسطة: {{ $file->user->name ?? 'غير معروف' }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold">عرض الملف</a>
                            <span class="text-[10px] text-gray-400">{{ $file->created_at->format('Y/m/d') }}</span>
                        </div>
                    </div>
                </div>

                {{-- أزرار التحكم --}}
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    {{-- زر التعديل المعدل --}}
                    <button
                        onclick="openEditFileModal('{{ $file->id }}', '{{ $file->title }}', '{{ $file->description }}')"
                        class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit"></i>
                    </button>

                    <form action="{{ route('files.destroy', $file->id) }}" method="POST"
                        onsubmit="return confirm('حذف؟')">
                        @csrf @method('DELETE')
                        <button class="text-black hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-400 py-10">لا توجد ملفات مرفوعة.</p>
        @endforelse
    </div>
</div>

{{-- مودال إضافة ملف (Add) --}}
<div id="addFileModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-xl text-right" dir="rtl">
        <h3 class="text-lg font-bold mb-4">رفع ملف جديد</h3>
        <form action="{{ route('request-files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
            <input type="text" name="title" placeholder="عنوان الملف" required
                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:text-white outline-none">
            <textarea name="description" placeholder="تفاصيل إضافية"
                class="w-full p-3 border rounded-xl dark:bg-gray-700 outline-none h-24"></textarea>
            <input type="file" name="file" required class="text-xs">
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-xl font-bold">رفع</button>
                <button type="button" onclick="document.getElementById('addFileModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 py-2 rounded-xl font-bold dark:bg-gray-700 dark:text-white">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال تعديل ملف (Edit) --}}
<div id="editFileModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-xl text-right" dir="rtl">
        <h3 class="text-lg font-bold mb-4 text-blue-600">تعديل بيانات الملف</h3>
        <form id="editFileForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="title" id="edit_file_title" required
                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:text-white outline-none">
            <textarea name="description" id="edit_file_description"
                class="w-full p-3 border rounded-xl dark:bg-gray-700 outline-none h-24"></textarea>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-xl font-bold">حفظ
                    التغييرات</button>
                <button type="button" onclick="document.getElementById('editFileModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 py-2 rounded-xl font-bold dark:bg-gray-700">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- الكود البرمجي (JavaScript) --}}
<script>
    function openEditFileModal(id, title, description) {
        // 1. تحديد الـ Route الصحيح للفورم
        const form = document.getElementById('editFileForm');
        form.action = `/project-files/${id}`; // تأكد أن هذا المسار مطابق للـ Route في web.php

        // 2. تعبئة البيانات في الحقول
        document.getElementById('edit_file_title').value = title;
        document.getElementById('edit_file_description').value = description !== 'undefined' ? description : '';

        // 3. إظهار المودال
        document.getElementById('editFileModal').classList.remove('hidden');
    }
</script>
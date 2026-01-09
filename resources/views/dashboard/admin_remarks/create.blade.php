@extends('layouts.app')

@section('title', 'إضافة ملاحظة إدارية')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="#" second="ملاحظات الإدارة" third="إضافة ملاحظة" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-yellow-50 to-white dark:from-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-comment-alt text-yellow-500"></i>
                    تسجيل ملاحظة جديدة للموظف
                </h2>
            </div>

            <form method="POST" action="{{ route('dashboard.admin_remarks.store') }}" class="p-6 space-y-6"
                enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user-tie ml-1 text-blue-600"></i> الموظف المعني
                    </label>
                    <select name="user_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="" disabled selected>اختر الموظف من القائمة...</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-edit ml-1 text-blue-600"></i> تفاصيل الملاحظة
                    </label>
                    <textarea name="details" rows="5" required placeholder="اكتب الملاحظة هنا بالتفصيل..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="border-t pt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        <i class="fas fa-image ml-1 text-blue-600"></i> مرفق توضيحي (صورة)
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-44 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">اضغط لرفع صورة أو اسحبها هنا</p>
                            </div>
                            <input id="dropzone-file" type="file" name="image" class="hidden" accept="image/*"
                                onchange="previewImg(this)" />
                        </label>
                    </div>
                    <div id="img-preview-container" class="mt-4 hidden">
                        <img id="img-show" src="#" class="max-w-xs rounded-lg shadow-md border">
                    </div>
                </div>

                <div class="pt-6 border-t flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> حفظ الملاحظة
                    </button>
                    <button type="reset"
                        class="px-6 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                        إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function previewImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-show').src = e.target.result;
                document.getElementById('img-preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
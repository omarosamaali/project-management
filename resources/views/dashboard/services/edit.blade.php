@extends('layouts.app')

@section('title', 'تعديل الخدمة')

@section('content')

<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.services.index') }}" second="الخدمة"
        third="تعديل الخدمة" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات الخدمة: {{ $service->name_ar }}
            </h2>

            <form method="POST" action="{{ route('dashboard.services.update', $service->id) }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="border-b pb-6">
                    <label for="image" class="block text-xl font-semibold text-gray-800 mb-4 items-center gap-2">
                        <i class="fas fa-image text-blue-600"></i>
                        صورة الخدمة
                    </label>
                    <div class="relative">
                        <div class="flex items-center justify-center w-full">
                            <label for="image"
                                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-150 ease-in-out">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">اضغط لرفع صورة</span> أو اسحب وأفلت
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 2MB)</p>
                                </div>
                                <input type="file" id="image" name="image" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <div id="image-preview" class="mt-4">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="Preview" class="max-w-xs rounded-lg shadow-md">
                        </div>
                    </div>

                    @error('image')
                    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- حقل الاسم بالعربي --}}
                <div>
                    <label for="name_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم
                        (بالعربي):</label>
                    <input type="text" id="name_ar" name="name_ar" placeholder="أدخل اسم الخدمة هنا" required
                        value="{{ old('name_ar', $service->name_ar) }}"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    @error('name_ar')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- حقل الاسم بالانجليزي --}}
                <div>
                    <label for="name_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم
                        (بالانجليزي):</label>
                    <input type="text" id="name_en" name="name_en" placeholder="أدخل اسم الخدمة هنا" required
                        value="{{ old('name_en', $service->name_en) }}"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    @error('name_en')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-briefcase text-blue-600"></i>
                        نوع الخدمة
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-gray-300 bg-gray-50 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                            <input {{ old('status', $service->status === 'active' ? 'checked' : '') }} type="radio"
                            name="status" value="active" class="w-5 h-5 text-blue-600">
                            <span class="font-medium text-gray-700">فعال</span>
                        </label>

                        <label
                            class="flex items-center gap-3 p-4 border-2 border-gray-300 bg-gray-50 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                            <input {{ old('status', $service->status === 'inactive' ? 'checked' : '') }}
                            type="radio" name="status" value="inactive" class="w-5 h-5 text-blue-600">
                            <span class="font-medium text-gray-700">غير فعال</span>
                        </label>
                    </div>
                    @error('status')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- زر الحفظ والإرسال --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('dashboard.services.index') }}"
                        class="mt-3 w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                        إلغاء والعودة
                    </a>
                </div>
            </form>
        </div>
    </div>

<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
</section>

@endsection
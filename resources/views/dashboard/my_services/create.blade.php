@extends('layouts.app')

@section('title', 'إضافة خدمة')

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

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_services.index') }}" second="خدماتي"
        third="إضافة خدمة" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <form method="POST" action="{{ route('dashboard.my_services.store') }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        عنوان الخدمة
                    </label>
                    <input type="text" id="title" name="title" placeholder="ادخل اسم الخدمة هنا" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('title')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Services --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        اختر الخدمة: <span class="text-red-500">*</span>
                    </label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($services as $service)
                        <div class="flex items-center">
                            <input type="radio" id="service_{{ $service->id }}" name="service_id"
                                value="{{ $service->id }}"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="service_{{ $service->id }}"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $service->name_ar }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    {{-- تحديث رسالة الخطأ لتستهدف services_id --}}
                    @error('service_id')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        اختر خدمة واحدة أو أكثر يعمل بها الشريك.
                    </p>
                </div>

                {{-- price --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        اجمالي سعر الخدمة</label>
                    <input type="number" id="price" name="price" placeholder="ادخل السعر" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    <span class="text-sm text-gray-500">نسبة شركة EVORQ هي <span class="text-blue-500 font-bold">{{
                            Auth::user()->percentage }}%</span> سوف تحصل علي اجمالي سعر الخدمة ناقص نسبة شركة
                        EVORQ</span>
                    @error('price')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- duration --}}
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                        مدة تسليم المشروع</label>
                    <input type="number" id="duration" name="duration" placeholder="مدة التسليم بالأيام مثال 30" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('duration')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        عن الخدمة
                    </label>
                    <textarea rows="5" id="description" name="description" placeholder="ادخل تفاصيل عن الخدمة" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500
                            focus:border-blue-500 transition duration-150 ease-in-out"></textarea>
                    @error('description')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- what_you_will_get --}}
                <div>
                    <label for="what_you_will_get" class="block text-sm font-medium text-gray-700 mb-1">
                        علي ماذا سوف يحصل العميل
                    </label>
                    <textarea rows="5" id="what_you_will_get" name="what_you_will_get" placeholder="ادخل ماذا سوف يحصل العميل" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500
                                                            focus:border-blue-500 transition duration-150 ease-in-out"></textarea>
                    @error('what_you_will_get')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- submit --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ بيانات الشريك
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection
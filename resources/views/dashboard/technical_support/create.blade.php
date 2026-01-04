@extends('layouts.app')

@section('title', 'فتح تذكرة دعم جديدة')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
<x-breadcrumb first="الرئيسية" link="{{ route('dashboard.technical_support.index') }}"
        third="إضافة تذكرة دعم" second="التذاكر" />
    <div class="mx-auto w-full max-w-4xl">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg p-6">

            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">نموذج فتح تذكرة دعم جديدة</h2>

            <form action="{{ route('dashboard.technical_support.store') }}" method="POST">
                @csrf

                {{-- حقل الموضوع (Subject) --}}
                <div class="mb-4">
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">موضوع
                        الشكوى/التذكرة:</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="مشكلة في إظهار البيانات، استفسار بخصوص خدمة، إلخ.">
                    @error('subject')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- حقل تفاصيل المشكلة (Description) --}}
                <div class="mb-4">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">تفاصيل
                        المشكلة/الشكوى كاملة:</label>
                    <textarea id="description" name="description" rows="6" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="يرجى وصف المشكلة بالتفصيل وموعد حدوثها إن أمكن.">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- حقل ربط الطلب (Request ID) (اختياري) --}}
                <div class="mb-6">
                    <label for="request_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ربط
                        التذكرة بطلب سابق <span class="text-red-500">*</span>:</label>
                    <select id="request_id" name="request_id" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">لا يوجد طلب محدد</option>
                        @foreach($userRequests as $request)
                        <option value="{{ $request->id }}" {{ old('request_id')==$request->id ? 'selected' : '' }}>
                            {{ $request->system->name_ar ?? $request->title }}
                        </option>
                        @endforeach
                    </select>
                    @error('request_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full sm:w-auto flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">
                    <i class="fa-solid fa-ticket-simple ml-2"></i>
                    فتح تذكرة الدعم الآن
                </button>
            </form>

        </div>
    </div>
</section>

@endsection
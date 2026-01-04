@extends('layouts.app')

@section('title', 'خدماتي المحفوظة')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_service.index') }}" second="خدماتي" third="المحفوظ" />

    <div class="mx-auto w-full">
        <!-- Header -->
        <div class="bg-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">خدماتي المحفوظة</h1>
                    <p class="text-blue-100">أنت تقدم {{ count($selectedServices) }} خدمة</p>
                </div>
                <a href="{{ route('dashboard.my_service.index') }}"
                    class="bg-white text-blue-600 px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 inline-flex items-center shadow">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل الخدمات
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-300">
            <i class="fas fa-check-circle ml-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(empty($selectedServices))
        <!-- لو مافيش خدمات -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center border border-gray-200">
            <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-700 mb-2">لم تقم باختيار أي خدمات بعد</h3>
            <p class="text-gray-500 mb-6">اختر الخدمات التي تحتاجها من القائمة المتاحة</p>
            <a href="{{ route('dashboard.my_service.index') }}"
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg">
                <i class="fas fa-plus ml-2"></i>
                تصفح الخدمات
            </a>
        </div>
        @else
        <!-- Services Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($selectedServices as $service)
            <div class="bg-white rounded-lg shadow-lg border-2 border-blue-600 overflow-hidden">
                <!-- Header الخدمة -->
                <div class="bg-blue-600 p-3 text-white relative">
                    <!-- علامة النجاح -->
                    <div
                        class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-check text-blue-600 text-sm font-bold"></i>
                    </div>

                    <!-- الأيقونة -->
                    <div
                        class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 mx-auto">
                        <i class="fas {{ $service['icon'] }} text-2xl"></i>
                    </div>

                    <!-- اسم الخدمة -->
                    <h3 class="text-sm font-bold text-center leading-tight">{{ $service['name'] }}</h3>
                </div>

                <!-- وصف الخدمة -->
                <div class="p-3 bg-blue-50">
                    <p class="text-xs text-gray-700 text-center mb-2 line-clamp-2">{{ $service['description'] }}</p>

                    <!-- حالة الخدمة -->
                    <div class="text-center">
                        <span class="text-xs font-bold text-blue-600 inline-flex items-center">
                            <i class="fas fa-check-circle ml-1"></i>
                            خدمة نشطة
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

@endsection
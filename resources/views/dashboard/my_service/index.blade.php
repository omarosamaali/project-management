@extends('layouts.app')

@section('title', 'خدماتي')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_service.index') }}" second="خدماتي" />

    <div class="mx-auto w-full">
        <!-- Header -->
        <div class="bg-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">ما هي الخدمات التي تقدمها؟</h1>
                    <p class="text-blue-100">اختر الخدمات التي تجيدها وتستطيع تقديمها للعملاء</p>
                </div>
                @if(!empty($selectedServices))
                <a href="{{ route('dashboard.my_service.show') }}"
                    class="bg-white text-blue-600 px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 inline-flex items-center shadow">
                    <i class="fas fa-eye ml-2"></i>
                    عرض خدماتي ({{ count($selectedServices) }})
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-300">
            <i class="fas fa-check-circle ml-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('info'))
        <div class="mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-300">
            <i class="fas fa-info-circle ml-2"></i>{{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-300">
            <i class="fas fa-exclamation-circle ml-2"></i>{{ $errors->first() }}
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('dashboard.my_service.store') }}" method="POST">
            @csrf

            <!-- Services Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">
                @foreach($availableServices as $service)
                <label class="cursor-pointer group">
                    <input type="checkbox" name="services[]" value="{{ $service['id'] }}" class="hidden peer" {{
                        in_array($service['id'], $selectedServices) ? 'checked' : '' }}>

                    <div
                        class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-xl peer-checked:scale-105 overflow-hidden">
                        <!-- Header الخدمة -->
                        <div class="bg-blue-600 peer-checked:bg-blue-700 p-3 text-white relative transition-colors">
                            <!-- علامة الاختيار -->
                            <div
                                class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full hidden peer-checked:flex items-center justify-center shadow-lg">
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
                        <div class="p-3 bg-gray-50 peer-checked:bg-white">
                            <p class="text-xs text-gray-600 text-center mb-2 line-clamp-2">{{ $service['description'] }}
                            </p>

                            <!-- زر الاختيار -->
                            <div class="text-center">
                                <span
                                    class="text-xs font-medium text-gray-500 peer-checked:text-blue-600 peer-checked:font-bold inline-flex items-center">
                                    <i class="fas fa-hand-pointer ml-1 peer-checked:hidden"></i>
                                    <i class="fas fa-check-circle ml-1 hidden peer-checked:inline"></i>
                                    <span class="peer-checked:hidden">اضغط للاختيار</span>
                                    <span class="hidden peer-checked:inline">✓ مُختارة</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-right">
                        <p class="text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-info-circle ml-1 text-blue-600"></i>
                            يمكنك اختيار أكثر من خدمة
                        </p>
                        <p class="text-gray-500 text-xs">
                            اختر جميع الخدمات التي تستطيع تقديمها بجودة عالية
                        </p>
                    </div>
                    @if(Auth::user()->role === 'independent_partner' && Auth::user()->status === 'active')
                    <button type="submit"
                        class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-save ml-2"></i>
                        حفظ خدماتي
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
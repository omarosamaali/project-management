@extends('layouts.app')

@section('title', 'إضافة دورة')

@section('content')
@include('dashboard.courses.partials.course-switch-styles')
<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    /* Tab Styles */
    .tab-button {
        position: relative;
        padding: 1rem 1.5rem;
        font-weight: 500;
        color: #6B7280;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .tab-button:hover {
        color: #0b8f7f;
        background-color: rgba(11, 143, 127, .1);
    }

    .tab-button.active {
        color: #061525;
        border-bottom-color: #0b8f7f;
        background-color: rgba(11, 143, 127, .12);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
</style>

<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات" third="إضافة دورة" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

            <!-- Error Messages -->
            @if($errors->any())
            <div class="p-4 m-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium">يوجد بعض الأخطاء:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.courses.store') }}" method="POST" enctype="multipart/form-data"
                id="courseForm">
                @csrf

                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 bg-gray-50 overflow-x-auto">
                    <nav class="flex gap-2 px-4" id="tabs-nav">
                        <button type="button" class="tab-button active" data-tab="basic-info">
                            <i class="fas fa-info-circle ml-2"></i>
                            المعلومات الأساسية
                        </button>
                        <button type="button" class="tab-button" data-tab="content">
                            <i class="fas fa-align-right ml-2"></i>
                            المحتوى والوصف
                        </button>
                        <button type="button" class="tab-button hidden" data-tab="educational-path" id="path-tab-btn">
                            <i class="fas fa-route ml-2"></i>
                            المسار التعليمي
                        </button>
                        <button type="button" class="tab-button" data-tab="features">
                            <i class="fas fa-star ml-2"></i>
                            المميزات والمتطلبات
                        </button>
                        <button type="button" class="tab-button" data-tab="actions">
                            <i class="fas fa-link ml-2"></i>
                            الأزرار والصور
                        </button>
                        <button type="button" class="tab-button" data-tab="settings">
                            <i class="fas fa-cog ml-2"></i>
                            الإعدادات النهائية
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div class="p-6">

                    <!-- Tab 1: Basic Info -->
                    <div class="tab-content active" id="basic-info">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Course Name AR -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم الدورة (بالعربي) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="name_ar" name="name_ar" required value="{{ old('name_ar') }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="مثال: دورة البرمجة المتقدمة">
                                @error('name_ar')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Course Name EN -->
                            <div>
                                <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                    Course Name (English) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="name_en" name="name_en" required dir="ltr"
                                    value="{{ old('name_en') }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ex: Advanced Programming Course">
                                @error('name_en')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Category & Level -->
                        <div class="mt-8 pt-6 border-t" id="course_category_section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-folder-tree text-blue-600"></i>
                                التصنيف والمستوى
                            </h3>
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <!-- Category -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        التصنيف <span class="text-red-600">*</span>
                                    </label>
                                    <select id="course_category_id" name="course_category_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- اختر التصنيف --</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) old('course_category_id') === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->title(app()->getLocale()) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('course_category_id')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if(auth()->user()->isAdmin())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        المحاضر المسؤول
                                    </label>
                                    <select id="trainer_id" name="trainer_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- بدون محاضر / تعيين لاحقاً --</option>
                                        @foreach(($trainers ?? []) as $trainer)
                                        <option value="{{ $trainer->id }}" {{ (string) old('trainer_id') === (string) $trainer->id ? 'selected' : '' }}>
                                            {{ $trainer->name }} ({{ $trainer->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('trainer_id')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                @endif
                            </div>

                            <!-- Levels -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    مستوى الدورة
                                </label>
                                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-3">
                                    @php
                                        $selectedLevels = old('levels', []);
                                        if (!is_array($selectedLevels)) $selectedLevels = [];
                                    @endphp
                                    @foreach(\App\Models\Course::levelOptions() as $level)
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                        <input type="checkbox" name="levels[]" value="{{ $level['key'] }}"
                                            {{ in_array($level['key'], $selectedLevels, true) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300" style="accent-color:#0b8f7f;">
                                        <span class="text-sm font-medium text-gray-800">{{ $level['label_ar'] }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">يمكن اختيار أكثر من مستوى. شارة «مجاني» تظهر تلقائياً عند تفعيل دورة مجانية.</p>
                                @error('levels')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                @error('levels.*')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Type -->
                        <div class="mt-8 pt-6 border-t" id="course_type_section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-layer-group text-blue-600"></i>
                                نوع الدورة
                            </h3>

                            <!-- Location Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    نوع المكان <span class="text-red-600">*</span>
                                </label>
                                <div class="grid md:grid-cols-3 gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                        <input type="radio" name="location_type" value="online" {{
                                            old('location_type', 'online')=='online' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <i class="fas fa-wifi text-blue-600 ml-2"></i>
                                                أونلاين
                                            </div>
                                            <div class="text-xs text-gray-500">محاضرة مباشرة</div>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition">
                                        <input type="radio" name="location_type" value="on_site" {{
                                            old('location_type')=='on_site' ? 'checked' : '' }}
                                            class="w-5 h-5 text-green-600">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <i class="fas fa-building text-green-600 ml-2"></i>
                                                حضوري
                                            </div>
                                            <div class="text-xs text-gray-500">في موقع محدد</div>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-amber-50 transition">
                                        <input type="radio" name="location_type" value="recorded" {{
                                            old('location_type')=='recorded' ? 'checked' : '' }}
                                            class="w-5 h-5 text-amber-600">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <i class="fas fa-play-circle text-amber-600 ml-2"></i>
                                                مسجّلة
                                            </div>
                                            <div class="text-xs text-gray-500">مسار تعليمي بالفيديو</div>
                                        </div>
                                    </label>
                                </div>
                                @error('location_type')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing & Capacity -->
                        <div class="mt-8 pt-6 border-t" id="course_pricing_section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-tag text-blue-600"></i>
                                السعر وعدد المشتركين
                            </h3>
                            @php
                                // Only treat as free when an explicit price of 0 was posted (never empty/default).
                                $isFreeOld = old('price') !== null && old('price') !== '' && (float) old('price') <= 0;
                                $trainerPriceCapped = auth()->user()->isTrainer() && ! auth()->user()->isAdmin();
                                $trainerMaxPrice = (float) config('courses.trainer_max_price', 400);
                                $allowPrivateOld = (string) old('allows_private_requests', '0') === '1';
                                $trainerPrivateCapped = $trainerPriceCapped;
                            @endphp
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Price / Free toggle -->
                                <div class="space-y-3">
                                    <div>
                                        <label for="is_free_toggle" class="block text-sm font-medium text-gray-700 mb-2">
                                            دورة مجانية
                                        </label>
                                        <label class="course-switch-field cursor-pointer">
                                            <span class="text-sm text-gray-600 truncate">تفعيل = سعر 0 بدون إدخال سعر</span>
                                            <span class="course-switch">
                                                <input type="checkbox" id="is_free_toggle"
                                                    {{ $isFreeOld ? 'checked' : '' }}>
                                                <span class="course-switch-track" aria-hidden="true"></span>
                                            </span>
                                        </label>
                                    </div>

                                    <div id="price_field_wrap" class="{{ $isFreeOld ? 'hidden' : '' }} space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            السعر الكلي <span class="text-red-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" name="price" id="price" min="0" step="0.01"
                                                @if($trainerPriceCapped) max="{{ $trainerMaxPrice }}" @endif
                                                value="{{ old('price', $isFreeOld ? '0' : '') }}"
                                                {{ $isFreeOld ? '' : 'required' }}
                                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-20"
                                                placeholder="{{ $trainerPriceCapped ? number_format($trainerMaxPrice, 2, '.', '') : '999.00' }}">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">
                                                <x-drhm-icon width="12" height="14" />
                                            </span>
                                        </div>
                                        @if($trainerPriceCapped)
                                        <p class="text-xs text-slate-500">
                                            الحد الأقصى للسعر هو
                                            <span class="inline-flex items-center gap-1 font-semibold text-slate-700" dir="ltr">
                                                {{ number_format($trainerMaxPrice, 0) }}
                                                <x-drhm-icon width="11" height="12" />
                                            </span>
                                        </p>
                                        @endif
                                        @error('price')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                        @include('dashboard.courses.partials.trainer-profit-preview', [
                                            'trainerProfitPercentage' => $trainerProfitPercentage ?? null,
                                            'trainerProfitPercentages' => $trainerProfitPercentages ?? null,
                                        ])
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        الحد الأقصى لعدد المشتركين في الدورة <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="counter" required min="0" step="1"
                                        value="{{ old('counter', 0) }}"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0">
                                    @error('counter')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">
                                        كم شخص يمكن تسجيله في هذه الدورة كحد أقصى؟
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Private course requests -->
                        <div class="mt-8 pt-6 border-t" id="course_private_section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-user-lock text-blue-600"></i>
                                طلبات الدورات الخاصة
                            </h3>
                            <div class="space-y-3 max-w-xl">
                                <div>
                                    <label class="course-switch-field cursor-pointer">
                                        <span class="text-sm text-gray-600 truncate">السماح بطلب دورة خاصة فردية</span>
                                        <span class="course-switch">
                                            <input type="hidden" name="allows_private_requests" value="0">
                                            <input type="checkbox" name="allows_private_requests" id="allows_private_requests"
                                                value="1" {{ $allowPrivateOld ? 'checked' : '' }}>
                                            <span class="course-switch-track" aria-hidden="true"></span>
                                        </span>
                                    </label>
                                </div>
                                <div id="private_price_wrap" class="{{ $allowPrivateOld ? '' : 'hidden' }} space-y-3">
                                    @php
                                        $privateFreeOld = old('private_course_price') !== null
                                            && old('private_course_price') !== ''
                                            && (float) old('private_course_price') <= 0;
                                    @endphp
                                    <div>
                                        <label for="is_private_free_toggle" class="block text-sm font-medium text-gray-700 mb-2">
                                            دورة خاصة مجانية
                                        </label>
                                        <label class="course-switch-field cursor-pointer">
                                            <span class="text-sm text-gray-600 truncate">تفعيل = سعر خاص 0 بدون إدخال سعر</span>
                                            <span class="course-switch">
                                                <input type="checkbox" id="is_private_free_toggle"
                                                    {{ $privateFreeOld ? 'checked' : '' }}>
                                                <span class="course-switch-track" aria-hidden="true"></span>
                                            </span>
                                        </label>
                                    </div>
                                    <div id="private_price_field_wrap" class="{{ $privateFreeOld ? 'hidden' : '' }} space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            سعر الدورة الخاصة <span class="text-red-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" name="private_course_price" id="private_course_price" min="0" step="0.01"
                                                @if($trainerPrivateCapped) max="500" @endif
                                                value="{{ old('private_course_price', $privateFreeOld ? '0' : '') }}"
                                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-20"
                                                placeholder="{{ $trainerPrivateCapped ? '500.00' : '999.00' }}"
                                                {{ ($allowPrivateOld && ! $privateFreeOld) ? 'required' : '' }}>
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">
                                                <x-drhm-icon width="12" height="14" />
                                            </span>
                                        </div>
                                        @if($trainerPrivateCapped)
                                        <p class="text-xs text-slate-500">
                                            الحد الأقصى لسعر الدورة الخاصة للمحاضر
                                            <span class="inline-flex items-center gap-1 font-semibold text-slate-700" dir="ltr">
                                                500 <x-drhm-icon width="11" height="12" />
                                            </span>
                                        </p>
                                        @endif
                                        @error('private_course_price')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                        @include('dashboard.courses.partials.trainer-profit-preview-private', [
                                            'trainerProfitPercentages' => $trainerProfitPercentages ?? null,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Streaming platform / meeting link + venue details -->
                        <div class="mt-8 pt-6 border-t" id="course_location_details_section">
                            <!-- Online Link / YouTube Live -->
                            <div id="online_link_container" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">منصة البث / الاجتماع</label>
                                    <div class="grid sm:grid-cols-2 gap-3">
                                        <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                            <input type="radio" name="meeting_provider" value="youtube"
                                                {{ old('meeting_provider', 'youtube') === 'youtube' ? 'checked' : '' }}
                                                class="mt-1 meeting-provider-radio" style="accent-color:#0b8f7f;">
                                            <span>
                                                <span class="block font-medium text-gray-800">يوتيوب لايف داخل المنصة</span>
                                                <span class="block text-xs text-gray-500">يُعرض البث المباشر داخل غرفة المحاضرة مع النقاش</span>
                                            </span>
                                        </label>
                                        <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                            <input type="radio" name="meeting_provider" value="external"
                                                {{ old('meeting_provider') === 'external' ? 'checked' : '' }}
                                                class="mt-1 meeting-provider-radio" style="accent-color:#0b8f7f;">
                                            <span>
                                                <span class="block font-medium text-gray-800">رابط خارجي</span>
                                                <span class="block text-xs text-gray-500">Google Meet / Zoom / غيرها — يُفتح في تبويب جديد مع صفحة النقاش</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div id="youtube_provider_hint" class="{{ old('meeting_provider', 'youtube') === 'youtube' ? '' : 'hidden' }} p-3 text-sm text-teal-900 bg-teal-50 border border-teal-200 rounded-lg">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    الصق رابط بث يوتيوب المباشر (أو فيديو يوتيوب). سيُضمَّن داخل غرفة المحاضرة قبل موعد البداية.
                                </div>

                                <div id="external_provider_hint" class="{{ old('meeting_provider') === 'external' ? '' : 'hidden' }} p-3 text-sm text-amber-900 bg-amber-50 border border-amber-200 rounded-lg">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    عند دخول المحاضرة تُفتح صفحة النقاش هنا، ويُفتح رابط الاجتماع في تبويب جديد تلقائياً (بدون إطار مضمّن).
                                </div>

                                <div id="meeting_link_fields">
                                    <label class="block text-sm font-medium text-gray-700 mb-2" id="online_link_label">
                                        رابط البث / الاجتماع <span class="text-red-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-link absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="url" name="online_link" id="online_link" dir="ltr"
                                            value="{{ old('online_link') }}"
                                            class="placeholder-gray-400 w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="https://www.youtube.com/live/... أو https://youtu.be/...">
                                    </div>
                                    @error('online_link')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Venue Details -->
                            <div id="venue_container" class="hidden space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            اسم المكان <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" name="venue_name" id="venue_name"
                                            value="{{ old('venue_name') }}"
                                            class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="قاعة المؤتمرات - فندق الريتز">
                                        @error('venue_name')
                                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            رابط الخريطة
                                        </label>
                                        <div class="relative">
                                            <i
                                                class="fas fa-map-marked-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input type="url" name="venue_map_url" id="venue_map_url" dir="ltr"
                                                value="{{ old('venue_map_url') }}"
                                                class="placeholder-gray-400 w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="https://maps.google.com/...">
                                        </div>
                                        @error('venue_map_url')
                                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        تفاصيل المكان
                                    </label>
                                    <textarea name="venue_details" id="venue_details" rows="3"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="تفاصيل إضافية عن المكان...">{{ old('venue_details') }}</textarea>
                                    @error('venue_details')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        
<!-- Dates Section (hidden for recorded courses) -->
<div class="mt-8 pt-6 border-t {{ old('location_type', 'online') === 'recorded' ? 'hidden' : '' }}" id="course_dates_section">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-calendar-alt text-blue-600"></i>
        التواريخ وأيام الدورة
    </h3>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <!-- تاريخ البداية -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ ووقت البداية <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" id="start_date" name="start_date"
                value="{{ old('start_date', isset($course) ? $course->start_date?->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror"
                required>
            @error('start_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- آخر موعد للتسجيل -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                آخر موعد للتسجيل <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" id="last_date" name="last_date"
                value="{{ old('last_date', isset($course) ? $course->last_date?->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('last_date') border-red-500 @enderror"
                required>
            @error('last_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- عدد أيام الدورة (الجلسات) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                عدد أيام الدورة (جلسات التدريس) <span class="text-red-600">*</span>
            </label>
            <input type="number" id="count_days" name="count_days" min="1" step="1"
                value="{{ old('count_days', isset($course) && $course->count_days ? $course->count_days : 1) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('count_days') border-red-500 @enderror"
                required>
            @error('count_days')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- أيام الراحة -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            أيام الراحة الأسبوعية (اختياري)
        </label>
        <p class="text-xs text-gray-500 mb-3">
            الأيام التي لا تُقام فيها جلسات — لن تُحتسب ضمن أيام الدورة وسيُمدَّد تاريخ الانتهاء تلقائياً لتعويضها
        </p>

        <div id="rest-days-container" class="flex flex-wrap gap-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <!-- سيتم إنشاء الـ checkboxes ديناميكياً بواسطة JavaScript -->
        </div>
    </div>

    @include('dashboard.courses.partials.trainer-off-days-select')

    <!-- عرض الحسابات -->
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ الانتهاء المتوقع
            </label>
            <input type="text" id="end_date_display" readonly value=""
                class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-lg text-blue-700 font-semibold">
            <!-- القيمة الفعلية المُرسلة مع النموذج -->
            <input type="hidden" id="end_date" name="end_date" value="{{ old('end_date', isset($course) ? $course->end_date?->format('Y-m-d\TH:i') : '') }}">
            @error('end_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                إجمالي المدة التقويمية (بالأيام)
            </label>
            <input type="text" id="total_days_display" readonly value="0"
                class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
        </div>
    </div>
    <p class="text-xs text-gray-500 mt-2" id="off-days-hint"></p>
</div>

<!-- JavaScript لحساب تاريخ الانتهاء تلقائياً من تاريخ البداية وعدد الأيام -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const countDaysInput = document.getElementById('count_days');
    const restDaysContainer = document.getElementById('rest-days-container');
    const endDateHidden = document.getElementById('end_date');
    const endDateDisplay = document.getElementById('end_date_display');
    const totalDaysDisplay = document.getElementById('total_days_display');
    const offDaysHint = document.getElementById('off-days-hint');

    const daysMap = {
        'sunday': 'الأحد', 'monday': 'الإثنين', 'tuesday': 'الثلاثاء',
        'wednesday': 'الأربعاء', 'thursday': 'الخميس', 'friday': 'الجمعة', 'saturday': 'السبت'
    };
    const daysOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    const preSelectedDays = @json(old('rest_days', isset($course) ? $course->rest_days : []));
    const trainerOffDaysByTrainer = @json($trainerOffDaysByTrainer ?? []);
    const preSelectedOffDates = @json(old('off_dates', isset($course) ? ($course->off_dates ?? []) : []));
    const initialTrainerId = @json(old('trainer_id', isset($course) ? $course->trainer_id : (auth()->user()->isTrainer() ? auth()->id() : null)));
    const emptyMsg = @json(__('messages.course_off_days_empty'));
    const noOptionsMsg = @json(__('messages.course_off_days_no_options'));

    function normalizeDateKey(v) {
        const s = String(v || '').trim();
        const m = s.match(/^(\d{4}-\d{2}-\d{2})/);
        return m ? m[1] : s;
    }

    let selectedOffDates = (Array.isArray(preSelectedOffDates) ? preSelectedOffDates : [])
        .map(normalizeDateKey)
        .filter(Boolean);
    let currentOffOptions = [];

    const rsRoot = document.getElementById('trainer-off-days-rs');
    const rsEmpty = document.getElementById('trainer-off-days-empty');
    const rsValues = rsRoot?.querySelector('[data-rs-values]');
    const rsPlaceholder = rsRoot?.querySelector('[data-rs-placeholder]');
    const rsMenu = rsRoot?.querySelector('[data-rs-menu]');
    const rsMenuList = rsRoot?.querySelector('[data-rs-menu-list]');
    const rsInputs = rsRoot?.querySelector('[data-rs-inputs]');
    const rsClear = rsRoot?.querySelector('[data-rs-clear]');
    const rsSep = rsRoot?.querySelector('[data-rs-sep]');
    const trainerSelect = document.getElementById('trainer_id');

    function getDayName(date) {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return days[date.getDay()];
    }

    function toDateKey(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function pad2(n) { return String(n).padStart(2, '0'); }

    function resolveTrainerKey() {
        if (trainerSelect && trainerSelect.value) return String(trainerSelect.value);
        if (initialTrainerId) return String(initialTrainerId);
        return null;
    }

    function loadOffOptionsForTrainer() {
        const key = resolveTrainerKey();
        currentOffOptions = (key && trainerOffDaysByTrainer[key] ? trainerOffDaysByTrainer[key] : []).map((o) => ({
            ...o,
            value: normalizeDateKey(o.value),
        }));
        const allowed = new Set(currentOffOptions.map(o => o.value));
        selectedOffDates = selectedOffDates.map(normalizeDateKey).filter(d => allowed.has(d));
        renderOffDaysSelect();
        calculateSchedule();
    }

    function renderOffDaysSelect() {
        if (!rsRoot || !rsEmpty) return;

        if (!currentOffOptions.length) {
            rsRoot.classList.add('hidden');
            rsEmpty.classList.remove('hidden');
            if (rsInputs) rsInputs.innerHTML = '';
            selectedOffDates = [];
            return;
        }

        rsEmpty.classList.add('hidden');
        rsRoot.classList.remove('hidden');

        // chips
        const chips = selectedOffDates.map(value => {
            const opt = currentOffOptions.find(o => o.value === value);
            const label = opt ? opt.label : value;
            return `<div class="rs-multi__multi-value" data-value="${value}">
                <div class="rs-multi__multi-value__label">${label}</div>
                <button type="button" class="rs-multi__multi-value__remove" data-rs-remove="${value}" aria-label="Remove">×</button>
            </div>`;
        }).join('');

        if (rsValues) {
            rsValues.innerHTML = chips + (rsPlaceholder ? rsPlaceholder.outerHTML : '');
            const ph = rsValues.querySelector('[data-rs-placeholder]');
            if (ph) ph.style.display = selectedOffDates.length ? 'none' : '';
        }

        if (rsClear) {
            rsClear.classList.toggle('hidden', selectedOffDates.length === 0);
        }
        if (rsSep) {
            rsSep.style.display = selectedOffDates.length ? '' : 'none';
        }

        // hidden inputs
        if (rsInputs) {
            rsInputs.innerHTML = selectedOffDates.map(v =>
                `<input type="hidden" name="off_dates[]" value="${v}">`
            ).join('');
        }

        // menu options (not yet selected)
        const available = currentOffOptions.filter(o => !selectedOffDates.includes(o.value));
        if (rsMenuList) {
            if (!available.length) {
                rsMenuList.innerHTML = `<div class="rs-multi__menu-notice">${noOptionsMsg}</div>`;
            } else {
                rsMenuList.innerHTML = available.map(o =>
                    `<button type="button" class="rs-multi__option" data-rs-option="${o.value}">${o.label}</button>`
                ).join('');
            }
        }
    }

    function openRsMenu() {
        if (!rsMenu || rsRoot.classList.contains('hidden')) return;
        rsMenu.classList.remove('hidden');
        rsRoot.classList.add('is-focused');
    }

    function closeRsMenu() {
        if (!rsMenu) return;
        rsMenu.classList.add('hidden');
        rsRoot.classList.remove('is-focused');
    }

    function toggleOffDate(value) {
        value = normalizeDateKey(value);
        if (!value) return;
        if (selectedOffDates.includes(value)) {
            selectedOffDates = selectedOffDates.filter(d => d !== value);
        } else {
            selectedOffDates.push(value);
        }
        renderOffDaysSelect();
        calculateSchedule();
    }

    if (rsRoot) {
        rsRoot.querySelector('[data-rs-control]')?.addEventListener('click', function(e) {
            if (e.target.closest('[data-rs-remove]') || e.target.closest('[data-rs-clear]')) return;
            if (rsMenu?.classList.contains('hidden')) openRsMenu();
            else closeRsMenu();
        });

        rsRoot.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('[data-rs-remove]');
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                toggleOffDate(removeBtn.getAttribute('data-rs-remove'));
                return;
            }
            const optBtn = e.target.closest('[data-rs-option]');
            if (optBtn) {
                e.preventDefault();
                toggleOffDate(optBtn.getAttribute('data-rs-option'));
                openRsMenu();
            }
        });

        rsClear?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            selectedOffDates = [];
            renderOffDaysSelect();
            calculateSchedule();
            closeRsMenu();
        });

        document.addEventListener('click', function(e) {
            if (!rsRoot.contains(e.target)) closeRsMenu();
        });
    }

    if (trainerSelect) {
        trainerSelect.addEventListener('change', loadOffOptionsForTrainer);
    }

    function renderRestDaysCheckboxes() {
        const currentSelections = restDaysContainer.querySelectorAll('.rest-day-checkbox').length
            ? Array.from(restDaysContainer.querySelectorAll('.rest-day-checkbox:checked')).map(cb => cb.value)
            : preSelectedDays;

        restDaysContainer.innerHTML = '';
        daysOrder.forEach(dayValue => {
            const isChecked = currentSelections.includes(dayValue);
            const checkboxHtml = `
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition ${isChecked ? 'border-blue-500 bg-blue-50' : ''}">
                    <input class="rest-day-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                           type="checkbox" name="rest_days[]" value="${dayValue}" ${isChecked ? 'checked' : ''}>
                    <span class="text-sm font-medium text-gray-700 ${isChecked ? 'text-blue-700' : ''}">${daysMap[dayValue]}</span>
                </label>`;
            restDaysContainer.insertAdjacentHTML('beforeend', checkboxHtml);
        });

        restDaysContainer.querySelectorAll('.rest-day-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const label = this.closest('label');
                label.classList.toggle('border-blue-500', this.checked);
                label.classList.toggle('bg-blue-50', this.checked);
                calculateSchedule();
            });
        });
    }

    function projectSchedule(offSet) {
        const sessionDays = Math.max(1, parseInt(countDaysInput.value, 10) || 1);
        const restSelected = Array.from(restDaysContainer.querySelectorAll('.rest-day-checkbox:checked')).map(cb => cb.value);
        const start = new Date(startDateInput.value);
        const hours = start.getHours();
        const minutes = start.getMinutes();

        let current = new Date(start.getFullYear(), start.getMonth(), start.getDate());
        let teachingCount = 0;
        let offHits = 0;
        let overlapHits = 0;
        let lastTeachingDate = new Date(current);
        const maxIterations = Math.max(sessionDays * 30, 3650);
        let iterations = 0;

        while (teachingCount < sessionDays && iterations < maxIterations) {
            const key = toDateKey(current);
            const dayName = getDayName(current);
            const isRest = restSelected.includes(dayName);
            const isOff = offSet.has(key);

            if (isOff && isRest) {
                overlapHits++;
            } else if (isOff) {
                offHits++;
            } else if (isRest) {
                // weekly rest
            } else {
                teachingCount++;
                lastTeachingDate = new Date(current);
            }

            if (teachingCount >= sessionDays) break;
            current.setDate(current.getDate() + 1);
            iterations++;
        }

        const endDate = new Date(lastTeachingDate.getFullYear(), lastTeachingDate.getMonth(), lastTeachingDate.getDate(), hours, minutes);
        const startDay = new Date(start.getFullYear(), start.getMonth(), start.getDate());
        const calendarSpanDays = Math.round((endDate - startDay) / (1000 * 60 * 60 * 24)) + 1;

        return { endDate, hours, minutes, calendarSpanDays, offHits, overlapHits };
    }

    // يحاكي منطق CourseScheduleCalculator::calculate() في الخادم
    function calculateSchedule() {
        if (!startDateInput.value || !countDaysInput.value) {
            endDateDisplay.value = ''; endDateHidden.value = ''; totalDaysDisplay.value = '0';
            offDaysHint.textContent = '';
            return;
        }

        const normalizedOffs = selectedOffDates.map(normalizeDateKey).filter(Boolean);
        selectedOffDates = Array.from(new Set(normalizedOffs));
        const offDatesSet = new Set(selectedOffDates);

        const baseline = projectSchedule(new Set());
        const projected = projectSchedule(offDatesSet);

        const isoLocal = `${projected.endDate.getFullYear()}-${pad2(projected.endDate.getMonth() + 1)}-${pad2(projected.endDate.getDate())}T${pad2(projected.hours)}:${pad2(projected.minutes)}`;
        endDateHidden.value = isoLocal;
        endDateDisplay.value = projected.endDate.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        totalDaysDisplay.value = String(projected.calendarSpanDays);

        [endDateDisplay, totalDaysDisplay].forEach((el) => {
            if (!el) return;
            el.classList.add('ring-2', 'ring-teal-400');
            setTimeout(() => el.classList.remove('ring-2', 'ring-teal-400'), 450);
        });

        const extendedBy = projected.calendarSpanDays - baseline.calendarSpanDays;
        if (extendedBy > 0) {
            offDaysHint.textContent = `تم تمديد المدة المتوقعة بمقدار ${extendedBy} يوم بسبب أيام الإجازة المختارة ضمن الفترة.`;
            offDaysHint.className = 'text-xs text-teal-700 mt-2 font-semibold';
        } else if (projected.overlapHits > 0 && selectedOffDates.length > 0) {
            offDaysHint.textContent = `الأيام المختارة توافق أيام راحة أسبوعية بالفعل، لذلك لم تتغير المدة أو تاريخ الانتهاء. اختر يوماً ليس يوم راحة لترى التمديد.`;
            offDaysHint.className = 'text-xs text-amber-700 mt-2 font-semibold';
        } else if (selectedOffDates.length > 0 && projected.offHits === 0 && projected.overlapHits === 0) {
            offDaysHint.textContent = `أيام الإجازة المختارة خارج فترة الدورة المتوقعة، لذلك لم تؤثر على الحساب.`;
            offDaysHint.className = 'text-xs text-amber-700 mt-2 font-semibold';
        } else {
            offDaysHint.textContent = '';
            offDaysHint.className = 'text-xs text-gray-500 mt-2';
        }
    }

    window.__courseOffDaysApi = {
        getSelected: () => selectedOffDates.slice(),
        setSelected: (dates) => {
            selectedOffDates = (Array.isArray(dates) ? dates : []).map(normalizeDateKey).filter(Boolean);
            loadOffOptionsForTrainer();
        },
    };

    startDateInput.addEventListener('change', calculateSchedule);
    countDaysInput.addEventListener('input', calculateSchedule);

    renderRestDaysCheckboxes();
    loadOffOptionsForTrainer();
});
</script>

<style>
    #rest-days-container {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    #rest-days-container .form-check {
        background: white;
        padding: 10px 15px;
        border-radius: 6px;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    #rest-days-container .form-check:hover {
        border-color: #0d6efd;
    }

    #rest-days-container .form-check-input:checked~.form-check-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .bg-light {
        background-color: #e9ecef !important;
    }
</style>

                        <div class="flex justify-end gap-3 mt-8">
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Content & Description -->
                    <div class="tab-content" id="content">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-align-right text-blue-600"></i>
                            المحتوى والوصف
                        </h2>

                        <div class="space-y-6">
                            <!-- Description AR -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الوصف بالعربي <span class="text-red-600">*</span>
                                </label>
                                <textarea name="description_ar" id="description_ar" required rows="6"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="اكتب وصفاً تفصيلياً للدورة بالعربي...">{{ old('description_ar') }}</textarea>
                                @error('description_ar')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description EN -->
                            <div>
                                <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                    Description (English) <span class="text-red-600">*</span>
                                </label>
                                <textarea required name="description_en" id="description_en" rows="6" dir="ltr"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Write detailed course description in English...">{{ old('description_en') }}</textarea>
                                @error('description_en')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Educational Path (recorded only) -->
                    <div class="tab-content" id="educational-path">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-route text-amber-500"></i>
                            المسار التعليمي
                        </h2>
                        @include('dashboard.courses.partials.educational-path-builder')
                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Features & Requirements -->
                    <div class="tab-content" id="features">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-star text-blue-600"></i>
                            المميزات والمتطلبات
                        </h2>

                        <!-- Requirements -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-list-check text-green-600"></i>
                                المتطلبات <span class="text-red-600 text-sm">*</span>
                            </h3>

                            <div id="requirements-container" class="space-y-3 mb-4">
                                @if(old('requirements_ar'))
                                @foreach(old('requirements_ar') as $index => $req_ar)
                                <div class="flex gap-2 requirement-row">
                                    <input type="text" name="requirements_ar[]" value="{{ $req_ar }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="متطلب بالعربي">
                                    <input type="text" name="requirements_en[]" dir="ltr"
                                        value="{{ old('requirements_en')[$index] ?? '' }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Requirement in English">
                                    <button type="button"
                                        class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="flex gap-2 requirement-row">
                                    <input type="text" name="requirements_ar[]"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="متطلب بالعربي">
                                    <input type="text" name="requirements_en[]" dir="ltr"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Requirement in English">
                                    <button type="button"
                                        class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-requirement-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة متطلب جديد
                            </button>
                        </div>

                        <!-- Features -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i>
                                المميزات <span class="text-red-600 text-sm">*</span>
                            </h3>

                            <div id="features-container" class="space-y-3 mb-4">
                                @if(old('features_ar'))
                                @foreach(old('features_ar') as $index => $feat_ar)
                                <div class="flex gap-2 feature-row">
                                    <input type="text" name="features_ar[]" value="{{ $feat_ar }}"
                                        class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="ميزة بالعربي">
                                    <input type="text" name="features_en[]" dir="ltr"
                                        value="{{ old('features_en')[$index] ?? '' }}"
                                        class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Feature in English">
                                    <button type="button"
                                        class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="flex gap-2 feature-row">
                                    <input type="text" name="features_ar[]"
                                        class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="ميزة بالعربي">
                                    <input type="text" name="features_en[]" dir="ltr"
                                        class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Feature in English">
                                    <button type="button"
                                        class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-feature-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة ميزة جديدة
                            </button>
                        </div>

                        <!-- Suitable for (optional) -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 flex items-center gap-2">
                                <i class="fas fa-user-check text-indigo-600"></i>
                                مناسبة لمن
                                <span class="text-gray-400 text-sm font-normal">(اختياري)</span>
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">حدد الفئات أو الأشخاص الذين تناسبهم هذه الدورة</p>

                            <div id="suitable-for-container" class="space-y-3 mb-4">
                                @if(old('suitable_for_ar'))
                                @foreach(old('suitable_for_ar') as $index => $item_ar)
                                <div class="flex gap-2 suitable-for-row">
                                    <input type="text" name="suitable_for_ar[]" value="{{ $item_ar }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="مثال: المبتدئين في البرمجة">
                                    <input type="text" name="suitable_for_en[]" dir="ltr"
                                        value="{{ old('suitable_for_en')[$index] ?? '' }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="e.g. Beginners in programming">
                                    <button type="button"
                                        class="remove-suitable-for-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="flex gap-2 suitable-for-row">
                                    <input type="text" name="suitable_for_ar[]"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="مثال: المبتدئين في البرمجة">
                                    <input type="text" name="suitable_for_en[]" dir="ltr"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="e.g. Beginners in programming">
                                    <button type="button"
                                        class="remove-suitable-for-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-suitable-for-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة فئة جديدة
                            </button>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 4: Buttons & Images -->
                    <div class="tab-content" id="actions">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-link text-blue-600"></i>
                            الأزرار والصور
                        </h2>

                        <!-- Buttons -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                أزرار الإجراءات
                            </h3>

                            <div id="buttons-container" class="space-y-4 mb-4">
                                @if(old('buttons_text_ar'))
                                @foreach(old('buttons_text_ar') as $index => $btn_text)
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]" value="{{ $btn_text }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="اطلب الآن">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button
                                                Text (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                value="{{ old('buttons_text_en')[$index] ?? '' }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Order Now">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                value="{{ old('buttons_link')[$index] ?? '' }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://example.com">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#0b8f7f' }}"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                                                <input type="text" name="buttons_color_hex[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#0b8f7f' }}" dir="ltr"
                                                    class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    placeholder="#0b8f7f" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $needsLoginOld = (string) (old('buttons_needs_login')[$index] ?? '0') === '1';
                                    @endphp
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="buttons_needs_login[]" value="{{ $needsLoginOld ? '1' : '0' }}">
                                            <input type="checkbox" class="sr-only peer" {{ $needsLoginOld ? 'checked' : '' }}
                                                onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                            <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                                        </label>
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                                            <i class="fas fa-trash"></i>
                                            حذف الزر
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="اطلب الآن">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button
                                                Text (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Order Now">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://example.com">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]" value="#0b8f7f"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                                                <input type="text" name="buttons_color_hex[]" value="#0b8f7f" dir="ltr"
                                                    class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    placeholder="#0b8f7f" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="buttons_needs_login[]" value="0">
                                            <input type="checkbox" class="sr-only peer"
                                                onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                            <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                                        </label>
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                                            <i class="fas fa-trash"></i>
                                            حذف الزر
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-button-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة زر جديد
                            </button>
                        </div>

                        <!-- Images -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-image text-purple-600"></i>
                                الصور
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Main Image -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        الصورة الرئيسية <span class="text-red-600">*</span>
                                    </label>
                                    <div
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                        <input id="main_image_input" type="file" name="main_image" accept="image/*"
                                            required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">اضغط أو اسحب الصورة هنا</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB)</p>
                                    </div>
                                    @error('main_image')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <div id="main_preview_container" class="mt-3 hidden relative w-full h-56">
                                        <img id="main_image_preview"
                                            class="w-full h-full object-cover rounded-lg border" />
                                        <button type="button" onclick="removeMainImage()"
                                            class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 flex items-center justify-center rounded-full shadow hover:bg-red-700 transition">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Extra Images -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        صور إضافية
                                    </label>
                                    <div
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                        <input id="extra_images_input" type="file" name="images[]" accept="image/*"
                                            multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">يمكنك اختيار عدة صور</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB each)</p>
                                    </div>
                                    @error('images.*')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <div id="extra_images_preview" class="mt-3 flex flex-wrap gap-3"></div>
                                </div>
                            </div>

                            <!-- Optional Video -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    فيديو تعريفي <span class="text-gray-400 font-normal">(اختياري)</span>
                                </label>
                                <div
                                    class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                    <input id="video_input" type="file" name="video"
                                        accept="video/mp4,video/webm,video/quicktime,video/ogg,.mp4,.webm,.mov,.ogg"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <i class="fas fa-video text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">اضغط أو اسحب الفيديو هنا</p>
                                    <p class="text-xs text-gray-400 mt-1">MP4, WEBM, MOV (حد أقصى 50MB)</p>
                                </div>
                                @error('video')
                                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                                <div id="video_preview_container" class="mt-3 hidden relative">
                                    <video id="video_preview" controls class="w-full max-h-72 rounded-lg border bg-black"></video>
                                    <p id="video_file_name" class="text-xs text-gray-500 mt-1"></p>
                                    <button type="button" onclick="removeCourseVideo()"
                                        class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 flex items-center justify-center rounded-full shadow hover:bg-red-700 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 5: Settings -->
                    <div class="tab-content" id="settings">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-cog text-blue-600"></i>
                            الإعدادات النهائية
                        </h2>

                        <div class="space-y-6">
                            <!-- Status -->
                            <div class="border rounded-lg p-5 bg-gray-50">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    حالة الدورة <span class="text-red-600">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-green-300 bg-white rounded-lg cursor-pointer hover:bg-green-50 transition flex-1">
                                        <input type="radio" name="status" value="active" {{ old('status', 'active'
                                            )=='active' ? 'checked' : '' }} class="w-5 h-5 text-green-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                            <span class="font-medium text-green-700">نشط</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-gray-300 bg-white rounded-lg cursor-pointer hover:bg-gray-50 transition flex-1">
                                        <input type="radio" name="status" value="inactive" {{ old('status')=='inactive'
                                            ? 'checked' : '' }} class="w-5 h-5 text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-times-circle text-gray-600"></i>
                                            <span class="font-medium text-gray-700">غير نشط</span>
                                        </div>
                                    </label>
                                </div>
                                @error('status')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            @include('dashboard.courses.partials.day-exams-builder')

                            <!-- Summary Box -->
                            <div class="border-2 border-blue-200 rounded-lg p-6 bg-blue-50">
                                <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    ملخص الدورة
                                </h3>
                                <p class="text-sm text-blue-700">
                                    تأكد من مراجعة جميع البيانات قبل الحفظ. يمكنك العودة للتبويبات السابقة للتعديل.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="submit" id="course-save-btn"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg font-bold text-lg hover:bg-green-700 transition shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-save" data-save-icon></i>
                                <span data-save-label>حفظ الدورة</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
<script>
    window.__courseHasOldInput = {{ $errors->any() || old('name_ar') ? 'true' : 'false' }};
</script>
<script>
    (function() {
    'use strict';

    let currentTab = 0;
    const tabs = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-button');

    // ========== Tab Navigation ==========
    function showTab(index) {
        tabs.forEach((tab, i) => {
            tab.classList.toggle('active', i === index);
        });
        tabButtons.forEach((btn, i) => {
            btn.classList.toggle('active', i === index);
        });
        currentTab = index;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (window.__saveCourseDraft) window.__saveCourseDraft();
    }

    tabButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => showTab(index));
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('.next-tab')) {
            e.preventDefault();
            let next = currentTab + 1;
            while (next < tabs.length && tabButtons[next]?.classList.contains('hidden')) next++;
            if (next < tabs.length) showTab(next);
        }
        if (e.target.closest('.prev-tab')) {
            e.preventDefault();
            let prev = currentTab - 1;
            while (prev >= 0 && tabButtons[prev]?.classList.contains('hidden')) prev--;
            if (prev >= 0) showTab(prev);
        }
    });

    // ========== Reveal invalid required fields on hidden tabs ==========
    // Without this, an invalid required field on a hidden tab silently blocks
    // submission (the browser can't show its popup on a display:none element).
    (function setupValidationTabJump() {
        const courseForm = document.getElementById('courseForm');
        if (!courseForm) return;

        let handlingInvalid = false;
        courseForm.addEventListener('invalid', function (e) {
            if (handlingInvalid) return; // only act on the first invalid field
            handlingInvalid = true;

            const field = e.target;
            const pane = field.closest('.tab-content');
            if (pane) {
                const idx = Array.from(tabs).indexOf(pane);
                if (idx >= 0 && idx !== currentTab) showTab(idx);
            }

            setTimeout(() => {
                try { field.focus(); } catch (_) {}
                if (typeof field.reportValidity === 'function') field.reportValidity();
                handlingInvalid = false;
            }, 60);
        }, true); // capture: the invalid event does not bubble
    })();

    // ========== Translation Functions ==========
    async function translateText(text, sourceLang, targetLang) {
        if (!text || !text.trim()) return "";
        const cleanText = text.trim();
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(cleanText)}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return text;
            const data = await response.json();
            if (data && data[0] && Array.isArray(data[0])) {
                let translatedText = '';
                for (let i = 0; i < data[0].length; i++) {
                    if (data[0][i] && data[0][i][0]) translatedText += data[0][i][0];
                }
                return translatedText.trim() || text;
            }
            return text;
        } catch (error) {
            return text;
        }
    }
    window.translateText = translateText;

    function setupTranslation(sourceId, targetId, fromLang, toLang, delay = 1000) {
        const source = document.getElementById(sourceId);
        const target = document.getElementById(targetId);
        if (!source || !target) return;
        let timer = null;
        source.addEventListener('input', (e) => {
            const val = e.target.value;
            if (timer) clearTimeout(timer);
            if (!val.trim()) return;
            timer = setTimeout(async () => {
                const translated = await translateText(val, fromLang, toLang);
                if (translated && translated !== target.value) {
                    target.value = translated;
                }
            }, delay);
        });
    }

    function setupDynamicTranslation(containerId, rowClass, arName, enName) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.addEventListener('input', (e) => {
            const isAr = e.target.name === arName;
            const isEn = e.target.name === enName;
            if (!isAr && !isEn) return;
            const row = e.target.closest(rowClass);
            const target = row ? row.querySelector(`input[name="${isAr ? enName : arName}"]`) : null;
            if (!target) return;
            if (e.target.timer) clearTimeout(e.target.timer);
            const val = e.target.value.trim();
            if (!val) return;
            e.target.timer = setTimeout(async () => {
                const res = await translateText(val, isAr ? 'ar' : 'en', isAr ? 'en' : 'ar');
                if (res && res !== target.value) {
                    target.value = res;
                }
            }, 1000);
        });
    }

    // ========== Free / Price Toggle ==========
    function setupFreePriceToggle() {
        const toggle = document.getElementById('is_free_toggle');
        const wrap = document.getElementById('price_field_wrap');
        const priceInput = document.getElementById('price');
        if (!toggle || !wrap || !priceInput) return;

        let lastPaidPrice = '';
        const current = String(priceInput.value || '').trim();
        if (current !== '' && parseFloat(current) > 0) {
            lastPaidPrice = current;
        }

        function applyFreeState() {
            const isFree = toggle.checked;
            wrap.classList.toggle('hidden', isFree);
            if (isFree) {
                const cur = String(priceInput.value || '').trim();
                if (cur !== '' && parseFloat(cur) > 0) {
                    lastPaidPrice = cur;
                }
                priceInput.value = '0';
                priceInput.removeAttribute('required');
            } else {
                priceInput.setAttribute('required', 'required');
                if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                    priceInput.value = lastPaidPrice || '';
                }
            }
        }

        toggle.addEventListener('change', applyFreeState);
        applyFreeState();
    }

    function setupPrivateRequestsToggle() {
        const toggle = document.getElementById('allows_private_requests');
        const wrap = document.getElementById('private_price_wrap');
        const freeToggle = document.getElementById('is_private_free_toggle');
        const priceFieldWrap = document.getElementById('private_price_field_wrap');
        const priceInput = document.getElementById('private_course_price');
        if (!toggle || !wrap) return;

        let lastPaidPrivatePrice = '';
        if (priceInput) {
            const current = String(priceInput.value || '').trim();
            if (current !== '' && parseFloat(current) > 0) {
                lastPaidPrivatePrice = current;
            }
        }

        function applyFreeState() {
            if (!freeToggle || !priceInput || !priceFieldWrap) return;
            const isFree = freeToggle.checked;
            priceFieldWrap.classList.toggle('hidden', isFree);
            if (isFree) {
                const cur = String(priceInput.value || '').trim();
                if (cur !== '' && parseFloat(cur) > 0) {
                    lastPaidPrivatePrice = cur;
                }
                priceInput.value = '0';
                priceInput.removeAttribute('required');
            } else if (toggle.checked) {
                priceInput.setAttribute('required', 'required');
                if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                    priceInput.value = lastPaidPrivatePrice || '';
                }
            }
            if (typeof window.__renderPrivateTrainerProfitPreview === 'function') {
                window.__renderPrivateTrainerProfitPreview();
            }
        }

        function apply() {
            const on = toggle.checked;
            wrap.classList.toggle('hidden', !on);
            if (!on) {
                if (priceInput) priceInput.removeAttribute('required');
            } else {
                applyFreeState();
            }
            if (typeof window.__renderPrivateTrainerProfitPreview === 'function') {
                window.__renderPrivateTrainerProfitPreview();
            }
        }

        toggle.addEventListener('change', apply);
        if (freeToggle) {
            freeToggle.addEventListener('change', applyFreeState);
        }
        apply();
    }

    // ========== Location Type Toggle ==========
    function setupLocationTypeToggle() {
        const locationInputs = document.querySelectorAll('input[name="location_type"]');
        const onlineContainer = document.getElementById('online_link_container');
        const venueContainer = document.getElementById('venue_container');
        const onlineLink = document.getElementById('online_link');
        const venueName = document.getElementById('venue_name');
        const pathTabBtn = document.getElementById('path-tab-btn');
        const pathPane = document.getElementById('educational-path');
        const datesSection = document.getElementById('course_dates_section');
        const dateFields = ['start_date', 'end_date', 'last_date', 'count_days']
            .map((id) => document.getElementById(id))
            .filter(Boolean);

        function updateMeetingProviderFields() {
            const provider = document.querySelector('input[name="meeting_provider"]:checked')?.value || 'youtube';
            const youtubeHint = document.getElementById('youtube_provider_hint');
            const externalHint = document.getElementById('external_provider_hint');
            const isExternal = provider === 'external';
            youtubeHint?.classList.toggle('hidden', isExternal);
            externalHint?.classList.toggle('hidden', !isExternal);
            if (onlineLink) {
                onlineLink.setAttribute('required', 'required');
                onlineLink.placeholder = isExternal
                    ? 'https://meet.google.com/... أو https://zoom.us/j/...'
                    : 'https://www.youtube.com/live/... أو https://youtu.be/...';
            }
        }

        function updateLocationFields(type) {
            if (type === 'online') {
                onlineContainer?.classList.remove('hidden');
                venueContainer?.classList.add('hidden');
                venueName?.removeAttribute('required');
                updateMeetingProviderFields();
            } else if (type === 'on_site') {
                onlineContainer?.classList.add('hidden');
                venueContainer?.classList.remove('hidden');
                onlineLink?.removeAttribute('required');
                venueName?.setAttribute('required', 'required');
            } else {
                onlineContainer?.classList.add('hidden');
                venueContainer?.classList.add('hidden');
                onlineLink?.removeAttribute('required');
                venueName?.removeAttribute('required');
            }

            const isRecorded = type === 'recorded';
            if (datesSection) datesSection.classList.toggle('hidden', isRecorded);
            dateFields.forEach((el) => {
                if (isRecorded) {
                    el.removeAttribute('required');
                    el.disabled = true;
                } else {
                    el.setAttribute('required', 'required');
                    el.disabled = false;
                }
            });

            if (typeof window.setDayExamsSectionVisible === 'function') {
                window.setDayExamsSectionVisible(!isRecorded);
            } else {
                document.getElementById('day-exams-section')?.classList.toggle('hidden', isRecorded);
            }

            const showPath = isRecorded;
            if (pathTabBtn) pathTabBtn.classList.toggle('hidden', !showPath);
            if (pathPane && !showPath && pathPane.classList.contains('active')) {
                // fall back to content tab if path was open
                const contentBtn = document.querySelector('.tab-button[data-tab="content"]');
                if (contentBtn) contentBtn.click();
            }
        }

        locationInputs.forEach(input => {
            input.addEventListener('change', (e) => updateLocationFields(e.target.value));
            if (input.checked) updateLocationFields(input.value);
        });
        document.querySelectorAll('input[name="meeting_provider"]').forEach((input) => {
            input.addEventListener('change', updateMeetingProviderFields);
        });
    }

    // ========== Dynamic Add/Remove - النسخة المُحسنة ==========
    function setupDynamicRows(containerId, addBtnClass, removeBtnClass, rowClass, createRowFn) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // متغير للتأكد من عدم إضافة الـ listener أكثر من مرة
        if (container.dataset.initialized) return;
        container.dataset.initialized = 'true';

        // البحث عن زر الإضافة
        const addButton = document.querySelector(`.${addBtnClass}`);
        
        if (addButton && !addButton.dataset.hasListener) {
            addButton.dataset.hasListener = 'true';
            addButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                container.insertAdjacentHTML('beforeend', createRowFn());
            });
        }

        // حذف الصفوف
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest(`.${removeBtnClass}`);
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const row = removeBtn.closest(`.${rowClass}`);
                const allRows = container.querySelectorAll(`.${rowClass}`);
                if (row && allRows.length > 1) {
                    row.remove();
                }
            }
        });
    }

    // ========== Image Previews ==========
    function normalizeUploadFile(file, kind) {
        if (!file || !(file.size > 0)) return null;
        const name = String(file.name || (kind === 'video' ? 'video.mp4' : 'image.jpg'));
        const lower = name.toLowerCase();
        let type = String(file.type || '').toLowerCase();

        if (kind === 'image') {
            if (!type.startsWith('image/')) {
                if (lower.endsWith('.png')) type = 'image/png';
                else if (lower.endsWith('.webp')) type = 'image/webp';
                else if (lower.endsWith('.jpg') || lower.endsWith('.jpeg')) type = 'image/jpeg';
                else return null;
            }
            if (!['image/jpeg', 'image/png', 'image/webp', 'image/jpg'].includes(type)) {
                return null;
            }
            if (type === 'image/jpg') type = 'image/jpeg';
        } else if (kind === 'video') {
            if (!type.startsWith('video/') && type !== 'application/ogg') {
                if (lower.endsWith('.mp4')) type = 'video/mp4';
                else if (lower.endsWith('.webm')) type = 'video/webm';
                else if (lower.endsWith('.mov')) type = 'video/quicktime';
                else if (lower.endsWith('.ogg') || lower.endsWith('.ogv')) type = 'video/ogg';
                else return null;
            }
        }

        if (file.type === type && file instanceof File) return file;
        try {
            return new File([file], name, {
                type,
                lastModified: file.lastModified || Date.now(),
            });
        } catch (e) {
            return file;
        }
    }
    window.normalizeUploadFile = normalizeUploadFile;

    function setupImagePreviews() {
        const mainImageInput = document.getElementById('main_image_input');
        const mainPreviewContainer = document.getElementById('main_preview_container');
        const mainImagePreview = document.getElementById('main_image_preview');

        if (mainImageInput && mainPreviewContainer && mainImagePreview) {
            mainImageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        mainImagePreview.src = event.target.result;
                        mainPreviewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const extraImagesInput = document.getElementById('extra_images_input');
        const extraImagesPreview = document.getElementById('extra_images_preview');

        if (extraImagesInput && extraImagesPreview) {
            window.__extraImagesFiles = window.__extraImagesFiles || [];

            function renderExtraImagePreviews(files) {
                extraImagesPreview.innerHTML = '';
                Array.from(files || []).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const div = document.createElement('div');
                        div.className = 'relative w-24 h-24';
                        div.innerHTML = `
                            <img src="${event.target.result}" class="w-full h-full object-cover rounded-lg border" />
                            <button type="button" onclick="removeExtraImage(${index})"
                                class="absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        `;
                        extraImagesPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            window.__syncExtraImages = function(files, { merge = false } = {}) {
                let list = Array.from(files || []);
                if (merge) {
                    list = (window.__extraImagesFiles || []).concat(list);
                }
                const seen = new Set();
                list = list
                    .map((f) => normalizeUploadFile(f, 'image'))
                    .filter(Boolean)
                    .filter((f) => {
                        const key = `${f.name}|${f.size}|${f.lastModified}`;
                        if (seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    });
                const dt = new DataTransfer();
                list.forEach((f) => dt.items.add(f));
                extraImagesInput.files = dt.files;
                window.__extraImagesFiles = list;
                renderExtraImagePreviews(list);
            };

            extraImagesInput.addEventListener('change', (e) => {
                // Programmatic sync (remove/restore) already set the final FileList.
                if (extraImagesInput.dataset.syncing === '1') {
                    delete extraImagesInput.dataset.syncing;
                    window.__extraImagesFiles = Array.from(extraImagesInput.files);
                    renderExtraImagePreviews(window.__extraImagesFiles);
                    return;
                }
                // Native file picker replaces FileList — append to what we already keep.
                window.__syncExtraImages(Array.from(e.target.files || []), { merge: true });
            });
        }

        const videoInput = document.getElementById('video_input');
        const videoPreviewContainer = document.getElementById('video_preview_container');
        const videoPreview = document.getElementById('video_preview');
        const videoFileName = document.getElementById('video_file_name');

        if (videoInput && videoPreviewContainer && videoPreview) {
            videoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                if (videoPreview.dataset.objectUrl) {
                    URL.revokeObjectURL(videoPreview.dataset.objectUrl);
                }
                const url = URL.createObjectURL(file);
                videoPreview.dataset.objectUrl = url;
                videoPreview.src = url;
                if (videoFileName) videoFileName.textContent = file.name + ' (' + Math.round(file.size / 1024 / 1024 * 10) / 10 + ' MB)';
                videoPreviewContainer.classList.remove('hidden');
            });
        }
    }

    // ========== Color Picker Sync ==========
    function setupColorPickers() {
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('button-color-picker')) {
                const row = e.target.closest('.button-row');
                const hexInput = row.querySelector('.button-color-hex');
                if (hexInput) hexInput.value = e.target.value;
            }
        });
    }

    const createRequirementRow = () => `
        <div class="flex gap-2 requirement-row">
            <input type="text" name="requirements_ar[]"
                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="متطلب بالعربي">
            <input type="text" name="requirements_en[]" dir="ltr"
                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Requirement in English">
            <button type="button"
                class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    const createFeatureRow = () => `
        <div class="flex gap-2 feature-row">
            <input type="text" name="features_ar[]"
                class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="ميزة بالعربي">
            <input type="text" name="features_en[]" dir="ltr"
                class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Feature in English">
            <button type="button"
                class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    const createSuitableForRow = () => `
        <div class="flex gap-2 suitable-for-row">
            <input type="text" name="suitable_for_ar[]"
                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="مثال: المبتدئين في البرمجة">
            <input type="text" name="suitable_for_en[]" dir="ltr"
                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="e.g. Beginners in programming">
            <button type="button"
                class="remove-suitable-for-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    const createButtonRow = () => `
        <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="grid md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر (عربي)</label>
                    <input type="text" name="buttons_text_ar[]"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="اطلب الآن">
                </div>
                <div>
                    <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button Text (English)</label>
                    <input type="text" name="buttons_text_en[]" dir="ltr"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Order Now">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رابط الزر</label>
                    <input type="url" name="buttons_link[]" dir="ltr"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="https://example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                    <div class="flex gap-2">
                        <input type="color" name="buttons_color[]" value="#0b8f7f"
                            class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                        <input type="text" name="buttons_color_hex[]" value="#0b8f7f" dir="ltr"
                            class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="#0b8f7f" readonly>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="buttons_needs_login[]" value="0">
                    <input type="checkbox" class="sr-only peer"
                        onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                    <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                </label>
                <button type="button"
                    class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                    <i class="fas fa-trash"></i>
                    حذف الزر
                </button>
            </div>
        </div>
    `;

    // ========== Initialize ==========
    function initCourseFormHelpers() {
        setupTranslation('name_ar', 'name_en', 'ar', 'en', 800);
        setupTranslation('name_en', 'name_ar', 'en', 'ar', 800);
        setupTranslation('description_ar', 'description_en', 'ar', 'en', 1500);
        setupTranslation('description_en', 'description_ar', 'en', 'ar', 1500);
        setupDynamicTranslation('features-container', '.feature-row', 'features_ar[]', 'features_en[]');
        setupDynamicTranslation('requirements-container', '.requirement-row', 'requirements_ar[]', 'requirements_en[]');
        setupDynamicTranslation('suitable-for-container', '.suitable-for-row', 'suitable_for_ar[]', 'suitable_for_en[]');
        setupDynamicTranslation('buttons-container', '.button-row', 'buttons_text_ar[]', 'buttons_text_en[]');
        
        setupLocationTypeToggle();
        setupFreePriceToggle();
        setupPrivateRequestsToggle();
        if (typeof setupTrainerProfitPreview === 'function') {
            setupTrainerProfitPreview();
        }
        if (typeof setupPrivateTrainerProfitPreview === 'function') {
            setupPrivateTrainerProfitPreview();
        }
        
        setupDynamicRows('requirements-container', 'add-requirement-btn', 'remove-requirement-btn', 'requirement-row', createRequirementRow);
        setupDynamicRows('features-container', 'add-feature-btn', 'remove-feature-btn', 'feature-row', createFeatureRow);
        setupDynamicRows('suitable-for-container', 'add-suitable-for-btn', 'remove-suitable-for-btn', 'suitable-for-row', createSuitableForRow);
        setupDynamicRows('buttons-container', 'add-button-btn', 'remove-button-btn', 'button-row', createButtonRow);
        
        setupImagePreviews();
        setupColorPickers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCourseFormHelpers);
    } else {
        initCourseFormHelpers();
    }

    // Direct bind backup (form HTML is above this script)
    (function bindAddButtonNow() {
        const container = document.getElementById('buttons-container');
        const addBtn = document.querySelector('.add-button-btn');
        if (!container || !addBtn || addBtn.dataset.hasListener === 'true' || addBtn.dataset.courseBound === '1') return;
        addBtn.dataset.courseBound = '1';
        addBtn.dataset.hasListener = 'true';
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            container.insertAdjacentHTML('beforeend', createButtonRow());
        });
    })();

    // ========== Global Functions ==========
    window.removeMainImage = function() {
        const input = document.getElementById('main_image_input');
        const container = document.getElementById('main_preview_container');
        if (input) {
            input.value = '';
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (container) container.classList.add('hidden');
        if (typeof window.__saveCourseDraft === 'function') window.__saveCourseDraft();
    };

    window.removeExtraImage = function(index) {
        const input = document.getElementById('extra_images_input');
        if (!input) return;
        const current = window.__extraImagesFiles && window.__extraImagesFiles.length
            ? window.__extraImagesFiles
            : Array.from(input.files || []);
        const next = current.filter((_, i) => i !== index);
        if (typeof window.__syncExtraImages === 'function') {
            window.__syncExtraImages(next, { merge: false });
        } else {
            const dt = new DataTransfer();
            next.forEach((f) => dt.items.add(f));
            input.dataset.syncing = '1';
            input.files = dt.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (typeof window.__saveCourseDraft === 'function') window.__saveCourseDraft();
    };

    window.removeCourseVideo = function() {
        const input = document.getElementById('video_input');
        const container = document.getElementById('video_preview_container');
        const preview = document.getElementById('video_preview');
        const fileName = document.getElementById('video_file_name');
        if (input) {
            input.value = '';
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (preview) {
            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(preview.dataset.objectUrl);
                delete preview.dataset.objectUrl;
            }
            preview.removeAttribute('src');
            preview.load();
        }
        if (fileName) fileName.textContent = '';
        if (container) container.classList.add('hidden');
        if (typeof window.__saveCourseDraft === 'function') window.__saveCourseDraft();
    };

    // ========== Draft Autosave (localStorage + IndexedDB for media) ==========
    (function setupDraftPersistence() {
        const DRAFT_KEY = 'course_create_draft_v1';
        const MEDIA_DB = 'course_create_draft_db';
        const MEDIA_STORE = 'files';
        const MEDIA_KEY = 'course_create_media_v1';
        const form = document.getElementById('courseForm');
        if (!form) return;

        const val = (name) => {
            const el = form.querySelector(`[name="${name}"]`);
            return el ? el.value : '';
        };
        const radioVal = (name) => {
            const el = form.querySelector(`[name="${name}"]:checked`);
            return el ? el.value : '';
        };

        function openMediaDb() {
            return new Promise((resolve, reject) => {
                const req = indexedDB.open(MEDIA_DB, 1);
                req.onupgradeneeded = () => {
                    const db = req.result;
                    if (!db.objectStoreNames.contains(MEDIA_STORE)) {
                        db.createObjectStore(MEDIA_STORE);
                    }
                };
                req.onsuccess = () => resolve(req.result);
                req.onerror = () => reject(req.error);
            });
        }

        function idbPut(store, key, value) {
            return openMediaDb().then((db) => new Promise((resolve, reject) => {
                const tx = db.transaction(store, 'readwrite');
                tx.objectStore(store).put(value, key);
                tx.oncomplete = () => { db.close(); resolve(); };
                tx.onerror = () => { db.close(); reject(tx.error); };
            }));
        }

        function idbGet(store, key) {
            return openMediaDb().then((db) => new Promise((resolve, reject) => {
                const tx = db.transaction(store, 'readonly');
                const req = tx.objectStore(store).get(key);
                req.onsuccess = () => { db.close(); resolve(req.result || null); };
                req.onerror = () => { db.close(); reject(req.error); };
            }));
        }

        function idbDelete(store, key) {
            return openMediaDb().then((db) => new Promise((resolve, reject) => {
                const tx = db.transaction(store, 'readwrite');
                tx.objectStore(store).delete(key);
                tx.oncomplete = () => { db.close(); resolve(); };
                tx.onerror = () => { db.close(); reject(tx.error); };
            })).catch(() => {});
        }

        function assignFiles(input, files) {
            if (!input) return;
            const normalized = (files || []).map((f) => {
                if (!f) return null;
                if (input.id === 'video_input') return normalizeUploadFile(f, 'video');
                return normalizeUploadFile(f, 'image');
            }).filter(Boolean);
            if (input.id === 'extra_images_input' && typeof window.__syncExtraImages === 'function') {
                window.__syncExtraImages(normalized, { merge: false });
                return;
            }
            const dt = new DataTransfer();
            normalized.forEach((f) => dt.items.add(f));
            input.files = dt.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        async function restoreMediaDraft() {
            try {
                const media = await idbGet(MEDIA_STORE, MEDIA_KEY);
                if (!media) return;
                if (media.main) assignFiles(document.getElementById('main_image_input'), [media.main]);
                if (media.extras && media.extras.length) {
                    assignFiles(document.getElementById('extra_images_input'), media.extras);
                }
                if (media.video) assignFiles(document.getElementById('video_input'), [media.video]);
            } catch (e) { /* ignore */ }
        }

        async function saveMediaDraft({ allowEmpty = true } = {}) {
            try {
                const mainInput = document.getElementById('main_image_input');
                const extraInput = document.getElementById('extra_images_input');
                const videoInput = document.getElementById('video_input');
                const extras = (window.__extraImagesFiles && window.__extraImagesFiles.length)
                    ? window.__extraImagesFiles
                    : Array.from(extraInput?.files || []);
                const next = {
                    main: mainInput?.files?.[0] || null,
                    extras: extras.filter((f) => f && f.size > 0),
                    video: videoInput?.files?.[0] || null,
                };

                // Never wipe previously drafted media with an empty snapshot (e.g. after scrub on failed submit).
                if (!allowEmpty) {
                    const prev = await idbGet(MEDIA_STORE, MEDIA_KEY);
                    if (prev) {
                        if (!next.main && prev.main) next.main = prev.main;
                        if ((!next.extras || !next.extras.length) && prev.extras && prev.extras.length) {
                            next.extras = prev.extras;
                        }
                        if (!next.video && prev.video) next.video = prev.video;
                    }
                }

                await idbPut(MEDIA_STORE, MEDIA_KEY, next);
            } catch (e) { /* quota / private mode */ }
        }

        async function clearMediaDraft() {
            await idbDelete(MEDIA_STORE, MEDIA_KEY);
        }

        function collectDraft() {
            const data = {
                scalars: {},
                rest_days: [],
                off_dates: [],
                requirements: [],
                features: [],
                suitable_for: [],
                buttons: [],
                exam: {
                    has_exam: false,
                    required_exam_pass_count: '1',
                    day_exams: [],
                    pass_score: '',
                    duration: '',
                    questions: [],
                },
                activeTab: currentTab,
            };

            ['name_ar','name_en','price','private_course_price','course_category_id','counter',
             'start_date','end_date','last_date','count_days',
             'online_link','venue_name','venue_map_url','venue_details',
             'description_ar','description_en'].forEach(n => { data.scalars[n] = val(n); });

            data.scalars.location_type = radioVal('location_type');
            data.scalars.status = radioVal('status');
            // Checkbox (not the hidden 0 twin)
            data.scalars.allows_private_requests = form.querySelector('#allows_private_requests')?.checked ? '1' : '0';

            data.rest_days = Array.from(form.querySelectorAll('input[name="rest_days[]"]:checked')).map(cb => cb.value);
            data.off_dates = Array.from(form.querySelectorAll('input[name="off_dates[]"]')).map(inp => inp.value);
            data.levels = Array.from(form.querySelectorAll('input[name="levels[]"]:checked')).map(cb => cb.value);

            form.querySelectorAll('.requirement-row').forEach(row => {
                data.requirements.push({
                    ar: row.querySelector('input[name="requirements_ar[]"]')?.value || '',
                    en: row.querySelector('input[name="requirements_en[]"]')?.value || '',
                });
            });

            form.querySelectorAll('.feature-row').forEach(row => {
                data.features.push({
                    ar: row.querySelector('input[name="features_ar[]"]')?.value || '',
                    en: row.querySelector('input[name="features_en[]"]')?.value || '',
                });
            });

            form.querySelectorAll('.suitable-for-row').forEach(row => {
                data.suitable_for.push({
                    ar: row.querySelector('input[name="suitable_for_ar[]"]')?.value || '',
                    en: row.querySelector('input[name="suitable_for_en[]"]')?.value || '',
                });
            });

            form.querySelectorAll('.button-row').forEach(row => {
                data.buttons.push({
                    text_ar: row.querySelector('input[name="buttons_text_ar[]"]')?.value || '',
                    text_en: row.querySelector('input[name="buttons_text_en[]"]')?.value || '',
                    link: row.querySelector('input[name="buttons_link[]"]')?.value || '',
                    color: row.querySelector('input[name="buttons_color[]"]')?.value || '#0b8f7f',
                    needs_login: row.querySelector('input[name="buttons_needs_login[]"]')?.value || '0',
                });
            });

            // Day exams (final settings)
            if (typeof window.collectDayExamsDraft === 'function') {
                const dayExamDraft = window.collectDayExamsDraft();
                data.exam.has_exam = !!dayExamDraft.has_exam;
                data.exam.required_exam_pass_count = dayExamDraft.required_exam_pass_count || '1';
                data.exam.day_exams = dayExamDraft.day_exams || [];
            } else {
                const examToggle = form.querySelector('#has_exam_toggle');
                data.exam.has_exam = !!(examToggle && examToggle.checked);
                data.exam.required_exam_pass_count = val('required_exam_pass_count') || '1';
            }

            return data;
        }

        function saveDraft() {
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
            } catch (e) { /* storage full / disabled */ }
            saveMediaDraft();
        }

        let saveTimer = null;
        function debouncedSave() {
            if (saveTimer) clearTimeout(saveTimer);
            saveTimer = setTimeout(saveDraft, 400);
        }
        window.__saveCourseDraft = debouncedSave;

        function ensureRows(container, currentSelector, createFn, targetCount) {
            if (!container) return;
            let existing = container.querySelectorAll(currentSelector).length;
            while (existing < targetCount) {
                container.insertAdjacentHTML('beforeend', createFn());
                existing++;
            }
        }

        function restoreDraft(draft) {
            // Scalars
            Object.entries(draft.scalars || {}).forEach(([name, value]) => {
                if (name === 'allows_private_requests') {
                    const toggle = form.querySelector('#allows_private_requests');
                    if (toggle) {
                        toggle.checked = value === '1' || value === 1 || value === true;
                        toggle.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    return;
                }
                if (value === '' || value == null) {
                    // Still clear private price when explicitly empty after toggle off
                    if (name === 'private_course_price') {
                        const el = form.querySelector('#private_course_price');
                        if (el) el.value = '';
                    }
                    return;
                }
                if (name === 'location_type' || name === 'status') {
                    const radio = form.querySelector(`[name="${name}"][value="${value}"]`);
                    if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change', { bubbles: true })); }
                    return;
                }
                if (name === 'private_course_price') {
                    const el = form.querySelector('#private_course_price');
                    if (el) el.value = value;
                    return;
                }
                const el = form.querySelector(`[name="${name}"]`);
                if (el) el.value = value;
            });

            // Sync free / paid toggle with restored price (empty price = paid, not free)
            const priceEl = form.querySelector('#price');
            const freeToggle = form.querySelector('#is_free_toggle');
            if (priceEl && freeToggle) {
                const raw = String(priceEl.value || '').trim();
                const isFree = raw !== '' && Number.isFinite(parseFloat(raw)) && parseFloat(raw) <= 0;
                freeToggle.checked = isFree;
                freeToggle.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (typeof window.__renderPrivateTrainerProfitPreview === 'function') {
                window.__renderPrivateTrainerProfitPreview();
            }

            // Dates -> restore rest-day / off-day selections, then recompute predicted end date
            const startEl = form.querySelector('[name="start_date"]');
            const countDaysEl = form.querySelector('[name="count_days"]');
            if (startEl && startEl.value) {
                (draft.rest_days || []).forEach(day => {
                    const cb = form.querySelector(`input[name="rest_days[]"][value="${day}"]`);
                    if (cb) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                if (window.__courseOffDaysApi && Array.isArray(draft.off_dates)) {
                    window.__courseOffDaysApi.setSelected(draft.off_dates);
                }
                startEl.dispatchEvent(new Event('change', { bubbles: true }));
                if (countDaysEl) countDaysEl.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Course levels
            form.querySelectorAll('input[name="levels[]"]').forEach((cb) => {
                cb.checked = false;
            });
            (draft.levels || []).forEach((level) => {
                const cb = form.querySelector(`input[name="levels[]"][value="${level}"]`);
                if (cb) cb.checked = true;
            });

            // Requirements
            if ((draft.requirements || []).length) {
                const c = document.getElementById('requirements-container');
                ensureRows(c, '.requirement-row', createRequirementRow, draft.requirements.length);
                const rows = c.querySelectorAll('.requirement-row');
                draft.requirements.forEach((r, i) => {
                    if (!rows[i]) return;
                    const ar = rows[i].querySelector('input[name="requirements_ar[]"]');
                    const en = rows[i].querySelector('input[name="requirements_en[]"]');
                    if (ar) ar.value = r.ar;
                    if (en) en.value = r.en;
                });
            }

            // Features
            if ((draft.features || []).length) {
                const c = document.getElementById('features-container');
                ensureRows(c, '.feature-row', createFeatureRow, draft.features.length);
                const rows = c.querySelectorAll('.feature-row');
                draft.features.forEach((f, i) => {
                    if (!rows[i]) return;
                    const ar = rows[i].querySelector('input[name="features_ar[]"]');
                    const en = rows[i].querySelector('input[name="features_en[]"]');
                    if (ar) ar.value = f.ar;
                    if (en) en.value = f.en;
                });
            }

            // Suitable for
            if ((draft.suitable_for || []).length) {
                const c = document.getElementById('suitable-for-container');
                ensureRows(c, '.suitable-for-row', createSuitableForRow, draft.suitable_for.length);
                const rows = c.querySelectorAll('.suitable-for-row');
                draft.suitable_for.forEach((s, i) => {
                    if (!rows[i]) return;
                    const ar = rows[i].querySelector('input[name="suitable_for_ar[]"]');
                    const en = rows[i].querySelector('input[name="suitable_for_en[]"]');
                    if (ar) ar.value = s.ar;
                    if (en) en.value = s.en;
                });
            }

            // Buttons
            if ((draft.buttons || []).length) {
                const c = document.getElementById('buttons-container');
                ensureRows(c, '.button-row', createButtonRow, draft.buttons.length);
                const rows = c.querySelectorAll('.button-row');
                draft.buttons.forEach((b, i) => {
                    if (!rows[i]) return;
                    const set = (sel, v) => { const el = rows[i].querySelector(sel); if (el) el.value = v; };
                    set('input[name="buttons_text_ar[]"]', b.text_ar);
                    set('input[name="buttons_text_en[]"]', b.text_en);
                    set('input[name="buttons_link[]"]', b.link);
                    set('input[name="buttons_color[]"]', b.color);
                    set('input[name="buttons_color_hex[]"]', b.color);
                    const hidden = rows[i].querySelector('input[name="buttons_needs_login[]"]');
                    const checkbox = rows[i].querySelector('input[type="checkbox"].peer');
                    if (hidden) hidden.value = b.needs_login;
                    if (checkbox) checkbox.checked = b.needs_login === '1';
                });
            }

            // Day exams (final settings)
            const examData = draft.exam || {};
            if (typeof window.restoreDayExamsDraft === 'function') {
                window.restoreDayExamsDraft({
                    has_exam: !!examData.has_exam,
                    required_exam_pass_count: examData.required_exam_pass_count || '1',
                    day_exams: examData.day_exams || [],
                });
            } else if (examData.has_exam) {
                const toggle = form.querySelector('#has_exam_toggle');
                if (toggle && !toggle.checked) {
                    toggle.checked = true;
                    toggle.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (examData.required_exam_pass_count) {
                    const p = form.querySelector('[name="required_exam_pass_count"]');
                    if (p) p.value = examData.required_exam_pass_count;
                }
            }

            if (typeof draft.activeTab === 'number') showTab(draft.activeTab);
        }

        function showRestoredBanner(message) {
            if (document.getElementById('course-draft-restore-banner')) return;
            const bar = document.createElement('div');
            bar.id = 'course-draft-restore-banner';
            bar.className = 'flex items-center justify-between gap-3 m-4 p-3 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg';
            bar.innerHTML = `
                <span class="flex items-center gap-2"><i class="fas fa-clock-rotate-left"></i> ${message || 'تم استرجاع مسودة غير محفوظة.'}</span>
                <button type="button" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">
                    <i class="fas fa-trash ml-1"></i> مسح المسودة والبدء من جديد
                </button>`;
            bar.querySelector('button').addEventListener('click', async () => {
                try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
                await clearMediaDraft();
                window.location.reload();
            });
            form.parentNode.insertBefore(bar, form);
        }

        // Restore on load — always prefer the local draft (survives validation errors).
        // Draft is cleared ONLY after a successful create (clear_course_create_draft flash).
        window.__courseMediaReady = false;
        async function runInitialRestore() {
            let draft = null;
            try {
                const raw = localStorage.getItem(DRAFT_KEY);
                if (raw) draft = JSON.parse(raw);
            } catch (e) { /* corrupt */ }

            if (draft) {
                restoreDraft(draft);
                showRestoredBanner(
                    window.__courseHasOldInput
                        ? 'تم استرجاع مسودتك بعد خطأ الحفظ. راجع البيانات وأعد المحاولة.'
                        : 'تم استرجاع مسودة غير محفوظة.'
                );
            }
            await restoreMediaDraft();
            window.__courseMediaReady = true;
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => setTimeout(() => { runInitialRestore(); }, 0));
        } else {
            setTimeout(() => { runInitialRestore(); }, 0);
        }

        // Save on any change / typing
        form.addEventListener('input', debouncedSave);
        form.addEventListener('change', debouncedSave);

        function scrubExtraImagesForSubmit() {
            const input = document.getElementById('extra_images_input');
            if (!input) return;
            const raw = (window.__extraImagesFiles && window.__extraImagesFiles.length)
                ? window.__extraImagesFiles
                : Array.from(input.files || []);
            const valid = raw.map((f) => normalizeUploadFile(f, 'image')).filter(Boolean);
            if (typeof window.__syncExtraImages === 'function') {
                window.__syncExtraImages(valid, { merge: false });
            } else {
                const dt = new DataTransfer();
                valid.forEach((f) => dt.items.add(f));
                input.files = dt.files;
            }
            window.__extraImagesFiles = valid;
        }

        function scrubSingleFileInput(id, kind) {
            const input = document.getElementById(id);
            if (!input || !input.files || !input.files[0]) return;
            const normalized = normalizeUploadFile(input.files[0], kind || 'image');
            if (!normalized) {
                input.value = '';
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(normalized);
            input.files = dt.files;
        }

        // Persist full draft BEFORE scrubbing. Never clear here — only after successful create.
        let submitting = false;
        form.addEventListener('submit', (e) => {
            if (!window.__courseMediaReady) {
                e.preventDefault();
                runInitialRestore().then(() => form.requestSubmit());
                return;
            }
            if (submitting) {
                e.preventDefault();
                return;
            }

            // Snapshot complete form + media first (before any scrub can empty inputs).
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
            } catch (err) {}
            saveMediaDraft({ allowEmpty: true });

            scrubExtraImagesForSubmit();
            scrubSingleFileInput('main_image_input', 'image');
            scrubSingleFileInput('video_input', 'video');

            // Update media draft after scrub, but do not wipe previous files if scrub removed them.
            saveMediaDraft({ allowEmpty: false });

            submitting = true;
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-wait');
                const icon = btn.querySelector('[data-save-icon]');
                const label = btn.querySelector('[data-save-label]');
                if (icon) icon.className = 'fas fa-circle-notch fa-spin';
                if (label) label.textContent = 'جاري الحفظ…';
            }
        });
    })();
})();
</script>

@include('dashboard.courses.partials.trainer-profit-preview-script')

@endsection
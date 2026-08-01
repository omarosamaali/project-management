@extends('layouts.app')

@section('title', 'إعدادات الأكاديمية')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الأكاديمية" third="إعدادات الأكاديمية" />

    <div class="max-w-4xl mx-auto bg-white border shadow-md rounded-xl p-5">
        <form method="POST" action="{{ route('dashboard.academy.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-lg font-bold text-slate-800">هوية الأكاديمية</h2>
                <p class="text-sm text-slate-500 mt-1">هذه الصور ستُستخدم في شريط التنقل العام، وصورة بطاقة الهيرو، وصورة التصنيف الاحتياطية عند عدم رفع صورة للتصنيف.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                @include('dashboard.course-categories.partials.drag-image-input', [
                    'name' => 'academy_logo',
                    'label' => 'شعار الأكاديمية',
                    'existingUrl' => $academyLogoUrl,
                    'hint' => 'يظهر في نافبار الأكاديمية، ويُستخدم كصورة افتراضية لتصنيفات الدورات.',
                    'previewRounded' => false,
                ])

                @include('dashboard.course-categories.partials.drag-image-input', [
                    'name' => 'academy_hero_image',
                    'label' => 'صورة بطاقة الهيرو',
                    'existingUrl' => $academyHeroImageUrl,
                    'hint' => 'تظهر في بطاقة الهيرو الرئيسية داخل صفحة الأكاديمية.',
                    'previewRounded' => false,
                ])
            </div>

            <div class="border-t pt-5">
                <h2 class="text-lg font-bold text-slate-800">نسبة أرباح المحاضرين</h2>
                <p class="text-sm text-slate-500 mt-1">يتم احتساب ربح المحاضر من سعر الاشتراك الأساسي للدورة بدون رسوم الدفع.</p>

                <div class="mt-4 max-w-md">
                    <label for="trainer_profit_percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الربح لكل اشتراك</label>
                    <div class="relative">
                        <input
                            type="number"
                            name="trainer_profit_percentage"
                            id="trainer_profit_percentage"
                            min="0"
                            max="100"
                            step="0.01"
                            value="{{ old('trainer_profit_percentage', $trainerProfitPercentage) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">%</span>
                    </div>
                    @error('trainer_profit_percentage')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-2">مثال: إذا كانت الدورة بسعر 100 AED والنسبة 50%، يصبح ربح المحاضر 50 AED لكل اشتراك مكتمل.</p>
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <a href="{{ route('dashboard.academy.profits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">عرض الأرباح</a>
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ الإعدادات</button>
            </div>
        </form>
    </div>
</section>

@include('dashboard.course-categories.partials.drag-image-script')
@endsection

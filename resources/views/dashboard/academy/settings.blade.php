@extends('layouts.app')

@section('title', 'إعدادات الأكاديمية')

@section('content')
@include('dashboard.courses.partials.course-switch-styles')
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
                <h2 class="text-lg font-bold text-slate-800">نسب أرباح المحاضرين حسب نوع الدورة</h2>
                <p class="text-sm text-slate-500 mt-1">تُطبَّق النسبة على سعر الاشتراك الأساسي فقط (بدون رسوم الدفع)، وعلى الدورات المرتبطة بمحاضر. الدورات بدون محاضر تذهب بالكامل للمنصة.</p>

                <div class="mt-4 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">أونلاين (افتراضي 60%)</label>
                        <div class="relative">
                            <input type="number" name="trainer_profit_online" min="0" max="100" step="0.01"
                                value="{{ old('trainer_profit_online', $profitOnline) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">%</span>
                        </div>
                        @error('trainer_profit_online')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">مسجّلة (افتراضي 50%)</label>
                        <div class="relative">
                            <input type="number" name="trainer_profit_recorded" min="0" max="100" step="0.01"
                                value="{{ old('trainer_profit_recorded', $profitRecorded) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">%</span>
                        </div>
                        @error('trainer_profit_recorded')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">خاصة (افتراضي 70%)</label>
                        <div class="relative">
                            <input type="number" name="trainer_profit_private" min="0" max="100" step="0.01"
                                value="{{ old('trainer_profit_private', $profitPrivate) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">%</span>
                        </div>
                        @error('trainer_profit_private')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">حضوري (فارغ = 0% للمحاضر)</label>
                        <div class="relative">
                            <input type="number" name="trainer_profit_onsite" min="0" max="100" step="0.01"
                                value="{{ old('trainer_profit_onsite', $profitOnsite) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="اتركه فارغاً حتى التحديد">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">%</span>
                        </div>
                        @error('trainer_profit_onsite')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t pt-5">
                <h2 class="text-lg font-bold text-slate-800">حدود سحب أرباح المحاضرين (بالدرهم)</h2>
                <p class="text-sm text-slate-500 mt-1">الحد الأدنى والأقصى لمبلغ طلب السحب الواحد لكل محاضر.</p>

                <div class="mt-4 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأدنى للسحب (افتراضي 100)</label>
                        <input type="number" name="cashout_min" min="0" step="0.01"
                            value="{{ old('cashout_min', $cashoutMin) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        @error('cashout_min')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للسحب (افتراضي 10000)</label>
                        <input type="number" name="cashout_max" min="0" step="0.01"
                            value="{{ old('cashout_max', $cashoutMax) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        @error('cashout_max')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t pt-5">
                <h2 class="text-lg font-bold text-slate-800">اجتماعات الدورات الخاصة المضمّنة</h2>
                <p class="text-sm text-slate-500 mt-1">
                    عند التفعيل، تُنشأ غرفة اجتماع داخل المنصة للدورات الخاصة بدل لصق رابط يوتيوب/خارجي من المحاضر.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <input type="hidden" name="academy_embedded_meetings_enabled" value="0">
                    <label class="course-switch-field cursor-pointer !w-auto !min-h-0 !py-2 !px-3">
                        <span class="text-sm text-gray-700 truncate">تفعيل الاجتماعات المضمّنة</span>
                        <span class="course-switch">
                            <input type="checkbox" name="academy_embedded_meetings_enabled" value="1"
                                {{ old('academy_embedded_meetings_enabled', $embeddedMeetingsEnabled) ? 'checked' : '' }}>
                            <span class="course-switch-track" aria-hidden="true"></span>
                        </span>
                    </label>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 border border-amber-200">
                        <i class="fas fa-flask" aria-hidden="true"></i>
                        ميزة تجريبية — غير جاهزة للإنتاج
                    </span>
                </div>
                @unless($meetingApiConfigured)
                <p class="mt-2 text-xs text-slate-500">
                    عيّن <code class="bg-slate-100 px-1 rounded">MEETING_BASE_URL</code> و
                    <code class="bg-slate-100 px-1 rounded">MEETING_API_KEY</code> و
                    <code class="bg-slate-100 px-1 rounded">MEETING_API_SECRET</code> في ملف البيئة قبل الاستخدام.
                </p>
                @endunless
            </div>

            <div class="flex gap-2 justify-end">
                <a href="{{ route('dashboard.academy.payout-methods.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">طرق السحب</a>
                <a href="{{ route('dashboard.academy.profits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">عرض الأرباح</a>
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ الإعدادات</button>
            </div>
        </form>
    </div>
</section>

@include('dashboard.course-categories.partials.drag-image-script')
@endsection

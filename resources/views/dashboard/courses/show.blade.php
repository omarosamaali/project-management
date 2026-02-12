@extends('layouts.app')

@section('title', 'تفاصيل الدورة: ' . $course->name_ar)

@section('content')
<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات"
        third="تفاصيل الدورة" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

            <!-- Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $course->name_ar }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ $course->name_en }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $course->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                            {{ $course->status === 'active' ? 'نشط' : 'غير نشط' }}
                        </span>

                        <a href="{{ route('dashboard.courses.edit', $course->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Card: Basic Info -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-info-circle"></i>
                            المعلومات الأساسية
                        </h3>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">السعر</dt>
                                <dd class="font-medium flex items-center gap-1">
                                    {{ number_format($course->price) }}
                                    {{-- <span class="text-xs text-gray-500"> --}}
                                        <x-drhm-icon width="12" height="14" />
                                        {{--
                                    </span> --}}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">الحد الأقصى لعدد المشتركين</dt>
                                <dd class="font-medium">
                                    {{ $course->counter }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">نوع الخدمة</dt>
                                <dd class="font-medium">
                                    {{ $course->service ? $course->service->name_ar : 'غير محدد' }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">الحالة</dt>
                                <dd>
                                    <span
                                        class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $course->status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Card: Dates -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-calendar-alt"></i>
                            التواريخ
                        </h3>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">تاريخ البداية</dt>
                                <dd class="font-medium">{{ $course->start_date->format('Y-m-d h:i:A') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">تاريخ النهاية</dt>
                                <dd class="font-medium">{{ $course->end_date->format('Y-m-d h:i:A') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">آخر موعد للتسجيل</dt>
                                <dd class="font-medium">{{ $course->last_date->format('Y-m-d h:i:A') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Card: Location -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-map-marker-alt"></i>
                            مكان الحضور
                        </h3>

                        <div class="text-sm space-y-2">
                            <p>
                                <strong>النوع:</strong>
                                <span
                                    class="{{ $course->location_type === 'online' ? 'text-blue-600' : 'text-green-600' }}">
                                    {{ $course->location_type === 'online' ? 'أونلاين' : 'حضوري' }}
                                </span>
                            </p>

                            @if($course->location_type === 'online')
                            <p>
                                <strong>رابط الدورة:</strong>
                                @if($course->online_link)
                                <a href="{{ $course->online_link }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    {{ Str::limit($course->online_link, 40) }}
                                </a>
                                @else
                                <span class="text-gray-500">غير محدد</span>
                                @endif
                            </p>
                            @else
                            <p><strong>اسم المكان:</strong> {{ $course->venue_name ?: 'غير محدد' }}</p>
                            @if($course->venue_map_url)
                            <p>
                                <strong>رابط الخريطة:</strong>
                                <a href="{{ $course->venue_map_url }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    عرض على الخريطة
                                </a>
                            </p>
                            @endif
                            @if($course->venue_details)
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ $course->venue_details }}
                            </p>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-align-right text-blue-600"></i>
                        الوصف
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">بالعربية</h4>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! nl2br(e($course->description_ar)) !!}
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">بالإنجليزية</h4>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! nl2br(e($course->description_en)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements & Features -->
                <div class="mt-10 grid md:grid-cols-2 gap-8">
                    <!-- Requirements -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                            <i class="fas fa-list-check text-green-600"></i>
                            المتطلبات
                        </h3>
                        @if(!empty($course->requirements))
                        <ul class="space-y-2">
                            @foreach($course->requirements as $req)
                            <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="font-medium">{{ $req['ar'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $req['en'] ?? '' }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">لا توجد متطلبات محددة</p>
                        @endif
                    </div>

                    <!-- Features -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                            <i class="fas fa-star text-yellow-500"></i>
                            المميزات
                        </h3>
                        @if(!empty($course->features))
                        <ul class="space-y-2">
                            @foreach($course->features as $feat)
                            <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-check-circle text-blue-500 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="font-medium">{{ $feat['ar'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $feat['en'] ?? '' }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">لا توجد مميزات محددة</p>
                        @endif
                    </div>
                </div>

                <!-- Buttons & Actions -->
                @if(!empty($course->buttons))
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-link text-blue-600"></i>
                        أزرار الإجراءات
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($course->buttons as $button)
                        <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                            class="px-6 py-3 rounded-lg font-medium text-white transition"
                            style="background-color: {{ $button['color'] ?? '#3B82F6' }};">
                            {{ $button['text_ar'] ?? 'اضغط هنا' }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Main Image -->
                @if($course->main_image)
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-image text-purple-600"></i>
                        الصورة الرئيسية
                    </h3>
                    <img src="{{ Storage::url($course->main_image) }}" alt="{{ $course->name_ar }}"
                        class="w-full max-h-96 object-cover rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                </div>
                @endif
                <!-- الصور الإضافية -->
                @if(!empty($course->images) && is_array($course->images) && count($course->images) > 0)
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-3 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-images text-purple-600"></i>
                        الصور الإضافية للدورة
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($course->images as $image)
                        <div
                            class="relative group rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                            <img src="{{ Storage::url($image) }}" alt="صورة إضافية لـ {{ $course->name_ar }}"
                                class="w-full h-48 object-cover transition-transform group-hover:scale-105"
                                loading="lazy">
                            <!-- overlay خفيف عند الـ hover (اختياري) -->
                            <div
                                class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <a href="{{ Storage::url($image) }}" target="_blank"
                                    class="text-white text-sm font-medium">
                                    عرض كامل الحجم
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mt-10 text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed">
                    <i class="fas fa-images text-5xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">
                        لا توجد صور إضافية مضافة لهذه الدورة
                    </p>
                </div>
                @endif
                <!-- المشتركين -->
                <div class="my-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="pt-6 text-2xl font-bold flex items-center gap-3">
                            <i class="fas fa-users text-indigo-600 text-2xl"></i>
                            قائمة المشتركين في الدورة
                        </h2>
                        <span class="text-lg font-medium text-gray-600 dark:text-gray-400">
                            إجمالي: {{ $course->payments?->count() ?? 0 }} مشترك
                        </span>
                    </div>

                    @if($course->payments?->count() > 0)
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        الاسم
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        البريد الإلكتروني
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        تاريخ الاشتراك
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                    {{-- @if($course->students->first()->pivot->price_paid ?? false) --}}
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        المبلغ المدفوع
                                    </th>
                                    {{-- @endif --}}
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        إجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @if($course->payments->isNotEmpty())
                                @foreach($course->payments as $index => $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $payment->user->name ?? 'غير متوفر' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $payment->user->email ?? 'غير متوفر' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                            مدفوع
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-1">
                                        {{ number_format($payment->amount, 2) }}
                                        <x-drhm-icon width="12" height="14" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('dashboard.payment.invoice', $payment->id) }}" class="btn-style" title="الفاتورة">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    
                                        <form action="{{ route('dashboard.courses.toggle-attendance', $payment->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="mx-1 px-3 py-1 rounded-lg {{ $payment->is_attended ? 'bg-green-600' : 'bg-gray-400' }} text-white transition">
                                                <i class="fas {{ $payment->is_attended ? 'fa-user-check' : 'fa-user-clock' }}"></i>
                                                {{ $payment->is_attended ? 'تم الحضور' : 'تحضير' }}
                                            </button>
                                        </form>
                                    
                                        @if($payment->is_attended)
                                        <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                                            class="px-3 py-.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-certificate"></i> الشهادة
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div
                        class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                        <i class="fas fa-users-slash text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">
                            لا يوجد مشتركين بعد
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            لم يشترك أي مستخدم في هذه الدورة حتى الآن
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@extends('layouts.app')

@section('title', 'تعديل المشروع: ' . $specialRequest->title)

@section('content')
<div class="p-4 sm:p-6" dir="rtl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('dashboard.special-request.index') }}" class="hover:text-black">المشاريع</a>
        <span>/</span>
        <a href="{{ route('dashboard.special-request.show', $specialRequest) }}" class="hover:text-black truncate max-w-xs">{{ $specialRequest->title }}</a>
        <span>/</span>
        <span class="text-gray-800 font-bold">تعديل</span>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('dashboard.special-request.update', $specialRequest) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- العمود الرئيسي --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- البيانات الأساسية --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i> البيانات الأساسية
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">عنوان المشروع <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $specialRequest->title) }}" required
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">نوع المشروع <span class="text-red-500">*</span></label>
                            <select name="project_type" required
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                                @php $currentType = old('project_type', $specialRequest->project_type); @endphp
                                <option value="">-- اختر --</option>
                                <option value="web"        {{ $currentType == 'web' ? 'selected' : '' }}>موقع ويب</option>
                                <option value="mobile"     {{ $currentType == 'mobile' ? 'selected' : '' }}>تطبيق موبايل</option>
                                <option value="both"       {{ $currentType == 'both' ? 'selected' : '' }}>موقع + تطبيق</option>
                                <option value="logo"       {{ $currentType == 'logo' ? 'selected' : '' }}>شعار</option>
                                <option value="identity"   {{ $currentType == 'identity' ? 'selected' : '' }}>هوية مؤسسية</option>
                                <option value="digital"    {{ $currentType == 'digital' ? 'selected' : '' }}>تسويق إلكتروني</option>
                                <option value="management" {{ $currentType == 'management' ? 'selected' : '' }}>إدارة موقع</option>
                                <option value="social"     {{ $currentType == 'social' ? 'selected' : '' }}>إدارة سوشيال ميديا</option>
                                <option value="desktop"    {{ $currentType == 'desktop' ? 'selected' : '' }}>تطبيق سطح المكتب</option>
                                <option value="training"   {{ $currentType == 'training' ? 'selected' : '' }}>دورة تدريبية</option>
                                <option value="consulting" {{ $currentType == 'consulting' ? 'selected' : '' }}>استشارات تقنية</option>
                                <option value="other"      {{ $currentType == 'other' ? 'selected' : '' }}>أخرى</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ $currentType == $service->id ? 'selected' : '' }}>{{ $service->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">وصف المشروع <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="4" required
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description', $specialRequest->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">الميزات والوظائف الأساسية <span class="text-red-500">*</span></label>
                            <textarea name="core_features" rows="4" required
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('core_features', $specialRequest->core_features) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">مشاريع مرجعية / أمثلة</label>
                            <input type="text" name="examples" value="{{ old('examples', $specialRequest->examples) }}"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="https://example.com">
                        </div>
                    </div>
                </div>

                {{-- الميزانية والجدول الزمني --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fas fa-wallet text-green-500"></i> الميزانية والجدول الزمني
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">الميزانية التقديرية للعميل</label>
                            <input type="number" name="budget" value="{{ old('budget', $specialRequest->budget) }}" min="0" step="0.01"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">سعر المشروع الفعلي</label>
                            <input type="number" name="price" value="{{ old('price', $specialRequest->price) }}" min="0" step="0.01"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">نوع الدفع</label>
                            <select name="payment_type"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="single"       {{ old('payment_type', $specialRequest->payment_type) == 'single' ? 'selected' : '' }}>دفعة واحدة</option>
                                <option value="installments" {{ old('payment_type', $specialRequest->payment_type) == 'installments' ? 'selected' : '' }}>تقسيط</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">الموعد النهائي للتسليم</label>
                            <input type="date" name="deadline"
                                value="{{ old('deadline', $specialRequest->deadline?->format('Y-m-d')) }}"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- الصيانة --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fas fa-tools text-orange-500"></i> فترة الصيانة
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">مدة الصيانة</label>
                            <input type="number" name="maintenance_period" min="0"
                                value="{{ old('maintenance_period', $specialRequest->maintenance_period) }}"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-gray-300">الوحدة</label>
                            <select name="maintenance_unit"
                                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="days"   {{ old('maintenance_unit', $specialRequest->maintenance_unit) == 'days' ? 'selected' : '' }}>أيام</option>
                                <option value="months" {{ old('maintenance_unit', $specialRequest->maintenance_unit) == 'months' ? 'selected' : '' }}>أشهر</option>
                                <option value="years"  {{ old('maintenance_unit', $specialRequest->maintenance_unit) == 'years' ? 'selected' : '' }}>سنوات</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            {{-- العمود الجانبي --}}
            <div class="space-y-6">

                {{-- الحالة --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fas fa-flag text-purple-500"></i> حالة المشروع
                    </h3>
                    <select name="status" required
                        class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                        @php $currentStatus = old('status', $specialRequest->status); @endphp
                        <option value="pending"     {{ $currentStatus == 'pending' ? 'selected' : '' }}>جديد / قيد المراجعة</option>
                        <option value="in_review"   {{ $currentStatus == 'in_review' ? 'selected' : '' }}>قيد المراجعة النهائية</option>
                        <option value="in_progress" {{ $currentStatus == 'in_progress' ? 'selected' : '' }}>تحت الاجراء</option>
                        <option value="active"      {{ $currentStatus == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed"   {{ $currentStatus == 'completed' ? 'selected' : '' }}>منتهية</option>
                        <option value="canceled"    {{ $currentStatus == 'canceled' ? 'selected' : '' }}>ملغية</option>
                        <option value="معلقة"       {{ $currentStatus == 'معلقة' ? 'selected' : '' }}>معلقة</option>
                        <option value="بانتظار الدفع" {{ $currentStatus == 'بانتظار الدفع' ? 'selected' : '' }}>بانتظار الدفع</option>
                    </select>
                </div>

                {{-- تاريخ التسليم --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i> تسليم المشروع
                    </h3>
                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-gray-300">تاريخ التسليم الفعلي</label>
                        <input type="date" name="delivered_at"
                            value="{{ old('delivered_at', $specialRequest->delivered_at?->format('Y-m-d')) }}"
                            class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">يُحدَّد هذا عند تسليم المشروع للعميل</p>
                    </div>
                </div>

                {{-- معلومات سريعة --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">رقم الطلب</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $specialRequest->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">تاريخ الإنشاء</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $specialRequest->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">العميل</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $specialRequest->user?->name ?? '—' }}</span>
                    </div>
                </div>

                {{-- أزرار الحفظ --}}
                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-red-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('dashboard.special-request.show', $specialRequest) }}"
                        class="w-full bg-gray-100 dark:bg-gray-700 dark:text-white py-3 rounded-xl font-bold text-center text-gray-700 hover:bg-gray-200 transition">
                        إلغاء
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

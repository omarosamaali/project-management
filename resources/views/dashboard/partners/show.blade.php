@extends('layouts.app')

@section('title', 'عرض تفاصيل الشريك')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.partners.index') }}" second="الشركاء" third="عرض الشريك" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            {{-- الهيدر العلوي --}}
            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $partner->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            الملف الشخصي والبيانات المالية والصلاحيات
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.partners.edit', $partner->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1 shadow-md">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('dashboard.partners.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">

                {{-- القسم 1: بيانات التواصل والتعاقد الأساسية --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-id-card-alt text-blue-600"></i> بيانات التواصل والتعاقد
                    </h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                            <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                            <span class="text-lg font-bold text-blue-600">{{ $partner->email }}</span>
                        </div>
                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg border border-green-100">
                            <label class="block text-sm font-medium text-gray-500 mb-1">نسبة الشريك (%)</label>
                            <span class="text-2xl font-black text-green-600">{{ number_format($partner->percentage, 2)
                                }}%</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                            <label class="block text-sm font-medium text-gray-500 mb-1">حالة الموظف</label>
                            @if($partner->is_employee)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">موظف
                                بالشركة</span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">شريك
                                خارجي</span>
                            @endif
                        </div>
                        <div class="bg-yellow-50 dark:bg-gray-700 p-4 rounded-lg border border-yellow-100">
                            <label class="block text-sm font-medium text-gray-500 mb-1">إجمالي الطلبات</label>
                            <span class="text-xl font-bold text-yellow-700">{{ $partner->partner_requests_count ?? 0 }}
                                طلب</span>
                        </div>
                    </div>
                </div>

                {{-- القسم 2: تفاصيل الرواتب (تظهر دائماً أو إذا كان موظف حسب رغبتك) --}}
                <div class="pb-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-money-check-alt text-green-600"></i> البيانات المالية (الرواتب)
                    </h2>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border rounded-lg shadow-sm">
                            <label class="text-xs text-gray-400 block">الراتب الأساسي</label>
                            <span class="text-lg font-bold">{{ number_format($partner->salary_amount, 2) }} {{
                                $partner->salary_currency }}</span>
                        </div>
                        <div class="p-4 bg-white border rounded-lg shadow-sm">
                            <label class="text-xs text-gray-400 block">تاريخ التعيين</label>
                            <span class="text-lg font-bold">{{ $partner->hiring_date ? $partner->hiring_date : '---'
                                }}</span>
                        </div>
                        <div class="p-4 bg-white border rounded-lg shadow-sm">
                            <label class="text-xs text-gray-400 block">دورة الصرف</label>
                            <span class="text-lg font-bold">{{ $partner->salary_month }} / {{ $partner->salary_year
                                }}</span>
                        </div>
                    </div>

                    @if($partner->salary_attachment)
                    <div
                        class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex justify-between items-center">
                        <span class="text-sm text-blue-700 font-medium">مستند الراتب المرفق</span>
                        <a href="{{ asset('storage/' . $partner->salary_attachment) }}" target="_blank"
                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">عرض الملف</a>
                    </div>
                    @endif

                    @if($partner->salary_notes)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-400 block">ملاحظات الراتب:</label>
                        <p class="text-sm text-gray-700 italic">{{ $partner->salary_notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- القسم 3: الأنظمة والخدمات --}}
                <div class="grid md:grid-cols-2 gap-8 pb-6 border-b">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-boxes text-blue-600"></i> الأنظمة المرتبطة
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @forelse($partner->systems as $system)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">{{
                                $system->name_ar }}</span>
                            @empty
                            <span class="text-gray-400">لا توجد أنظمة.</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fa fa-users text-blue-600"></i> الخدمات المرتبطة
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @forelse($partner->services as $service)
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-medium">{{
                                $service->name_ar }}</span>
                            @empty
                            <span class="text-gray-400">لا توجد خدمات.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- القسم 4: جميع الصلاحيات الـ 8 (بما فيها شاشة الخدمات) --}}
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-red-600"></i> صلاحيات الوصول والإدارة
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @php
                        $all_perms = [
                        'can_view_projects' => 'الاطلاع على المشاريع',
                        'can_view_notes' => 'الاطلاع على الملاحظات',
                        'can_propose_quotes' => 'تقديم عرض سعر',
                        'can_enter_knowledge_bank' => 'إدخال بنك معلومات',
                        'apply_working_hours' => 'تطبيق أوقات العمل',
                        'can_request_meetings' => 'إمكانية طلب اجتماع',
                        'services_screen_available' => 'شاشة الخدمات',
                        'apply_salary_scale' => 'سلم الرواتب (26 يوم)'
                        ];
                        @endphp

                        @foreach($all_perms as $key => $label)
                        <div
                            class="flex flex-col items-center p-3 rounded-lg border {{ $partner->$key ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-100 opacity-60' }}">
                            <i
                                class="fas {{ $partner->$key ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }} mb-1 text-lg"></i>
                            <span class="text-[11px] font-bold text-center">{{ $label }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- القسم الأخير: التواريخ --}}
                <div class="border-t pt-6 grid grid-cols-2 gap-4 text-xs text-gray-400">
                    <div><i class="fas fa-clock"></i> تاريخ الإضافة: {{ $partner->created_at->format('Y-m-d H:i') }}
                    </div>
                    <div><i class="fas fa-sync"></i> آخر تحديث: {{ $partner->updated_at->format('Y-m-d H:i') }}</div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
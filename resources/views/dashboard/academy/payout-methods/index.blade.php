@extends('layouts.app')

@section('title', 'طرق سحب المحاضرين')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الأكاديمية" third="طرق السحب" />

    <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4">
            <div>
                <h1 class="text-lg font-bold text-slate-800">طرق سحب أرباح المحاضرين</h1>
                <p class="text-sm text-slate-500 mt-1">التحويل البنكي طريقة افتراضية للنظام، وبقية الطرق (محافظ / PayPal وغيرها) تُنشأ هنا بحقول مخصصة.</p>
            </div>
            <a href="{{ route('dashboard.academy.payout-methods.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg whitespace-nowrap"
                style="background:#0D2444;">
                <i class="fas fa-plus"></i> إضافة طريقة سحب
            </a>
        </div>

        @if(session('success'))
        <div class="mx-4 mb-3 p-3 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mx-4 mb-3 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">الطريقة</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">عدد الحقول</th>
                        <th class="px-4 py-3">ملفات دفع مرتبطة</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($methods as $method)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($method->imageUrl())
                                <img src="{{ $method->imageUrl() }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-gray-200 bg-slate-100">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-gray-200 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $method->name_ar }}</p>
                                    <p class="text-xs text-slate-500 dir-ltr text-left">{{ $method->name_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($method->is_system)
                            <span class="text-blue-700 bg-blue-50 px-2 py-1 rounded text-xs">نظامي (بنكي)</span>
                            @else
                            <span class="text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs">مخصص</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $method->is_system ? '—' : $method->fields()->count() }}</td>
                        <td class="px-4 py-3">{{ $method->payment_profiles_count }}</td>
                        <td class="px-4 py-3">
                            @if($method->is_active)
                            <span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs">نشط</span>
                            @else
                            <span class="text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs">موقوف</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('dashboard.academy.payout-methods.edit', $method) }}"
                                    class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white">تعديل</a>
                                @unless($method->is_system)
                                <form action="{{ route('dashboard.academy.payout-methods.destroy', $method) }}" method="POST"
                                    onsubmit="return confirm('حذف طريقة السحب هذه؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white">حذف</button>
                                </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">لا توجد طرق سحب بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

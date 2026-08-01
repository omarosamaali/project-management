@extends('layouts.app')

@section('title', 'أرباح دوراتي')

@section('content')
<section class="p-3 sm:p-5">
    @unless(auth()->user()->usesAcademyShell())
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الأكاديمية" third="أرباح دوراتي" />
    @else
    <div class="mb-5">
        <h1 class="text-xl font-extrabold text-slate-900">أرباح دوراتي</h1>
        <p class="text-sm text-slate-500 mt-1">الاحتساب من الاشتراكات المكتملة أو الفعالة فقط</p>
    </div>
    @endunless

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">عدد دوراتي</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['courses_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">إجمالي الاشتراكات</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['subscriptions_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">أرباحي</p>
                <p class="text-2xl font-black text-green-700 mt-2 inline-flex items-center gap-1.5">{{ number_format($summary['trainer_profit'], 2) }} <x-drhm-icon width="16" height="18" /></p>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">أرباح كل دورة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($courses as $course)
                <article class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 leading-snug">{{ $course['name_ar'] }}</h3>
                    @if($course['name_en'])
                    <p class="text-xs text-slate-400 mt-1 dir-ltr text-left">{{ $course['name_en'] }}</p>
                    @endif
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-500">الاشتراكات</p>
                            <p class="text-lg font-bold text-slate-800">{{ $course['subscriptions_count'] }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-slate-500">ربحي</p>
                            <p class="text-lg font-black text-green-700 inline-flex items-center gap-1">{{ number_format($course['trainer_profit'], 2) }} <x-drhm-icon width="12" height="14" /></p>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full text-center py-12 bg-white border border-dashed border-slate-200 rounded-2xl text-slate-400">
                    لا توجد أرباح لدوراتك حتى الآن.
                </div>
                @endforelse
            </div>
            @if(method_exists($courses, 'hasPages') && $courses->hasPages())
            <div class="ac-pagination mt-6">{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection

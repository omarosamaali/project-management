@extends('layouts.app')

@section('title', __('messages.private_requests_my_courses'))

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="{{ __('messages.private_requests_my_courses') }}" />

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-bold text-slate-900">{{ __('messages.private_requests_my_courses') }}</h1>
                <p class="text-sm text-slate-500">طلبات الدورات الخاصة على دوراتك بدون محاضر معيّن</p>
            </div>
            <a href="{{ route('dashboard.academy.private-requests.admin-index') }}" class="text-sm text-pink-600 font-bold">كل الطلبات</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right min-w-[720px]">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">المتدرب</th>
                        <th class="px-4 py-3">الدورة</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3 text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr class="border-t hover:bg-slate-50/80">
                        <td class="px-4 py-3">{{ $req->trainee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $req->sourceCourse?->name_ar ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs bg-amber-50 text-amber-800">{{ $req->statusLabel() }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('private-requests.show', $req) }}" class="text-pink-600 font-bold hover:underline">معالجة</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">لا توجد طلبات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="p-4">{{ $requests->links() }}</div>
        @endif
    </div>
</section>
@endsection

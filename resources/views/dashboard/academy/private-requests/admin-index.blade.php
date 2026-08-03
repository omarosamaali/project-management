@extends('layouts.app')

@section('title', 'طلبات الدورات الخاصة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="طلبات الدورات الخاصة" />

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('dashboard.academy.private-requests.admin-index') }}"
            class="px-3 py-1.5 rounded-lg text-sm font-bold {{ $status === '' ? 'bg-pink-600 text-white' : 'bg-white border text-gray-700' }}">الكل</a>
        @foreach($statusCounts as $st => $count)
        <a href="{{ route('dashboard.academy.private-requests.admin-index', ['status' => $st]) }}"
            class="px-3 py-1.5 rounded-lg text-sm font-bold {{ $status === $st ? 'bg-pink-600 text-white' : 'bg-white border text-gray-700' }}">
            {{ __('messages.private_request_status_'.$st) !== 'messages.private_request_status_'.$st ? __('messages.private_request_status_'.$st) : $st }}
            ({{ $count }})
        </a>
        @endforeach
        <a href="{{ route('dashboard.academy.private-requests.admin-unassigned') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold bg-white border text-gray-700">{{ __('messages.private_requests_my_courses') }}</a>
        <a href="{{ route('dashboard.academy.private-refunds.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold bg-white border text-gray-700">الاستردادات</a>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right min-w-[860px]">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">المتدرب</th>
                        <th class="px-4 py-3">الدورة</th>
                        <th class="px-4 py-3">المحاضر</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3 text-center">تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr class="border-t hover:bg-slate-50/80">
                        <td class="px-4 py-3 text-gray-500">{{ $req->id }}</td>
                        <td class="px-4 py-3">{{ $req->trainee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $req->sourceCourse?->name_ar ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $req->trainer?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs bg-pink-50 text-pink-700">{{ $req->statusLabel() }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('private-requests.show', $req) }}" class="text-pink-600 font-bold hover:underline">عرض</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">لا توجد طلبات</td></tr>
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

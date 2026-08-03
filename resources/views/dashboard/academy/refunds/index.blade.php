@extends('layouts.app')

@section('title', 'استردادات الدورات الخاصة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="استردادات الدورات الخاصة" />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach(['' => 'الكل', 'required' => 'بانتظار المعالجة', 'pending_trainee_confirm' => 'بانتظار المتدرب', 'refunded' => 'مكتمل'] as $key => $label)
        <a href="{{ route('dashboard.academy.private-refunds.index', $key !== '' ? ['status' => $key] : []) }}"
            class="px-3 py-1.5 rounded-lg text-sm font-bold {{ ($status ?? '') === $key ? 'bg-pink-600 text-white' : 'bg-white border text-gray-700' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right min-w-[900px]">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">المتدرب</th>
                        <th class="px-4 py-3">الدورة</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $refund)
                    <tr class="border-t hover:bg-slate-50/80 align-top">
                        <td class="px-4 py-3">{{ $refund->trainee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $refund->request?->sourceCourse?->name_ar ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1" dir="ltr">
                                <x-drhm-icon width="12" height="14" />
                                {{ number_format((float) $refund->amount, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs bg-pink-50 text-pink-700">
                                {{ __('messages.private_refund_status_'.$refund->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($refund->status === \App\Models\PrivateCourseRefund::STATUS_REQUIRED)
                            <form method="POST" action="{{ route('dashboard.academy.private-refunds.upload-screenshot', $refund) }}" enctype="multipart/form-data" class="space-y-2 max-w-xs">
                                @csrf
                                <input type="file" name="screenshot" accept="image/*" required class="text-xs w-full">
                                <textarea name="admin_note" rows="2" class="w-full rounded border text-xs px-2 py-1" placeholder="ملاحظة"></textarea>
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-pink-600 text-white text-xs font-bold w-full">
                                    {{ __('messages.private_refund_upload_proof') }}
                                </button>
                            </form>
                            @elseif($refund->screenshotUrl())
                            <a href="{{ $refund->screenshotUrl() }}" target="_blank" class="text-pink-600 text-xs font-bold hover:underline">عرض الإثبات</a>
                            @else
                            —
                            @endif
                            @if($refund->request)
                            <div class="mt-2">
                                <a href="{{ route('private-requests.show', $refund->request) }}" class="text-xs text-gray-500 hover:underline">الطلب</a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">لا توجد استردادات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($refunds->hasPages())
        <div class="p-4">{{ $refunds->links() }}</div>
        @endif
    </div>
</section>
@endsection

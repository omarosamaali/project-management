@extends('layouts.app')

@section('title', 'إضافة طريقة سحب')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="طرق السحب" third="إضافة" />

    <div class="max-w-3xl mx-auto bg-white border shadow-md rounded-xl p-5">
        @if ($errors->any())
        <div class="mb-4 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
            <p class="font-bold mb-1">تعذر الحفظ. راجع الأخطاء التالية:</p>
            <ul class="list-disc pe-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('dashboard.academy.payout-methods.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('dashboard.academy.payout-methods.partials.form-fields', ['method' => null])
            <div class="flex gap-2 justify-end">
                <a href="{{ route('dashboard.academy.payout-methods.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">إلغاء</a>
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ الطريقة</button>
            </div>
        </form>
    </div>
</section>

@include('dashboard.academy.payout-methods.partials.fields-script')
@include('dashboard.course-categories.partials.drag-image-script')
@endsection

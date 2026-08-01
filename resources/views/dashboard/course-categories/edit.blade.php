@extends('layouts.app')

@section('title', 'تعديل تصنيف دورة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.course-categories.index') }}" second="تصنيفات الدورات" third="تعديل" />

    @if(session('success'))
    <div class="max-w-3xl mx-auto mb-4 p-3 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="max-w-3xl mx-auto mb-4 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="max-w-3xl mx-auto bg-white border shadow-md rounded-xl p-5">
        <h2 class="text-lg font-bold text-slate-800 mb-4">بيانات التصنيف</h2>
        <form method="POST" action="{{ route('dashboard.course-categories.update', $category) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            @include('dashboard.course-categories.partials.form-fields', ['category' => $category])
            <div class="flex gap-2 justify-end">
                <a href="{{ route('dashboard.course-categories.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">رجوع</a>
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</section>

@include('dashboard.course-categories.partials.translate-script')
@include('dashboard.course-categories.partials.drag-image-script')
@endsection

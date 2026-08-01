@extends('layouts.app')

@section('title', 'إضافة تصنيف دورة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.course-categories.index') }}" second="تصنيفات الدورات" third="إضافة" />

    <div class="max-w-3xl mx-auto bg-white border shadow-md rounded-xl p-5">
        <form method="POST" action="{{ route('dashboard.course-categories.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('dashboard.course-categories.partials.form-fields')
            <div class="flex gap-2 justify-end">
                <a href="{{ route('dashboard.course-categories.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">إلغاء</a>
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ التصنيف</button>
            </div>
        </form>
    </div>
</section>

@include('dashboard.course-categories.partials.translate-script')
@include('dashboard.course-categories.partials.drag-image-script')
@endsection

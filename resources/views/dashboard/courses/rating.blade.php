@extends('layouts.app')

@section('title', 'تقييم الدورة')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-xl text-center py-16">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
            <i class="fas fa-star text-amber-500 text-3xl"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">تقييم الدورة</h1>
        <p class="text-gray-500 text-sm mb-1">{{ $course->name_ar }}</p>
        <p class="text-amber-600 text-xs font-medium mb-6">يجب إكمال التقييم قبل استخراج الشهادة</p>
        <button type="button" onclick="openRatingModal()"
            class="inline-flex items-center gap-2 px-8 py-3 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 transition text-lg">
            <i class="fas fa-star"></i> ابدأ التقييم
        </button>
    </div>
</section>

@include('dashboard.courses.partials.rating-modal', [
    'ratingCourse' => $course,
    'ratingPayment' => $payment,
    'ratingQuestions' => $questions,
    'ratingAutoOpen' => true,
])
@endsection

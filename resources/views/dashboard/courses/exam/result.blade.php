@extends('layouts.app')

@section('title', 'نتيجة الاختبار')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-lg">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden text-center">
            <div class="p-8 {{ $attempt->passed ? 'bg-green-50' : 'bg-red-50' }}">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center {{ $attempt->passed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <i class="fas {{ $attempt->passed ? 'fa-check' : 'fa-times' }} text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold {{ $attempt->passed ? 'text-green-800' : 'text-red-800' }}">
                    {{ $attempt->passed ? 'مبروك! لقد اجتزت الاختبار' : 'للأسف لم تجتز الاختبار' }}
                </h1>
                <p class="text-gray-600 mt-2">{{ $course->name_ar }}</p>
                <p class="text-gray-500 text-sm mt-1">{{ $dayExam->displayTitle() }} — اليوم {{ $dayExam->day_index }}</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">درجتك</span>
                    <span class="font-bold text-lg">{{ $attempt->score }} / {{ $totalQuestions }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">المطلوب للنجاح</span>
                    <span class="font-bold">{{ $dayExam->pass_score }} إجابات صحيحة</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">الاختبارات المجتازة</span>
                    <span class="font-bold">{{ $passedCount }} / {{ $requiredPass }}</span>
                </div>

                @if($canCertificate)
                <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                    class="block w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-certificate ml-2"></i>
                    استخراج الشهادة
                </a>
                @elseif($needsRating)
                <button type="button" onclick="openRatingModal()"
                    class="block w-full px-6 py-3 bg-amber-500 text-white rounded-lg font-bold hover:bg-amber-600 transition">
                    <i class="fas fa-star ml-2"></i>
                    أكمل التقييم للحصول على الشهادة
                </button>
                <p class="text-xs text-amber-700 text-center mt-1">يجب إكمال تقييم الدورة قبل استخراج الشهادة</p>
                @elseif(!$course->areAllDayExamsFinished())
                <p class="text-sm text-gray-500">انتظر انتهاء باقي اختبارات الدورة ثم أكمل التقييم لاستخراج الشهادة.</p>
                @elseif(!$attempt->passed)
                <p class="text-sm text-gray-500">لا يمكن إعادة هذا الاختبار. يلزم اجتياز {{ $requiredPass }} اختبار(ات) للحصول على الشهادة.</p>
                @else
                <p class="text-sm text-gray-500">أكمل باقي المتطلبات لاستخراج الشهادة.</p>
                @endif

                <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                    class="block w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    العودة لتفاصيل الدورة
                </a>
            </div>
        </div>
    </div>
</section>

@if($needsRating)
@include('dashboard.courses.partials.rating-modal', [
    'ratingCourse' => $course,
    'ratingPayment' => $payment,
    'ratingQuestions' => config("course_rating.{$course->location_type}", []),
    'ratingAutoOpen' => true,
])
@endif
@endsection

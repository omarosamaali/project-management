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
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">درجتك</span>
                    <span class="font-bold text-lg">{{ $attempt->score }} / {{ $totalQuestions }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">المطلوب للنجاح</span>
                    <span class="font-bold">{{ $course->exam_pass_score }} إجابات صحيحة</span>
                </div>

                @if($attempt->passed)
                <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                    class="block w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-certificate ml-2"></i>
                    استخراج الشهادة
                </a>
                @else
                <p class="text-sm text-gray-500">لا يمكن إعادة الاختبار. تواصل مع الإدارة إن لزم الأمر.</p>
                @endif

                <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                    class="block w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    العودة لتفاصيل الدورة
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

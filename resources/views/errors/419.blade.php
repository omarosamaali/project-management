@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-clock text-yellow-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-3">انتهت صلاحية الجلسة</h1>
        <p class="text-gray-600 mb-6 leading-relaxed">
            انتهت صلاحية الصفحة بسبب عدم النشاط لفترة طويلة. يرجى الرجوع والمحاولة مرة أخرى.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() ?: url('/') }}"
                class="px-6 py-3 bg-black text-white rounded-lg font-bold hover:bg-gray-800 transition-all">
                <i class="fas fa-arrow-right ml-2"></i>
                الرجوع والمحاولة مرة أخرى
            </a>
            <a href="{{ route('login') }}"
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition-all">
                تسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection

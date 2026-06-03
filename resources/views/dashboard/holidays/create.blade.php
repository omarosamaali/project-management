@extends('layouts.app')
@section('title', 'إضافة عطلة')
@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-2xl bg-white dark:bg-gray-800 p-6 shadow-xl rounded-xl">
        <h2 class="text-2xl font-bold mb-6 border-b pb-4 dark:border-gray-600">إضافة عطلة</h2>
        <form action="{{ route('dashboard.holidays.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('dashboard.holidays._form')
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                حفظ وإرسال الإشعارات
            </button>
        </form>
    </div>
</section>
@endsection

@extends('layouts.app')
@section('title', 'تعديل عطلة')
@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-2xl bg-white dark:bg-gray-800 p-6 shadow-xl rounded-xl">
        <h2 class="text-2xl font-bold mb-6 border-b pb-4 dark:border-gray-600">تعديل عطلة</h2>
        <form action="{{ route('dashboard.holidays.update', $holiday) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @include('dashboard.holidays._form', ['holiday' => $holiday])
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
</section>
@endsection

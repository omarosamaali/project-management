@extends('layouts.app')

@section('content')
<section class="p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.sessions.index') }}" second="الاجتماعات" />
    <div class="flex justify-between items-center mb-6 text-right" dir="rtl">
        <h1 class="text-2xl font-bold dark:text-white">إدارة الاجتماعات</h1>
        <a href="{{ route('dashboard.sessions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            طلب اجتماع جديد
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
        <table class="w-full text-right" dir="rtl">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="p-4">العنوان</th>
                    <th class="p-4">صاحب الطلب</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4">العمليات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr class="border-t dark:border-gray-700">
                    <td class="p-4">{{ $session->title }}</td>
                    <td class="p-4">{{ $session->user->name }}</td>
                    <td class="p-4">
                        <span
                            class="px-2 py-1 rounded text-xs {{ $session->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ $session->status_name }}
                        </span>
                    </td>
                    <td class="p-4">
                        <a href="{{ route('dashboard.sessions.show', $session->id) }}" class="text-blue-600">عرض</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
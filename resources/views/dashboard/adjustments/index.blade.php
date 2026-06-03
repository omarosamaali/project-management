@extends('layouts.app')
@section('title', !empty($isEmployeeView) ? 'خصوماتي ومكافآتي' : 'الخصومات والمكافآت')
@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb
        first="الرئيسية"
        link="{{ route('dashboard.adjustments.index') }}"
        :second="!empty($isEmployeeView) ? 'خصوماتي ومكافآتي' : 'الخصومات والمكافآت'" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between p-4 gap-3">
                @if(empty($isEmployeeView))
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.adjustments.index') }}" method="GET" class="flex items-center">
                        <div class="relative w-full">
                            <input value="{{ request()->search }}" type="text" name="search"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="بحث باسم الموظف...">
                        </div>
                    </form>
                </div>
                <a href="{{ route('dashboard.adjustments.create') }}"
                    class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2 whitespace-nowrap">
                    إضافة (خصم/مكافأة)
                </a>
                @else
                <p class="w-full text-sm text-gray-600 dark:text-gray-300">
                    عرض خصوماتك ومكافآتك فقط — للاطلاع دون تعديل أو حذف.
                </p>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            @if(empty($isEmployeeView))
                            <th class="px-4 py-3">الموظف</th>
                            @endif
                            <th class="px-4 py-3">النوع</th>
                            <th class="px-4 py-3">المبلغ</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adj)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            @if(empty($isEmployeeView))
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $adj->user->name }}</td>
                            @endif
                            <td class="px-4 py-3">
                                <span class="{{ $adj->type == 'bonus' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-2 py-1 rounded font-bold">
                                    {{ $adj->type == 'bonus' ? 'مكافأة' : 'خصم' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold {{ $adj->type == 'bonus' ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($adj->amount, 2) }}
                            </td>
                            <td class="px-4 py-3">{{ $adj->date?->format('Y-m-d') ?? $adj->date }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $adj->notes ?: '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ empty($isEmployeeView) ? 5 : 4 }}" class="text-center px-4 py-8 text-gray-500">
                                {{ !empty($isEmployeeView) ? 'لا توجد سجلات خصومات أو مكافآت لك حتى الآن.' : 'لا توجد سجلات لعرضها.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $adjustments->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection

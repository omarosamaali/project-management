@extends('layouts.app')
@section('title', 'الخصومات والمكافآت')
@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.adjustments.index') }}" second="الخصومات والمكافآت" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.adjustments.index') }}" method="GET" class="flex items-center">
                        <div class="relative w-full">
                            <input value="{{ request()->search }}" type="text" name="search"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2"
                                placeholder="بحث باسم الموظف...">
                        </div>
                    </form>
                </div>
                <a href="{{ route('dashboard.adjustments.create') }}"
                    class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2">
                    إضافة (خصم/مكافأة)
                </a>
            </div>

            <table class="w-full text-sm text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">الموظف</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustments as $adj)
                    <tr class="border-b">
                        <td class="px-4 py-3 font-bold">{{ $adj->user->name }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="{{ $adj->type == 'bonus' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-2 py-1 rounded">
                                {{ $adj->type == 'bonus' ? 'مكافأة' : 'خصم' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold {{ $adj->type == 'bonus' ? 'text-green-600' : 'text-black' }}">
                            {{ number_format($adj->amount, 2) }}
                        </td>
                        <td class="px-4 py-3">{{ $adj->date }}</td>
                        <td class="px-4 py-3 flex space-x-reverse space-x-2">
                            <a href="{{ route('dashboard.adjustments.edit', $adj->id) }}" class="text-yellow-500"><i
                                    class="fas fa-edit"></i></a>
                            <form action="{{ route('dashboard.adjustments.destroy', $adj->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('حذف؟')" class="text-black"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $adjustments->links() }}</div>
        </div>
    </div>
</section>
@endsection
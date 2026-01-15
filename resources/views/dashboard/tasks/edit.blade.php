@extends('layouts.app')

@section('title', 'تعديل العميل')

@section('content')

<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<section class="p-3 sm:p-5">
    {{-- تفترض أن لديك مكون breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.requests.index') }}" second="العملاء"
        third="تعديل العميل" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات العميل: {{ $userRequest->name }}
            </h2>

            <form method="POST" action="{{ route('dashboard.requests.update', $userRequest->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- رقم الطلب --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الطلب:</label>
                    <input type="text" disabled value="{{ $userRequest->order_number }}"
                        class="cursor-not-allowed w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    <input type="hidden" name="order_number" value="{{ $userRequest->order_number }}">
                </div>

                {{-- النظام --}}
                <label class="block text-sm font-medium text-gray-700">النظام:</label>
                <select name="system_id" id="system_id"
                    class="!mt-1 w-full !px-4 !py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($systems as $system)
                    <option value="{{ $system->id }}" {{ old('system_id', $userRequest->system_id) == $system->id ?
                        'selected' : ''
                        }}>
                        {{ $system->name_ar }}
                    </option>
                    @endforeach
                </select>
                @error('system_id')
                <span class="text-black text-xs">{{ $message }}</span>
                @enderror
                
                {{-- العميل --}}
                <label class="block text-sm font-medium text-gray-700 mb-2">العميل:</label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                    @foreach($clients as $client)
                    <div class="flex items-center">
                        <input type="radio" id="{{ $client->id }}" name="client_id" value="{{ $client->id }}"
                        {{ old('client_id', $userRequest->client_id) == $client->id ? 'checked' : '' }}
                            class="h-4 w-4 rounded-full text-blue-600 border-gray-300 focus:ring-blue-500">
                        <label for="{{ $client->id }}" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                            {{ $client->name }}
                        </label>
                    </div>
                    @endforeach
                    @error('client_id')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        الحالة
                    </h2>

                    <div class="flex gap-4 flex-wrap">

                        <!-- جديد (أخضر) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-green-300 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100">
                            <input type="radio" name="status" value="جديد" {{ old('status', $userRequest->status) ==
                            'جديد' ? 'checked' : '' }}
                            class="w-5 h-5 text-green-600">
                            <span class="font-medium text-green-700">جديد</span>
                        </label>

                        <!-- تحت الإجراء (أزرق) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-blue-300 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100">
                            <input type="radio" name="status" value="تحت الاجراء" {{ old('status', $userRequest->status)
                            == 'تحت الاجراء' ?
                            'checked' : '' }}
                            class="w-5 h-5 text-blue-600">
                            <span class="font-medium text-blue-700">تحت الاجراء</span>
                        </label>

                        <!-- بإنتظار رد العميل (برتقالي) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-orange-300 bg-orange-50 rounded-lg cursor-pointer hover:bg-orange-100">
                            <input type="radio" name="status" value="بإنتظار رد العميل" {{ old('status',
                                $userRequest->status) == 'بإنتظار رد
                            العميل' ? 'checked' : '' }}
                            class="w-5 h-5 text-orange-600">
                            <span class="font-medium text-orange-700">بإنتظار رد العميل</span>
                        </label>

                        <!-- بالانتظار (أصفر) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-yellow-300 bg-yellow-50 rounded-lg cursor-pointer hover:bg-yellow-100">
                            <input type="radio" name="status" value="بالانتظار" {{ old('status', $userRequest->status)
                            == 'بالانتظار' ?
                            'checked' : '' }}
                            class="w-5 h-5 text-yellow-600">
                            <span class="font-medium text-yellow-700">بالانتظار</span>
                        </label>

                        <!-- ملغية (أحمر) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-red-300 bg-red-50 rounded-lg cursor-pointer hover:bg-red-100">
                            <input type="radio" name="status" value="ملغية" {{ old('status', $userRequest->status) ==
                            'ملغية' ? 'checked' : ''
                            }}
                            class="w-5 h-5 text-black">
                            <span class="font-medium text-red-700">ملغية</span>
                        </label>

                        <!-- معلقة (بنفسجي) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-purple-300 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100">
                            <input type="radio" name="status" value="معلقة" {{ old('status', $userRequest->status) ==
                            'معلقة' ? 'checked' : ''
                            }}
                            class="w-5 h-5 text-purple-600">
                            <span class="font-medium text-purple-700">معلقة</span>
                        </label>
                    </div>
                    @error('status')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>

                    <a href="{{ route('dashboard.requests.index') }}"
                        class="mt-3 w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg text-lg font-medium text-gray-700 bg-white hover:bg-gray-50">
                        إلغاء والعودة
                    </a>
                </div>

            </form>
        </div>
    </div>


</section>

@endsection
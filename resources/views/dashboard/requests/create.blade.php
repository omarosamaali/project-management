@extends('layouts.app')

@section('title', 'المشاريع')

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

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.requests.index') }}" second="المشاريع" third="إضافة طلب" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            @foreach ($errors->all() as $error)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>
            @endforeach
            <form method="POST" action="{{ route('dashboard.requests.store') }}" class="space-y-6">
                @csrf
                @php
                $orderNumber = 'REQ' . time() . rand(1, 9);
                @endphp
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">رقم الطلب:</label>
                    <input type="text" id="order_number" name="order_number" value="{{ $orderNumber }}" disabled
                        required
                        class="cursor-not-allowed placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    <input type="hidden" name="order_number" value="{{ $orderNumber }}">
                    @error('order_number')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <label for="order_number" class="pb-0 block text-sm font-medium text-gray-700 mb-1">النظام:</label>
                <select name="system_id" id="system_id"
                    class="!mt-1 placeholder-gray-500 w-full !px-4 !py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    <option disabled selected>إختار النظام</option>
                    @foreach ($systems as $system)\
                    <option value="{{ $system->id }}">{{ $system->name_ar }}</option>
                    @endforeach
                </select>
                @error('systems_id')
                <span class="text-black text-xs mt-1">{{ $message }}</span>
                @enderror

                {{-- العميل --}}
                <label class="block text-sm font-medium text-gray-700 mb-2">العميل:</label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                    @foreach($clients as $client)
                    <div class="flex items-center">
                        <input type="radio" id="{{ $client->id }}" name="client_id" value="{{ $client->id }}"
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
                            <input type="radio" name="status" value="جديد" checked class="w-5 h-5 text-green-600">
                            <span class="font-medium text-green-700">جديد</span>
                        </label>

                        <!-- تحت الإجراء (أزرق) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-blue-300 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100">
                            <input type="radio" name="status" value="تحت الاجراء" class="w-5 h-5 text-blue-600">
                            <span class="font-medium text-blue-700">تحت الاجراء</span>
                        </label>

                        <!-- بإنتظار رد العميل (برتقالي) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-orange-300 bg-orange-50 rounded-lg cursor-pointer hover:bg-orange-100">
                            <input type="radio" name="status" value="بإنتظار رد العميل" class="w-5 h-5 text-orange-600">
                            <span class="font-medium text-orange-700">بإنتظار رد العميل</span>
                        </label>

                        <!-- بالانتظار (أصفر) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-yellow-300 bg-yellow-50 rounded-lg cursor-pointer hover:bg-yellow-100">
                            <input type="radio" name="status" value="بالانتظار" class="w-5 h-5 text-yellow-600">
                            <span class="font-medium text-yellow-700">بالانتظار</span>
                        </label>

                        <!-- ملغية (أحمر) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-red-300 bg-red-50 rounded-lg cursor-pointer hover:bg-red-100">
                            <input type="radio" name="status" value="ملغية" class="w-5 h-5 text-black">
                            <span class="font-medium text-red-700">ملغية</span>
                        </label>

                        <!-- معلقة (بنفسجي) -->
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-purple-300 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100">
                            <input type="radio" name="status" value="معلقة" class="w-5 h-5 text-purple-600">
                            <span class="font-medium text-purple-700">معلقة</span>
                        </label>

                    </div>
                    @error('status')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ بيانات الشريك
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
    $('#system_id').select2({
        placeholder: 'ابحث عن النظام...',
        allowClear: true,
        dir: 'rtl',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'تعديل ملاحظة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="#" second="ملاحظات الإدارة" third="تعديل الملاحظة" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">
            <div class="p-6 border-b bg-green-50">
                <h2 class="text-xl font-bold text-green-800 flex items-center gap-2">
                    <i class="fas fa-edit text-green-600"></i> تعديل بيانات الملاحظة رقم #{{ $remark->id }}
                </h2>
            </div>

            <form method="POST" action="{{ route('dashboard.admin_remarks.update', $remark) }}" class="p-6 space-y-6"
                enctype="multipart/form-data">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الموظف المعني</label>
                    <select name="user_id" required class="w-full rounded-lg border-gray-300">
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $remark->user_id == $emp->id ? 'selected' : '' }}>{{
                            $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نص الملاحظة</label>
                    <textarea name="details" rows="5" required
                        class="w-full rounded-lg border-gray-300">{{ $remark->details }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t pt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الصورة الحالية</label>
                        @if($remark->image)
                        <img src="{{ asset('storage/' . $remark->image) }}" class="w-32 h-32 rounded border shadow-sm">
                        @else
                        <p class="text-gray-400 italic">لا توجد صورة</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">استبدال الصورة (اختياري)</label>
                        <input type="file" name="image" class="w-full text-sm text-gray-500 border rounded-lg p-2">
                    </div>
                </div>

                <div class="pt-6 border-t flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">تحديث
                        الملاحظة</button>
                    <a href="{{ route('dashboard.admin_remarks.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
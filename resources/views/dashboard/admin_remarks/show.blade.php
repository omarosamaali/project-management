@extends('layouts.app')

@section('title', 'عرض الملاحظة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="#" second="ملاحظات الإدارة" third="تفاصيل الملاحظة" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-2xl border rounded-xl overflow-hidden">
            <div class="p-6 border-b bg-gray-50 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">ملاحظة للموظف: {{ $remark->user->name }}
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard.admin_remarks.edit', $remark) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-edit ml-1"></i>
                        تعديل</a>
                    <a href="{{ route('dashboard.admin_remarks.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm"><i
                            class="fas fa-arrow-right ml-1"></i> رجوع</a>
                </div>
            </div>

            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-600 mb-4 border-b pb-2"><i
                                class="fas fa-info-circle ml-2"></i>التفاصيل</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg italic">
                            " {{ $remark->details }} "
                        </p>
                        <div class="mt-6 text-sm text-gray-500">
                            <p><i class="fas fa-calendar ml-2"></i> تاريخ الإضافة: {{ $remark->created_at->format('Y-m-d
                                H:i') }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-blue-600 mb-4 border-b pb-2"><i
                                class="fas fa-image ml-2"></i>المرفق</h3>
                        @if($remark->image)
                        <img src="{{ asset('storage/' . $remark->image) }}" onclick="openModal(this.src)"
                            class="w-full rounded-lg shadow-lg cursor-zoom-in hover:scale-[1.02] transition-transform">
                        @else
                        <div
                            class="h-48 bg-gray-100 flex items-center justify-center rounded-lg border-2 border-dashed">
                            <span class="text-gray-400 italic">لا يوجد مرفق صوري</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function openModal(src) {
        Swal.fire({ imageUrl: src, imageWidth: '100%', showConfirmButton: false });
    }
</script>
@endsection
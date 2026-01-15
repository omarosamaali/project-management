@extends('layouts.app')

@section('title', 'تفاصيل الطلب الخاص: ' . $specialRequest->title)

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('special-request.show') }}" second="عرض الطلب" />
</section>
<div class="max-w-4xl mx-auto mb-10 p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
    <div class="flex justify-between items-center mb-6 pb-4">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            عرض تفاصيل الطلب الخاص
        </h2>

        {{-- زر العودة أو التعديل --}}
        <div>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('special-request.edit', $specialRequest) }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-red-700 transition">
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </path>
                </svg>
                تعديل الطلب
            </a>
            @endif
            <a href="{{ route('special-request.show') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition mr-2">
                العودة للقائمة
            </a>
{{-- زر الدفع (إذا كان مشروع وله سعر ولم يتم الدفع) --}}
@if($specialRequest->is_project && $specialRequest->price && $specialRequest->status !== 'completed')
<div class="p-4 border-2 border-red-600 dark:border-red-500 rounded-lg md:col-span-2 bg-red-50 dark:bg-red-900/10">
    <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">
        💳 الدفع والتسليم
    </h3>

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-gray-700 dark:text-gray-300 mb-2">
                المبلغ المطلوب:
                <span class="text-2xl font-bold text-black dark:text-red-400">
                    {{ number_format($specialRequest->price) }} <x-drhm-icon color="000" />
                </span>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                يرجى إتمام عملية الدفع للبدء في تنفيذ المشروع
            </p>
        </div>

        <button onclick="handlePurchase({{ $specialRequest->id }}, {{ $specialRequest->price }})"
            class="px-6 py-3 bg-black hover:bg-red-700 text-white font-bold rounded-lg transition shadow-lg hover:shadow-xl">
            💰 الدفع الآن
        </button>
    </div>
</div>
@endif

{{-- Modal الدفع --}}
<div id="purchaseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 text-center">
                تأكيد عملية الدفع
            </h3>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                    <span class="text-gray-700 dark:text-gray-300">سعر المشروع:</span>
                    <span class="font-bold text-gray-900 dark:text-white" id="originalPrice">0.00 
                        <x-drhm-icon color="000" />
                    </span>
                </div>

                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                    <span class="text-gray-700 dark:text-gray-300">رسوم المعالجة (7.9% + 2 <x-drhm-icon color="000" />):</span>
                    <span class="font-bold text-gray-900 dark:text-white" id="fees">0.00 <x-drhm-icon color="000" /></span>
                </div>

                <div
                    class="flex justify-between items-center p-3 bg-red-100 dark:bg-red-900/30 rounded border-2 border-red-600">
                    <span class="text-gray-900 dark:text-white font-bold">الإجمالي:</span>
                    <span class="font-bold text-black dark:text-red-400 text-xl" id="totalPrice">0.00 <x-drhm-icon color="000" /></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('purchaseModal').classList.add('hidden')"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    إلغاء
                </button>
                <button onclick="proceedPayment()" id="payButton"
                    class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    تأكيد الدفع
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentSpecialRequestId = null;

function handlePurchase(requestId, price) {
    currentSpecialRequestId = requestId;
    const fees = (price * 0.079) + 2;
    const total = price + fees;
    document.getElementById('originalPrice').textContent = price.toFixed(2) + ' AED';
    document.getElementById('fees').textContent = fees.toFixed(2) + ' AED';
    document.getElementById('totalPrice').textContent = total.toFixed(2) + ' AED';
    document.getElementById('purchaseModal').classList.remove('hidden');
}

async function proceedPayment() {
    const payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
    
    try {
        const response = await fetch('/payment/special-request/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                special_request_id: currentSpecialRequestId
            })
        });
        
        const data = await response.json();
        
        console.log('Response:', data);
        
        if (response.ok && data.success) {
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'حدث خطأ في عملية الدفع');
            console.error('Payment error:', data);
            payButton.disabled = false;
            payButton.innerHTML = 'تأكيد الدفع';
        }
    } catch (error) {
        console.error('Payment error:', error);
        alert('حدث خطأ في عملية الدفع');
        payButton.disabled = false;
        payButton.innerHTML = 'تأكيد الدفع';
    }
}

// إغلاق المودال عند الضغط خارجه
document.getElementById('purchaseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
        </div>
    </div>

    @php
    $statusClasses = [
    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'in_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'بانتظار الدفع' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];
    $statusText = [
    'pending' => 'بانتظار المراجعة',
    'in_review' => 'قيد المراجعة',
    'in_progress' => 'قيد التنفيذ',
    'completed' => 'مكتمل',
    'canceled' => 'ملغى',
    'بانتظار الدفع' => 'بانتظار الدفع',
    ];
    @endphp

    <div class="mb-6 p-4 rounded-lg {{ $statusClasses[$specialRequest->status] }}">
        <p class="text-lg font-semibold flex items-center">
            الحالة الحالية:
            <span class="mr-2 px-3 py-1 font-bold rounded-full text-sm">
                {{ $statusText[$specialRequest->status] ?? $specialRequest->status }}
            </span>
        </p>
    </div>

    {{-- شبكة عرض التفاصيل --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- تفاصيل الطلب --}}
        <div class="p-4 border rounded-lg dark:border-gray-700 md:col-span-2">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">تفاصيل الطلب</h3>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">العنوان:</span>
                <span class="text-gray-900 dark:text-white font-bold">{{ $specialRequest->title }}</span>
            </p>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">الوصف:</span>
                <span class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $specialRequest->description
                    }}</span>
            </p>
        </div>

        {{-- معلومات فنية --}}
        <div class="p-4 border rounded-lg dark:border-gray-700">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">المواصفات الفنية</h3>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">نوع الطلب:</span>
                <span class="text-gray-900 dark:text-white">{{ $specialRequest->project_type }}</span>
            </p>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">الميزات الأساسية:</span>
                <span class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $specialRequest->core_features ??
                    'لم يتم تحديد ميزات إضافية.' }}</span>
            </p>
        </div>

        {{-- معلومات الميزانية والوقت --}}
        <div class="p-4 border rounded-lg dark:border-gray-700">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">الميزانية والجدول الزمني
            </h3>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">الميزانية التقديرية:</span>
                <span class="text-gray-900 dark:text-white">{{ $specialRequest->budget ?? 'غير محدد' }}</span>
            </p>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">الموعد النهائي:</span>
                <span class="text-gray-900 dark:text-white">
                    {{ $specialRequest->deadline ? \Carbon\Carbon::parse($specialRequest->deadline)->translatedFormat('j
                    F Y') : 'غير محدد' }}
                </span>
            </p>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">تاريخ الإرسال:</span>
                <span class="text-gray-900 dark:text-white">{{ $specialRequest->created_at->translatedFormat('j M Y, h:i
                    A') }}</span>
            </p>
        </div>

        {{-- روابط الأمثلة --}}
        @if ($specialRequest->examples)
        <div class="p-4 border rounded-lg dark:border-gray-700 md:col-span-2">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">روابط الأمثلة</h3>
            <a href="{{ $specialRequest->examples }}" target="_blank"
                class="text-blue-500 hover:text-blue-700 underline break-all">
                {{ $specialRequest->examples }}
            </a>
        </div>
        @endif

        {{-- معلومات المشروع --}}
        <div class="p-4 border rounded-lg dark:border-gray-700">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">معلومات المشروع</h3>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">حالة المشروع:</span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $specialRequest->is_project ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                    {{ $specialRequest->is_project ? '✅ تم التحويل إلى مشروع' : '⏳ طلب خاص' }}
                </span>
            </p>

            @if($specialRequest->price)
            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">سعر المشروع:</span>
                <span class="text-gray-900 dark:text-white font-bold text-lg">
                    {{ number_format($specialRequest->price) }} دولار
                </span>
            </p>
            @endif
        </div>

        {{-- معلومات صاحب الطلب --}}
        <div class="p-4 border rounded-lg dark:border-gray-700">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">معلومات صاحب الطلب</h3>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">الاسم:</span>
                <span class="text-gray-900 dark:text-white">{{ $specialRequest->user->name }}</span>
            </p>

            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">البريد الإلكتروني:</span>
                <a href="mailto:{{ $specialRequest->user->email }}" class="text-blue-500 hover:text-blue-700 underline">
                    {{ $specialRequest->user->email }}
                </a>
            </p>

            @if($specialRequest->user->phone)
            <p class="mb-4">
                <span class="font-medium text-gray-700 dark:text-gray-400 block">رقم الهاتف:</span>
                <a href="tel:{{ $specialRequest->user->phone }}" class="text-blue-500 hover:text-blue-700 underline">
                    {{ $specialRequest->user->phone }}
                </a>
            </p>
            @endif
        </div>

        {{-- الشركاء المسندين --}}
        @if($specialRequest->is_project && $specialRequest->partners->count() > 0)
        <div class="p-4 border rounded-lg dark:border-gray-700 md:col-span-2">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">
                الشركاء المسندين للمشروع ({{ $specialRequest->partners->count() }})
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($specialRequest->partners as $partner)
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center mb-3">
                        <div
                            class="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center font-bold text-lg ml-3">
                            {{ mb_substr($partner->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $partner->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $partner->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p>
                            <span class="font-medium text-gray-700 dark:text-gray-400">نسبة الأرباح:</span>
                            <span class="text-gray-900 dark:text-white font-bold">
                                {{ $partner->pivot->profit_share_percentage }}%
                            </span>
                        </p>

                        @if($partner->pivot->notes)
                        <p>
                            <span class="font-medium text-gray-700 dark:text-gray-400">ملاحظات:</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $partner->pivot->notes }}</span>
                        </p>
                        @endif

                        <p>
                            <span class="font-medium text-gray-700 dark:text-gray-400">تاريخ الإسناد:</span>
                            <span class="text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($partner->pivot->created_at)->translatedFormat('j M Y') }}
                            </span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- إجمالي نسب الأرباح --}}
            @php
            $totalPercentage = $specialRequest->partners->sum('pivot.profit_share_percentage');
            @endphp
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-400">إجمالي نسب الأرباح الموزعة:</span>
                    <span class="font-bold {{ $totalPercentage > 100 ? 'text-black' : 'text-green-600' }}">
                        {{ $totalPercentage }}%
                    </span>
                    @if($totalPercentage > 100)
                    <span class="text-black text-xs mr-2">⚠️ تحذير: النسبة الإجمالية تتجاوز 100%</span>
                    @endif
                </p>
            </div>
        </div>
        @elseif($specialRequest->is_project)
        <div class="p-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg md:col-span-2 text-center">
            <p class="text-gray-500 dark:text-gray-400">
                📋 لم يتم إسناد أي شركاء لهذا المشروع بعد
            </p>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('special-request.edit', $specialRequest) }}"
                class="mt-3 inline-block text-black hover:text-red-700 font-medium">
                إسناد شركاء للمشروع ←
            </a>
            @endif
        </div>
        @endif

        {{-- النظام المرتبط (إن وجد) --}}
        @if($specialRequest->system)
        <div class="p-4 border rounded-lg dark:border-gray-700 md:col-span-2">
            <h3 class="text-xl font-semibold text-black dark:text-red-400 mb-3 border-b pb-2">النظام المرتبط</h3>

            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-lg">
                        {{ $specialRequest->system->name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $specialRequest->system->description }}
                    </p>
                </div>
                <a href="{{ route('systems.show', $specialRequest->system) }}"
                    class="px-4 py-2 bg-black hover:bg-red-700 text-white rounded-lg transition">
                    عرض النظام
                </a>
            </div>
        </div>
        @endif

        {{-- أزرار إدارية إضافية --}}
        @if(Auth::user()->role === 'admin')
        <div class="p-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">إجراءات إدارية</h3>

            <div class="flex flex-wrap gap-3">
                @if(!$specialRequest->is_project)
                <form action="{{ route('dashboard.special-request.convert-to-project', $specialRequest) }}"
                    method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition"
                        onclick="return confirm('هل تريد تحويل هذا الطلب إلى مشروع؟')">
                        🚀 تحويل إلى مشروع
                    </button>
                </form>
                @endif

                <form action="{{ route('dashboard.special-request.destroy', $specialRequest) }}" method="POST"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-black hover:bg-red-700 text-white rounded-lg transition"
                        onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء!')">
                        🗑️ حذف الطلب
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
</div>

</div>

@endsection
@extends('layouts.app')

@section('title', 'عرض التذكرة #' . $ticket->id)

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    {{-- تأكد من أن لديك X-breadcrumb أو قم بإلغائها --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.technical_support.index') }}"
        third="التذكرة #{{ $ticket->id }}" second="التذاكر" />
    <div class="mx-auto w-full max-w-4xl">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg p-6">
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partner')
            <h3 class="text-lg font-bold text-black dark:text-red-400 mb-4 mt-8">تحديث حالة التذكرة (للمسؤول او الشريك
                فقط من يمكنه تعديل الحالة)</h3>
            <hr class="mb-4">
            <form action="{{ route('dashboard.technical_support.update', $ticket->id) }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">تغيير حالة
                        التذكرة
                        الحالية ({{ $ticket->status }})</label>
                    <select id="status" name="status" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500">

                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>مفتوحة (Open)</option>
                        <option value="in_review" {{ $ticket->status == 'in_review' ? 'selected' : '' }}>قيد المراجعة
                            (In Review)
                        </option>
                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>محلولة (Resolved)
                        </option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>منهية (Closed)
                        </option>

                    </select>
                    @error('status')
                    <p class="mt-2 text-sm text-black dark:text-black">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex items-center justify-center text-white bg-black hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-500 dark:hover:bg-black focus:outline-none dark:focus:ring-red-800">
                        حفظ الحالة الجديدة
                    </button>
                </div>
            </form>
            @endif

            <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-white mb-4">تفاصيل تذكرة الدعم الفني</h2>
            <hr class="mb-4">

            {{-- **معلومات التذكرة الحالية** --}}
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <p class="font-medium text-gray-500 dark:text-gray-400">رقم التذكرة:</p>
                    <p class="text-gray-900 dark:text-white">{{ $ticket->id }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-500 dark:text-gray-400">موضوع التذكرة:</p>
                    <p class="text-gray-900 dark:text-white">{{ $ticket->subject }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-500 dark:text-gray-400">العميل:</p>
                    <p class="text-gray-900 dark:text-white">{{ $ticket->client->name ?? 'غير متوفر' }}</p>
                </div>
                <div>
                    @php
                    $status_class = match ($ticket->status) {
                    'open' => 'bg-red-100 text-red-800',
                    'in_review' => 'bg-yellow-100 text-yellow-800',
                    'resolved' => 'bg-green-100 text-green-800',
                    default => 'bg-gray-100 text-gray-800',
                    };
                    @endphp
                    <p class="font-medium text-gray-500 w-fit p-2 rounded-xl {{ $status_class }} dark:text-gray-400 mb-2">حالة التذكرة:</p>
                    <span class="px-3 py-1 rounded-full text-xs font-bold">
                        {{ $ticket->status_label }}</span>
                </div>
                <div class="col-span-2">
                    <p class="font-medium text-gray-500 dark:text-gray-400">رقم الطلب المرتبط:</p>
                    <p class="text-gray-900 dark:text-white">
                        @if($ticket->request_id)
                        <a href="{{ route('dashboard.requests.show', $ticket->request_id) }}"
                            class="text-blue-600 hover:underline">
                            {{ $ticket->request->order_number ?? 'N/A' }}
                        </a>
                        @else
                        لا يوجد طلب مرتبط
                        @endif
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="font-medium text-gray-500 dark:text-gray-400">وصف المشكلة:</p>
                    <p class="text-gray-900 text-xl mt-1 dark:text-white bg-gray-100 dark:bg-gray-700 p-3 rounded-lg ">
                        {{ $ticket->description }}</p>
                </div>
            </div>

            {{-- **نموذج إرسال ملاحظات/التفاصيل (إذا كان العميل هو من يعرضها)** --}}
            {{-- @if(Auth::id() == $ticket->client_id && $ticket->status != 'resolved')
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 mt-6">تحديث التذكرة أو إضافة تفاصيل</h3>
            <hr class="mb-4">

            <form action="{{ route('dashboard.technical_support.update', $ticket->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="complaint_details"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">أضف تفاصيل جديدة
                        للشكوى:</label>
                    <textarea id="complaint_details" name="complaint_details" rows="4"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="أضف أي تفاصيل جديدة أو تحديثات حول المشكلة."></textarea>
                    @error('complaint_details')
                    <p class="mt-2 text-sm text-black dark:text-black">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full sm:w-auto flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">
                    تحديث التذكرة
                </button>
            </form>
            @endif --}}

            @if(session('success'))
            <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>

@endsection
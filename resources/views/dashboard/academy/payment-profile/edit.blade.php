@extends('layouts.app')

@section('title', 'ملف الدفع')

@section('content')
@php
    $locked = $profile->isLocked();
    $selectedMethodId = old('payout_method_id', $profile->payout_method_id);
@endphp
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="ملف الدفع" />

    @if(session('success'))
    <div class="max-w-3xl mx-auto mb-4 p-3 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="max-w-3xl mx-auto mb-4 p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200">
        <ul class="list-disc pr-5 space-y-1 mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-3xl mx-auto bg-white border shadow-md rounded-xl p-5">
        <div class="mb-5">
            <h1 class="text-lg font-bold text-slate-800">ملف الدفع الخاص بك</h1>
            <p class="text-sm text-slate-500 mt-1">تُستخدم هذه البيانات لتحويل أرباحك عند طلب السحب. يمكنك تعديلها بحرية حتى أول طلب سحب، وبعدها تُقفل تلقائياً لضمان أمان التحويلات.</p>
        </div>

        @if($locked)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 flex items-start gap-2">
            <i class="fas fa-lock mt-0.5"></i>
            <div>
                <p class="font-bold">{{ __('messages.payment_profile_locked') }}</p>
                <p class="mt-1">لا يمكن تعديل بيانات الدفع بعد إرسال أول طلب سحب. للتواصل بخصوص أي تعديل، راسل الدعم الفني.</p>
            </div>
        </div>

        <div class="mt-5 space-y-4 text-sm">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-500 text-xs">طريقة السحب</p>
                    <p class="font-bold text-slate-800">{{ $profile->method?->title() ?? '—' }}</p>
                </div>
            </div>
            @if($profile->method?->isBankTransfer())
            <div class="grid sm:grid-cols-2 gap-4">
                <div><p class="text-slate-500 text-xs">اسم صاحب الحساب</p><p class="font-semibold">{{ $profile->bank_account_name }}</p></div>
                <div><p class="text-slate-500 text-xs">الآيبان</p><p class="font-semibold dir-ltr text-left">{{ $profile->bank_iban }}</p></div>
                <div><p class="text-slate-500 text-xs">اسم البنك</p><p class="font-semibold">{{ $profile->bank_name }}</p></div>
                <div><p class="text-slate-500 text-xs">الدولة</p><p class="font-semibold">{{ $profile->bank_country }}</p></div>
            </div>
            @elseif($profile->method)
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($profile->method->fields as $field)
                <div>
                    <p class="text-slate-500 text-xs">{{ $field->label() }}</p>
                    <p class="font-semibold">{{ data_get($profile->field_values, $field->key) }}</p>
                </div>
                @endforeach
            </div>
            @endif
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-500 text-xs mb-1">صورة الهوية (أمام)</p>
                    @if($profile->idCardFrontUrl())
                    <img src="{{ $profile->idCardFrontUrl() }}" class="w-full max-w-xs rounded-lg border">
                    @endif
                </div>
                <div>
                    <p class="text-slate-500 text-xs mb-1">صورة الهوية (خلف)</p>
                    @if($profile->idCardBackUrl())
                    <img src="{{ $profile->idCardBackUrl() }}" class="w-full max-w-xs rounded-lg border">
                    @endif
                </div>
            </div>
        </div>
        @else
        <form method="POST" action="{{ route('dashboard.academy.payment-profile.update') }}" enctype="multipart/form-data" class="space-y-6" id="ppForm">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-bold text-slate-800 mb-2">اختر طريقة السحب</h2>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach($methods as $method)
                    <label class="pp-method-card flex items-center gap-3 border rounded-lg p-3 cursor-pointer {{ (int) $selectedMethodId === $method->id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}">
                        <input type="radio" name="payout_method_id" value="{{ $method->id }}" class="pp-method-radio"
                            {{ (int) $selectedMethodId === $method->id ? 'checked' : '' }} required>
                        @if($method->imageUrl())
                        <img src="{{ $method->imageUrl() }}" class="w-9 h-9 rounded object-cover border bg-white">
                        @else
                        <div class="w-9 h-9 rounded bg-slate-100 border flex items-center justify-center text-slate-400">
                            <i class="fas fa-wallet"></i>
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-800 text-sm">{{ $method->title() }}</p>
                            @if($method->isBankTransfer())
                            <p class="text-[11px] text-slate-400">الطريقة الافتراضية</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('payout_method_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            @foreach($methods as $method)
            <div class="pp-method-fields border-t pt-4" data-method-id="{{ $method->id }}"
                style="display: {{ (int) $selectedMethodId === $method->id ? 'block' : 'none' }};">
                @if($method->isBankTransfer())
                <h3 class="text-sm font-bold text-slate-800 mb-3">بيانات الحساب البنكي</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم صاحب الحساب</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $profile->bank_account_name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        @error('bank_account_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الآيبان (IBAN)</label>
                        <input type="text" name="bank_iban" dir="ltr" value="{{ old('bank_iban', $profile->bank_iban) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        @error('bank_iban')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم البنك</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        @error('bank_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">دولة البنك</label>
                        <input type="text" name="bank_country" value="{{ old('bank_country', $profile->bank_country) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        @error('bank_country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                @else
                <h3 class="text-sm font-bold text-slate-800 mb-3">بيانات {{ $method->title() }}</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($method->fields as $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $field->label() }}
                            @if($field->is_required)<span class="text-red-500">*</span>@endif
                        </label>
                        @if($field->input_type === 'textarea')
                        <textarea name="field_values[{{ $field->key }}]" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('field_values.'.$field->key, data_get($profile->field_values, $field->key)) }}</textarea>
                        @else
                        <input type="{{ $field->input_type === 'email' ? 'email' : ($field->input_type === 'number' ? 'number' : 'text') }}"
                            name="field_values[{{ $field->key }}]"
                            value="{{ old('field_values.'.$field->key, data_get($profile->field_values, $field->key)) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        @endif
                        @error('field_values.'.$field->key)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            <div class="border-t pt-4">
                <h3 class="text-sm font-bold text-slate-800 mb-3">صورة الهوية</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    @include('dashboard.course-categories.partials.drag-image-input', [
                        'name' => 'id_card_front',
                        'label' => 'الهوية (الوجه الأمامي)',
                        'existingUrl' => $profile->idCardFrontUrl(),
                        'hint' => 'صورة واضحة للوجه الأمامي لبطاقة الهوية.',
                        'required' => ! $profile->id_card_front_path,
                        'previewRounded' => false,
                    ])
                    @include('dashboard.course-categories.partials.drag-image-input', [
                        'name' => 'id_card_back',
                        'label' => 'الهوية (الوجه الخلفي)',
                        'existingUrl' => $profile->idCardBackUrl(),
                        'hint' => 'صورة واضحة للوجه الخلفي لبطاقة الهوية.',
                        'required' => ! $profile->id_card_back_path,
                        'previewRounded' => false,
                    ])
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">حفظ ملف الدفع</button>
            </div>
        </form>
        @endif
    </div>
</section>

@include('dashboard.course-categories.partials.drag-image-script')
<script>
(() => {
    const radios = document.querySelectorAll('.pp-method-radio');
    const groups = document.querySelectorAll('.pp-method-fields');
    const cards = document.querySelectorAll('.pp-method-card');

    const sync = () => {
        const checked = document.querySelector('.pp-method-radio:checked');
        const id = checked ? checked.value : null;
        groups.forEach((g) => {
            g.style.display = (id && g.dataset.methodId === id) ? 'block' : 'none';
        });
        cards.forEach((c) => {
            const input = c.querySelector('.pp-method-radio');
            c.classList.toggle('border-emerald-500', !!(input && input.checked));
            c.classList.toggle('bg-emerald-50', !!(input && input.checked));
        });
    };

    radios.forEach((r) => r.addEventListener('change', sync));
    sync();
})();
</script>
@endsection

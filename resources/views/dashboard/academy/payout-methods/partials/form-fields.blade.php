@php
    $method = $method ?? null;
    $isSystem = (bool) ($method?->is_system);
    $oldActive = old('is_active', null);
    if (is_array($oldActive)) {
        $isActive = in_array('1', array_map('strval', $oldActive), true);
    } elseif ($oldActive !== null) {
        $isActive = filter_var($oldActive, FILTER_VALIDATE_BOOLEAN);
    } else {
        $isActive = (bool) ($method->is_active ?? true);
    }
@endphp

@include('dashboard.courses.partials.course-switch-styles')

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الطريقة (عربي)</label>
        <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar', $method->name_ar ?? '') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
        @error('name_ar')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الطريقة (إنجليزي)</label>
        <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $method->name_en ?? '') }}" dir="ltr"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
        @error('name_en')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

@include('dashboard.course-categories.partials.drag-image-input', [
    'name' => 'image',
    'label' => 'شعار / صورة الطريقة (اختياري)',
    'existingUrl' => $method?->imageUrl(),
    'showRemove' => (bool) $method,
    'removeName' => 'remove_image',
    'removeLabel' => 'حذف الصورة الحالية',
    'hint' => 'يظهر للمحاضر عند اختيار طريقة السحب.',
    'previewRounded' => false,
])

@if($isSystem)
<div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
    هذه طريقة نظامية (التحويل البنكي)، وحقولها (اسم الحساب / الآيبان / اسم البنك / الدولة) ثابتة وتُدار من ملف الدفع الخاص بالمحاضر.
</div>
@else
<div class="border-t pt-4" id="pmFieldsWrap">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-800">الحقول المطلوبة من المحاضر</h3>
        <button type="button" id="pmAddField" class="px-3 py-1.5 text-xs rounded-lg bg-slate-100 text-slate-700">
            <i class="fas fa-plus"></i> إضافة حقل
        </button>
    </div>
    <p class="text-xs text-slate-500 mt-1">مثال: عنوان المحفظة، بريد PayPal، رقم الحساب... يظهر هذا الحقل للمحاضر عند إعداد ملف الدفع. يُستخدم الاسم الإنجليزي كمفتاح داخلي تلقائياً.</p>

    <div id="pmFieldsRows" class="mt-3 space-y-3">
        @php
            $oldFields = old('fields');
            if (is_array($oldFields) && count($oldFields)) {
                $fieldsForForm = collect($oldFields)->map(function ($row, $key) {
                    return (object) [
                        'label_ar' => $row['label_ar'] ?? '',
                        'label_en' => $row['label_en'] ?? '',
                        'input_type' => $row['input_type'] ?? 'text',
                        'is_required' => array_key_exists('is_required', $row)
                            ? filter_var($row['is_required'], FILTER_VALIDATE_BOOLEAN)
                            : false,
                    ];
                });
            } else {
                $fieldsForForm = $method->fields ?? collect();
            }
        @endphp
        @foreach($fieldsForForm as $i => $field)
        @include('dashboard.academy.payout-methods.partials.field-row', ['idx' => $i, 'field' => $field])
        @endforeach
    </div>
</div>

<template id="pmFieldRowTemplate">
    @include('dashboard.academy.payout-methods.partials.field-row', ['idx' => '__INDEX__', 'field' => null])
</template>
@endif

<div class="border-t pt-4">
    <input type="hidden" name="is_active" value="0">
    <label class="course-switch-field cursor-pointer">
        <span class="text-sm text-gray-700">
            <span class="font-semibold text-slate-800 block">طريقة نشطة</span>
            <span class="text-xs text-slate-500">متاحة للمحاضرين عند إعداد ملف الدفع</span>
        </span>
        <span class="course-switch">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
            <span class="course-switch-track" aria-hidden="true"></span>
        </span>
    </label>
</div>

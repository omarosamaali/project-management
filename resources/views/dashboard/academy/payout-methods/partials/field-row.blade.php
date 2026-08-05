@php
    $field = $field ?? null;
@endphp
<div class="pm-field-row grid sm:grid-cols-5 gap-2 items-start bg-slate-50 border border-slate-200 rounded-lg p-3">
    <div class="sm:col-span-2">
        <label class="block text-[11px] font-medium text-gray-500 mb-1">التسمية (عربي)</label>
        <input type="text" name="fields[{{ $idx }}][label_ar]" value="{{ $field->label_ar ?? '' }}"
            data-pm-label="ar" placeholder="عنوان المحفظة" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-[11px] font-medium text-gray-500 mb-1">التسمية (إنجليزي) <span class="text-slate-400">· مفتاح الحقل</span></label>
        <input type="text" name="fields[{{ $idx }}][label_en]" value="{{ $field->label_en ?? '' }}" dir="ltr"
            data-pm-label="en" placeholder="Wallet address" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
    </div>
    <div class="sm:col-span-1">
        <label class="block text-[11px] font-medium text-gray-500 mb-1">النوع</label>
        <select name="fields[{{ $idx }}][input_type]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
            @foreach(['text' => 'نص', 'textarea' => 'نص طويل', 'email' => 'بريد', 'number' => 'رقم'] as $val => $label)
            <option value="{{ $val }}" {{ ($field->input_type ?? 'text') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-5 flex items-center justify-between gap-3">
        <label class="course-switch-field cursor-pointer !min-h-0 !py-2 !px-3 !w-auto flex-1 max-w-xs">
            <span class="text-xs text-slate-600">حقل إلزامي</span>
            <span class="course-switch scale-90 origin-center">
                <input type="checkbox" name="fields[{{ $idx }}][is_required]" value="1" {{ ($field->is_required ?? true) ? 'checked' : '' }}>
                <span class="course-switch-track" aria-hidden="true"></span>
            </span>
        </label>
        <button type="button" class="pm-remove-field px-2 py-1 text-xs rounded bg-red-50 text-red-600 border border-red-200">
            <i class="fas fa-trash"></i> حذف
        </button>
    </div>
</div>

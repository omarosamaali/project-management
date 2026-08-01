@props([
    'name' => 'icon',
    'label' => 'الأيقونة — اختياري',
    'existingUrl' => null,
    'showRemove' => false,
    'hint' => 'إن لم تُرفع صورة، تُستخدم أيقونة افتراضية.',
    'required' => false,
    'previewRounded' => true,
])

<div class="drag-image-field">
    @if ($label)
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if ($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    <div class="relative drag-image-dropzone border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-500 transition cursor-pointer">
        <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif"
            class="drag-image-input absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            @if ($required) required @endif>
        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
        <p class="text-sm text-gray-600">اضغط أو اسحب الصورة هنا</p>
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (حد 2MB)</p>
        <p class="drag-image-filename text-xs text-blue-600 mt-2 font-medium hidden"></p>
    </div>
    @error($name)
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <img src="{{ $existingUrl ?? '' }}" alt=""
            class="drag-image-preview {{ $previewRounded ? 'w-16 h-16 rounded-full' : 'w-28 h-20 rounded-lg' }} object-cover border bg-slate-100 {{ $existingUrl ? '' : 'hidden' }}">
        @if ($showRemove)
        <label class="drag-image-remove-wrap inline-flex items-center gap-1 text-xs text-red-600 {{ $existingUrl ? '' : 'hidden' }}">
            <input type="checkbox" name="remove_icon" value="1"> حذف الأيقونة
        </label>
        @endif
    </div>
    @if ($hint)
    <p class="text-[11px] text-slate-400 mt-1">{{ $hint }}</p>
    @endif
</div>

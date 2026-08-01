<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">العنوان (عربي) <span class="text-red-600">*</span></label>
        <input type="text" name="title_ar" id="title_ar" required
            value="{{ old('title_ar', $category->title_ar ?? '') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        @error('title_ar') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title (English) <span class="text-red-600">*</span></label>
        <input type="text" name="title_en" id="title_en" required dir="ltr"
            value="{{ old('title_en', $category->title_en ?? '') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        @error('title_en') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الوصف (عربي) — اختياري</label>
        <textarea name="description_ar" id="description_ar" rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('description_ar', $category->description_ar ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (English) — optional</label>
        <textarea name="description_en" id="description_en" rows="3" dir="ltr"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('description_en', $category->description_en ?? '') }}</textarea>
    </div>
</div>

@php
    $isActive = old('is_active', isset($category) ? ($category->is_active ? '1' : '0') : '1') == '1';
@endphp

<style>
    .category-status-toggle:checked + .category-status-track {
        background-color: #0D2444;
    }
    .category-status-toggle:focus + .category-status-track {
        box-shadow: 0 0 0 4px rgba(13, 36, 68, 0.25);
    }
</style>

<div class="max-w-md space-y-4">
    @include('dashboard.course-categories.partials.drag-image-input', [
        'existingUrl' => isset($category) && $category->icon ? $category->iconUrl() : null,
        'showRemove' => isset($category) && $category->icon,
    ])
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer category-status-toggle" {{ $isActive ? 'checked' : '' }}>
            <div class="category-status-track relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            <span class="ms-3 text-sm font-medium text-gray-700 select-none">نشط</span>
        </label>
    </div>
</div>

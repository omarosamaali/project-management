@props(['user', 'logoRequired' => false])

@php
    $accountType = old('account_type', $user->account_type ?? 'personal');
    $logoUrl = \App\Support\ClientCompanyFields::logoUrl($user);
@endphp

<div class="border-t border-gray-200 dark:border-gray-600 pt-6 space-y-4">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
        <i class="fas fa-building text-blue-600"></i>
        نوع الحساب وبيانات الشركة
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <label
            class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition account-type-option {{ $accountType === 'personal' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600' }}">
            <input type="radio" name="account_type" value="personal" class="text-blue-600 focus:ring-blue-500"
                {{ $accountType === 'personal' ? 'checked' : '' }} required>
            <span>
                <span class="block font-bold text-gray-800 dark:text-white">حساب شخصي</span>
                <span class="text-xs text-gray-500">بدون بيانات شركة</span>
            </span>
        </label>
        <label
            class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition account-type-option {{ $accountType === 'business' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600' }}">
            <input type="radio" name="account_type" value="business" class="text-blue-600 focus:ring-blue-500"
                {{ $accountType === 'business' ? 'checked' : '' }}>
            <span>
                <span class="block font-bold text-gray-800 dark:text-white">حساب تجاري</span>
                <span class="text-xs text-gray-500">اسم الشركة ولوجو</span>
            </span>
        </label>
    </div>
    @error('account_type')
    <p class="text-red-600 text-xs">{{ $message }}</p>
    @enderror

    <div id="business-fields" class="space-y-4 {{ $accountType === 'business' ? '' : 'hidden' }}">
        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الشركة</label>
            <input type="text" id="company_name" name="company_name"
                value="{{ old('company_name', $user->company_name) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-blue-500">
            @error('company_name')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">لوجو الشركة</label>
            @if($logoUrl)
            <div class="mb-3 flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-600">
                <img src="{{ $logoUrl }}" alt="لوجو الشركة" class="h-16 w-auto max-w-[140px] object-contain rounded">
                <p class="text-xs text-gray-500">اللوجو الحالي — ارفع صورة جديدة لتغييره{{ $logoRequired ? '' : ' (اختياري)' }}</p>
            </div>
            @endif
            <input type="file" id="company_logo" name="company_logo" accept="image/*"
                class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
            <p class="text-xs text-gray-500 mt-1">JPG أو PNG — حد أقصى 5 ميجابايت</p>
            @error('company_logo')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const businessFields = document.getElementById('business-fields');
        const companyName = document.getElementById('company_name');
        const companyLogo = document.getElementById('company_logo');
        const accountRadios = document.querySelectorAll('input[name="account_type"]');
        const logoRequired = @json($logoRequired);

        function syncBusinessFields() {
            const isBusiness = document.querySelector('input[name="account_type"]:checked')?.value === 'business';
            if (businessFields) {
                businessFields.classList.toggle('hidden', !isBusiness);
            }
            if (companyName) {
                companyName.required = isBusiness;
            }
            if (companyLogo) {
                companyLogo.required = isBusiness && logoRequired && !@json((bool) $logoUrl);
            }
            document.querySelectorAll('.account-type-option').forEach((label) => {
                const input = label.querySelector('input[name="account_type"]');
                const active = input?.checked;
                label.classList.toggle('border-blue-500', active);
                label.classList.toggle('bg-blue-50', active);
                label.classList.toggle('dark:bg-blue-900/20', active);
                label.classList.toggle('border-gray-200', !active);
                label.classList.toggle('dark:border-gray-600', !active);
            });
        }

        accountRadios.forEach((r) => r.addEventListener('change', syncBusinessFields));
        syncBusinessFields();
    });
</script>

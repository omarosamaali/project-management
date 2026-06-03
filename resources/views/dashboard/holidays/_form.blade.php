@php
    $holiday = $holiday ?? null;
    $selectedIds = old('employee_ids', $holiday ? $holiday->employees->pluck('id')->all() : []);
    $typeVal = old('type', $holiday->type ?? 'general');
@endphp

<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium">نوع العطلة</label>
        <select name="type" id="holiday_type" required class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
            <option value="general" {{ $typeVal === 'general' ? 'selected' : '' }}>عامة (لجميع الموظفين)</option>
            <option value="private" {{ $typeVal === 'private' ? 'selected' : '' }}>خاصة (موظفون محددون)</option>
        </select>
        @error('type')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
    </div>

    <div id="employees_block" class="{{ $typeVal === 'private' ? '' : 'hidden' }}">
        <label class="block mb-2 text-sm font-medium">الموظفون (يمكن اختيار أكثر من موظف)</label>
        <select name="employee_ids[]" id="employee_ids" multiple size="8"
            class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ in_array($emp->id, $selectedIds) ? 'selected' : '' }}>
                {{ $emp->name }}
            </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد على ويندوز</p>
        @error('employee_ids')<span class="text-red-600 text-xs block">{{ $message }}</span>@enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium">اسم العطلة</label>
        <input type="text" name="name" value="{{ old('name', $holiday->name ?? '') }}" required
            class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
        @error('name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block mb-2 text-sm font-medium">تاريخ البداية</label>
            <input type="date" name="start_date" value="{{ old('start_date', isset($holiday) ? $holiday->start_date->format('Y-m-d') : '') }}" required
                class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
            @error('start_date')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block mb-2 text-sm font-medium">تاريخ النهاية</label>
            <input type="date" name="end_date" value="{{ old('end_date', isset($holiday) ? $holiday->end_date->format('Y-m-d') : '') }}" required
                class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
            @error('end_date')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block mb-2 text-sm font-medium">حالة الخصم من الراتب</label>
            <select name="salary_deduction_status" required class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
                <option value="paid" {{ old('salary_deduction_status', $holiday->salary_deduction_status ?? 'paid') === 'paid' ? 'selected' : '' }}>
                    مدفوعة — لا يُخصم يوم العطلة من الراتب
                </option>
                <option value="unpaid" {{ old('salary_deduction_status', $holiday->salary_deduction_status ?? '') === 'unpaid' ? 'selected' : '' }}>
                    غير مدفوعة — يُخصم يوم العطلة من الراتب
                </option>
            </select>
            @error('salary_deduction_status')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block mb-2 text-sm font-medium">الحالة</label>
            <select name="status" required class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">
                <option value="active" {{ old('status', $holiday->status ?? 'active') === 'active' ? 'selected' : '' }}>فعالة</option>
                <option value="inactive" {{ old('status', $holiday->status ?? '') === 'inactive' ? 'selected' : '' }}>غير فعالة</option>
            </select>
            @error('status')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
        </div>
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium">التفاصيل</label>
        <textarea name="details" rows="3" class="w-full border rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600">{{ old('details', $holiday->details ?? '') }}</textarea>
        @error('details')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('holiday_type');
        const block = document.getElementById('employees_block');
        const multi = document.getElementById('employee_ids');

        function toggleEmployees() {
            const isPrivate = typeSelect.value === 'private';
            block.classList.toggle('hidden', !isPrivate);
            if (multi) {
                multi.required = isPrivate;
            }
        }

        typeSelect.addEventListener('change', toggleEmployees);
        toggleEmployees();
    });
</script>

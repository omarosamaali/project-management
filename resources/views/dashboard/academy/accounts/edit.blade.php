@extends('layouts.app')

@section('title', __('messages.edit') . ' — ' . $meta['singular'])

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route($meta['route'] . '.index') }}" second="{{ $meta['title'] }}" third="{{ __('messages.edit') }}" />
    <div class="mx-auto max-w-3xl w-full">
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl border rounded-xl">
            <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('messages.edit') }}: {{ $account->name }}</h2>

            @foreach ($errors->all() as $error)
            <div class="p-3 mb-3 text-sm text-red-800 rounded-lg bg-red-50">{{ $error }}</div>
            @endforeach

            <form method="POST" action="{{ route($meta['route'] . '.update', $account) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $account->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $account->email) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $account->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.password') }} <span class="text-gray-400 text-xs">({{ __('messages.optional_leave_blank') }})</span></label>
                    <input type="text" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>

                @if($meta['role'] === 'trainer')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.trainer_category') }}</label>
                    <select name="course_category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">{{ __('messages.trainer_category_placeholder') }}</option>
                        @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}" @selected((string) old('course_category_id', $account->course_category_id) === (string) $category->id)>
                            {{ $category->title(app()->getLocale()) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.country') }}</label>
                    <div class="country-select2-host is-classic">
                        <select id="trainer_country_select2" name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة' : 'Select country' }}</option>
                        </select>
                    </div>
                    @error('country')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.trainer_linkedin') }}</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $account->linkedin_url) }}"
                        placeholder="https://linkedin.com/in/username"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                    @error('linkedin_url')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.become_trainer_teaching_lang') }}</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="teaching_language" value="ar" {{ old('teaching_language', $account->teaching_language ?? 'ar') === 'ar' ? 'checked' : '' }}>
                            {{ __('messages.become_trainer_teaching_lang_ar') }}
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="teaching_language" value="en" {{ old('teaching_language', $account->teaching_language) === 'en' ? 'checked' : '' }}>
                            {{ __('messages.become_trainer_teaching_lang_en') }}
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.trainer_bio_label') }}</label>
                    <textarea name="trainer_bio" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-y"
                        placeholder="اكتب نبذة تعريفية عن المحاضر...">{{ old('trainer_bio', $account->trainer_bio) }}</textarea>
                    @error('trainer_bio')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.status') }}</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="active" {{ old('status', $account->status) === 'active' ? 'checked' : '' }}>
                            {{ __('messages.active') }}
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="inactive" {{ old('status', $account->status) === 'inactive' ? 'checked' : '' }}>
                            {{ __('messages.inactive') }}
                        </label>
                        @if($meta['role'] === 'trainer')
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="pending" {{ old('status', $account->status) === 'pending' ? 'checked' : '' }}>
                            {{ __('messages.pending') }}
                        </label>
                        @endif
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-black text-white rounded-lg hover:bg-gray-800">
                        {{ __('messages.save') }}
                    </button>
                    <a href="{{ route($meta['route'] . '.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@if($meta['role'] === 'trainer')
@include('partials.country-select2', [
    'selector' => '#trainer_country_select2',
    'oldCountry' => old('country', $account->country ?? ''),
    'variant' => 'classic',
])
@endif
@endsection

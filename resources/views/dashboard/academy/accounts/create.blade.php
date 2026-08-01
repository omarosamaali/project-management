@extends('layouts.app')

@section('title', $meta['create_label'])

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route($meta['route'] . '.index') }}" second="{{ $meta['title'] }}" third="{{ $meta['create_label'] }}" />
    <div class="mx-auto max-w-3xl w-full">
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl border rounded-xl">
            <h2 class="text-xl font-bold text-gray-900 mb-6">{{ $meta['create_label'] }}</h2>

            @foreach ($errors->all() as $error)
            <div class="p-3 mb-3 text-sm text-red-800 rounded-lg bg-red-50">{{ $error }}</div>
            @endforeach

            <form method="POST" action="{{ route($meta['route'] . '.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.password') }}</label>
                    <input type="text" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.status') }}</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            {{ __('messages.active') }}
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            {{ __('messages.inactive') }}
                        </label>
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
@endsection

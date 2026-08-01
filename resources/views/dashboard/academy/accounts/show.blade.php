@extends('layouts.app')

@section('title', $meta['singular'] . ' — ' . $account->name)

@section('content')
@php
    $statusLabel = match ($account->status) {
        'active' => __('messages.active'),
        'pending' => __('messages.pending'),
        'inactive' => __('messages.inactive'),
        'blocked' => __('messages.blocked'),
        default => $account->status,
    };
    $statusClass = match ($account->status) {
        'active' => 'bg-green-100 text-green-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'inactive' => 'bg-gray-100 text-gray-700',
        'blocked' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp
<section class="p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route($meta['route'] . '.index') }}" second="{{ $meta['title'] }}" third="{{ $account->name }}" />
    <div class="mx-auto max-w-3xl w-full">
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl border rounded-xl space-y-5">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <img src="{{ $account->avatarUrl() }}" alt="" class="w-14 h-14 rounded-full object-cover border">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $account->name }}</h2>
                        <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>
                <a href="{{ route($meta['route'] . '.edit', $account) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    {{ __('messages.edit') }}
                </a>
            </div>

            @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($meta['role'] === 'trainer' && $account->status === 'pending')
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('messages.trainer_pending_admin_hint') }}
            </div>
            <form method="POST" action="{{ route($meta['route'] . '.update', $account) }}" class="flex flex-wrap gap-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $account->name }}">
                <input type="hidden" name="email" value="{{ $account->email }}">
                <input type="hidden" name="phone" value="{{ $account->phone }}">
                <input type="hidden" name="country" value="{{ $account->country }}">
                <input type="hidden" name="course_category_id" value="{{ $account->course_category_id }}">
                <input type="hidden" name="status" value="active">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-check ml-1"></i> {{ __('messages.approve_account') }}
                </button>
            </form>
            @endif

            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('messages.email') }}</dt>
                    <dd class="font-medium text-gray-900" dir="ltr">{{ $account->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.phone') }}</dt>
                    <dd class="font-medium text-gray-900">
                        @if($account->phone)
                        <span dir="ltr" class="inline-block text-left" style="unicode-bidi: plaintext;">{{ $account->phone }}</span>
                        @else
                        —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.status') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $statusLabel }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.role') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $meta['singular'] }}</dd>
                </div>
                @if($meta['role'] === 'trainer')
                <div>
                    <dt class="text-gray-500">{{ __('messages.trainer_category') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $account->courseCategory?->title(app()->getLocale()) ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.trainer_terms_accepted') }}</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $account->terms_accepted_at ? $account->terms_accepted_at->format('Y-m-d H:i') : '—' }}
                    </dd>
                </div>
                @endif
            </dl>

            @if($meta['role'] === 'trainer')
            <div class="pt-2 border-t">
                <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('messages.trainer_documents') }}</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ __('messages.trainer_id_front') }}</p>
                        @if($account->idCardFrontUrl())
                        <a href="{{ $account->idCardFrontUrl() }}" target="_blank" class="block">
                            <img src="{{ $account->idCardFrontUrl() }}" alt="" class="w-full max-h-48 object-contain rounded-lg border bg-slate-50">
                        </a>
                        @else
                        <p class="text-sm text-gray-400">—</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ __('messages.trainer_id_back') }}</p>
                        @if($account->idCardBackUrl())
                        <a href="{{ $account->idCardBackUrl() }}" target="_blank" class="block">
                            <img src="{{ $account->idCardBackUrl() }}" alt="" class="w-full max-h-48 object-contain rounded-lg border bg-slate-50">
                        </a>
                        @else
                        <p class="text-sm text-gray-400">—</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

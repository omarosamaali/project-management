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
        'pending' => 'bg-amber-100 text-amber-900 ring-1 ring-amber-200',
        'inactive' => 'bg-gray-100 text-gray-700',
        'blocked' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-700',
    };
    $isTrainer = $meta['role'] === 'trainer';
    $locale = app()->getLocale();
    $teachLang = ($account->teaching_language ?? 'ar') === 'en'
        ? __('messages.become_trainer_teaching_lang_en')
        : __('messages.become_trainer_teaching_lang_ar');

    $checks = [];
    if ($isTrainer) {
        $checks = [
            [
                'ok' => (bool) $account->avatar,
                'label' => __('messages.trainer_review_check_avatar'),
            ],
            [
                'ok' => (bool) $account->course_category_id,
                'label' => __('messages.trainer_category'),
            ],
            [
                'ok' => filled($account->linkedin_url),
                'label' => __('messages.trainer_linkedin'),
            ],
            [
                'ok' => (bool) $account->teachingSampleUrl(),
                'label' => __('messages.trainer_sample').' ('.__('messages.optional').')',
            ],
            [
                'ok' => filled($account->trainer_bio) && mb_strlen(trim((string) $account->trainer_bio)) >= 120,
                'label' => __('messages.trainer_bio'),
            ],
            [
                'ok' => (bool) $account->terms_accepted_at,
                'label' => __('messages.trainer_terms_accepted'),
            ],
        ];
    }
    $checksPassed = collect($checks)->where('ok', true)->count();
    $checksTotal = count($checks);
    $checksComplete = $checksTotal > 0 && $checksPassed === $checksTotal;
@endphp
<section class="p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route($meta['route'] . '.index') }}" second="{{ $meta['title'] }}" third="{{ $account->name }}" />

    <div class="mx-auto {{ $isTrainer ? 'max-w-5xl' : 'max-w-3xl' }} w-full space-y-4">
        @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <img src="{{ $account->avatarUrl() }}" alt=""
                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $account->name }}</h2>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                        <p class="text-sm text-gray-500" dir="ltr">{{ $account->email }}</p>
                        @if($isTrainer)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($account->courseCategory)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-800 ring-1 ring-violet-100">
                                <i class="fas fa-layer-group text-[10px]"></i>
                                {{ $account->courseCategory->title($locale) }}
                            </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-pink-50 text-pink-800 ring-1 ring-pink-100">
                                <i class="fas fa-language text-[10px]"></i>
                                {{ $teachLang }}
                            </span>
                            @if($account->country)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 ring-1 ring-slate-200">
                                <i class="fas fa-globe text-[10px]"></i>
                                {{ $account->country }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 sm:justify-end">
                    <a href="{{ route($meta['route'] . '.edit', $account) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700">
                        <i class="fas fa-edit"></i>
                        {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route($meta['route'] . '.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200">
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>

            @if($isTrainer && $account->status === 'pending')
            <div class="mt-5 rounded-2xl border border-amber-200 bg-gradient-to-l from-amber-50 to-orange-50 p-4 sm:p-5">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hourglass-half"></i>
                        </span>
                        <div>
                            <p class="font-bold text-amber-950">{{ __('messages.trainer_review_pending_title') }}</p>
                            <p class="text-sm text-amber-900/80 mt-0.5">{{ __('messages.trainer_pending_admin_hint') }}</p>
                            <p class="text-xs font-semibold mt-2 {{ $checksComplete ? 'text-green-700' : 'text-amber-800' }}">
                                {{ __('messages.trainer_review_checklist_progress', ['done' => $checksPassed, 'total' => $checksTotal]) }}
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route($meta['route'] . '.update', $account) }}" class="flex-shrink-0">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $account->name }}">
                        <input type="hidden" name="email" value="{{ $account->email }}">
                        <input type="hidden" name="phone" value="{{ $account->phone }}">
                        <input type="hidden" name="country" value="{{ $account->country }}">
                        <input type="hidden" name="course_category_id" value="{{ $account->course_category_id }}">
                        <input type="hidden" name="status" value="active">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-3 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 shadow-lg shadow-green-600/20">
                            <i class="fas fa-check-circle"></i>
                            {{ __('messages.approve_account') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        @if($isTrainer)
        {{-- Completeness checklist --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">{{ __('messages.trainer_review_checklist_title') }}</h3>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $checksComplete ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">
                    {{ $checksPassed }}/{{ $checksTotal }}
                </span>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                @foreach($checks as $check)
                <div class="flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-sm
                    {{ $check['ok'] ? 'border-green-100 bg-green-50/70 text-green-900' : 'border-rose-100 bg-rose-50/70 text-rose-900' }}">
                    <i class="fas {{ $check['ok'] ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-rose-500' }}"></i>
                    <span class="font-semibold leading-snug">{{ $check['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Account details --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">{{ __('messages.trainer_review_account_title') }}</h3>
            <dl class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.email') }}</dt>
                    <dd class="font-semibold text-gray-900 break-all" dir="ltr">{{ $account->email }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.phone') }}</dt>
                    <dd class="font-semibold text-gray-900">
                        @if($account->phone)
                        <span dir="ltr" class="inline-block text-left" style="unicode-bidi: plaintext;">{{ $account->phone }}</span>
                        @else
                        —
                        @endif
                    </dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.country') }}</dt>
                    <dd class="font-semibold text-gray-900">{{ $account->country ?: '—' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.trainer_category') }}</dt>
                    <dd class="font-semibold text-gray-900">{{ $account->courseCategory?->title($locale) ?: '—' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.become_trainer_teaching_lang') }}</dt>
                    <dd class="font-semibold text-gray-900">{{ $teachLang }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.trainer_terms_accepted') }}</dt>
                    <dd class="font-semibold text-gray-900">
                        {{ $account->terms_accepted_at ? $account->terms_accepted_at->format('Y-m-d H:i') : '—' }}
                    </dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.trainer_review_applied_at') }}</dt>
                    <dd class="font-semibold text-gray-900">
                        {{ optional($account->created_at)->format('Y-m-d H:i') ?: '—' }}
                    </dd>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5">
                    <dt class="text-xs text-gray-500 mb-1">{{ __('messages.status') }}</dt>
                    <dd class="font-semibold text-gray-900">{{ $statusLabel }}</dd>
                </div>
            </dl>
        </div>

        {{-- Bio --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-9 h-9 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                    <i class="fas fa-quote-right"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('messages.trainer_bio') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('messages.trainer_review_bio_hint') }}</p>
                </div>
            </div>
            @if(filled($account->trainer_bio))
            <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-sm text-gray-800 leading-7 whitespace-pre-line">
                {{ $account->trainer_bio }}
            </div>
            @else
            <p class="text-sm text-rose-600 font-medium">{{ __('messages.trainer_review_missing') }}</p>
            @endif
        </div>

        {{-- LinkedIn + sample --}}
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fab fa-linkedin-in"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.trainer_linkedin') }}</h3>
                    </div>
                </div>
                @if(filled($account->linkedin_url))
                <a href="{{ $account->linkedin_url }}" target="_blank" rel="noopener"
                    class="group rounded-2xl border border-dashed border-sky-200 bg-sky-50 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-sky-300 hover:bg-sky-100/70 transition">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 truncate" dir="ltr">{{ $account->linkedin_url }}</p>
                        <p class="text-xs text-gray-500">{{ __('messages.trainer_review_open_file') }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm"
                        style="background:#0a66c2;color:#fff;">
                        <i class="fas fa-external-link-alt"></i>
                        LinkedIn
                    </span>
                </a>
                @else
                <p class="text-sm text-red-600 font-medium">{{ __('messages.trainer_review_missing') }}</p>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-video"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.trainer_sample') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('messages.trainer_review_sample_hint') }}</p>
                    </div>
                </div>
                @if($account->teachingSampleUrl())
                <div class="rounded-2xl overflow-hidden border border-slate-200 bg-black">
                    <video controls preload="metadata" class="w-full max-h-72 bg-black"
                        src="{{ $account->teachingSampleUrl() }}">
                        {{ __('messages.trainer_review_video_fallback') }}
                    </video>
                </div>
                <a href="{{ $account->teachingSampleUrl() }}" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-indigo-700 hover:underline">
                    <i class="fas fa-external-link-alt"></i>
                    {{ __('messages.trainer_review_open_sample') }}
                </a>
                @else
                <p class="text-sm text-slate-500 font-medium">{{ __('messages.optional') }} — {{ __('messages.trainer_review_missing') }}</p>
                @endif
            </div>
        </div>

        {{-- ID documents (from payment config after apply) --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-id-card"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('messages.trainer_documents') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('messages.trainer_review_id_from_payment_hint') }}</p>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">{{ __('messages.trainer_id_front') }}</p>
                    @if($account->idCardFrontUrl())
                    <a href="{{ $account->idCardFrontUrl() }}" target="_blank" rel="noopener" class="block group">
                        <img src="{{ $account->idCardFrontUrl() }}" alt=""
                            class="w-full max-h-64 object-contain rounded-2xl border border-slate-200 bg-slate-50 group-hover:ring-2 group-hover:ring-pink-300 transition">
                    </a>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500 font-medium">
                        {{ __('messages.trainer_review_id_pending_payment') }}
                    </div>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">{{ __('messages.trainer_id_back') }}</p>
                    @if($account->idCardBackUrl())
                    <a href="{{ $account->idCardBackUrl() }}" target="_blank" rel="noopener" class="block group">
                        <img src="{{ $account->idCardBackUrl() }}" alt=""
                            class="w-full max-h-64 object-contain rounded-2xl border border-slate-200 bg-slate-50 group-hover:ring-2 group-hover:ring-pink-300 transition">
                    </a>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500 font-medium">
                        {{ __('messages.trainer_review_id_pending_payment') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @else
        {{-- Non-trainer simple show --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl p-6">
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
            </dl>
        </div>
        @endif
    </div>
</section>
@endsection

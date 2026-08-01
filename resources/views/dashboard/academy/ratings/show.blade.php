@extends('layouts.app')

@section('title', __('messages.rating_details'))

@section('content')
@php
    $locale = app()->getLocale();
    $answers = $rating->answers ?? [];
@endphp
<section class="p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route('dashboard.academy.ratings.index') }}" second="{{ __('messages.academy_ratings') }}" third="{{ __('messages.rating_details') }}" />
    <div class="mx-auto max-w-3xl w-full space-y-4">
        <div class="p-6 bg-white shadow-xl border rounded-xl">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('messages.rating_details') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $rating->course?->name_ar ?? $rating->course?->name_en }}
                    </p>
                </div>
                @php $score = $rating->overallScore(); @endphp
                @if($score !== null)
                <div class="px-4 py-2 rounded-lg bg-amber-50 text-amber-700 font-bold">
                    <i class="fas fa-star ml-1"></i> {{ $score }} / 5
                </div>
                @endif
            </div>

            <dl class="grid sm:grid-cols-2 gap-4 text-sm mb-6 pb-6 border-b">
                <div>
                    <dt class="text-gray-500">{{ __('messages.trainee') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $rating->user?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.email') }}</dt>
                    <dd class="font-medium text-gray-900" dir="ltr">{{ $rating->user?->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.completed_at') }}</dt>
                    <dd class="font-medium text-gray-900">{{ optional($rating->completed_at)->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('messages.featured_review') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $rating->is_featured ? __('messages.yes') : __('messages.no') }}</dd>
                </div>
            </dl>

            <h3 class="font-bold text-gray-900 mb-4">{{ __('messages.question_answers') }}</h3>
            <div class="space-y-4">
                @foreach($questions as $q)
                @php
                    $qid = $q['id'] ?? null;
                    $label = $locale === 'en' ? ($q['label_en'] ?? $q['label_ar'] ?? $qid) : ($q['label_ar'] ?? $q['label_en'] ?? $qid);
                    $value = $qid ? ($answers[$qid] ?? null) : null;
                @endphp
                <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                    <div class="text-sm font-medium text-gray-800 mb-2">{{ $label }}</div>
                    @if(($q['type'] ?? '') === 'scale')
                        <div class="text-lg font-bold text-amber-600">
                            {{ $value !== null ? $value . ' / ' . ($q['max'] ?? 5) : '—' }}
                        </div>
                    @else
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $value !== null && $value !== '' ? $value : '—' }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard.academy.ratings.index') }}" class="text-sm text-gray-600 hover:text-black">
                    ← {{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', __('messages.academy_ratings'))

@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route('dashboard.academy.ratings.index') }}" second="{{ __('messages.academy_ratings') }}" />
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('messages.academy_ratings') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('messages.academy_ratings_hint') }}</p>
                </div>
                <form action="{{ route('dashboard.academy.ratings.index') }}" method="GET" class="w-full md:w-72">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">{{ __('messages.trainee') }}</th>
                            <th class="px-4 py-3">{{ __('messages.course') }}</th>
                            <th class="px-4 py-3">{{ __('messages.overall_rating') }}</th>
                            <th class="px-4 py-3">{{ __('messages.completed_at') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratings as $rating)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $ratings->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $rating->user?->name ?? '—' }}
                                <div class="text-xs text-gray-400" dir="ltr">{{ $rating->user?->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $rating->course?->name_ar ?? $rating->course?->name_en ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php $score = $rating->overallScore(); @endphp
                                @if($score !== null)
                                <span class="inline-flex items-center gap-1 font-bold text-amber-600">
                                    <i class="fas fa-star text-xs"></i> {{ $score }}
                                </span>
                                @else
                                —
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ optional($rating->completed_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('dashboard.academy.ratings.show', $rating) }}"
                                    class="text-indigo-600 hover:underline text-xs font-medium">{{ __('messages.view_answers') }}</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-8 text-gray-500 bg-gray-50">
                                {{ __('messages.no_ratings_yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $ratings->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection

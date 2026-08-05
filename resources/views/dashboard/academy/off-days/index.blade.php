@extends('layouts.app')

@section('title', __('messages.trainer_off_days_title'))

@section('content')
<section class="p-3 sm:p-6">
    <div class="mx-auto w-full max-w-3xl">
        <header class="mb-6 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-teal-50 text-teal-800 text-xs font-bold px-3 py-1 mb-3">
                <i class="fas fa-calendar-xmark"></i>
                {{ __('messages.trainer_off_days_badge') }}
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ __('messages.trainer_off_days_title') }}
            </h1>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed max-w-2xl mx-auto">
                {{ __('messages.trainer_off_days_hint') }}
            </p>
            <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 shadow-sm">
                <span class="text-xs font-semibold text-slate-500">{{ __('messages.trainer_off_days_count') }}</span>
                <span class="text-base font-black text-teal-700">{{ $days->total() }}</span>
            </div>
        </header>

        @if(session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center justify-center gap-2 text-center">
            <i class="fas fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 text-center">
            <ul class="list-none mb-0 space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm mb-5">
            <div class="px-5 sm:px-6 pt-5 pb-3 text-center border-b border-slate-100">
                <div class="mx-auto mb-2 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-teal-600 text-white text-sm">
                    <i class="fas fa-plus"></i>
                </div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('messages.trainer_off_days_add') }}</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ __('messages.trainer_off_days_add_hint') }}</p>
            </div>

            <form method="post" action="{{ route('dashboard.academy.off-days.store') }}" class="p-5 sm:p-6">
                @csrf
                <div class="grid sm:grid-cols-[200px_1fr_auto] gap-3 sm:gap-4 sm:items-end">
                    <div>
                        <label for="off_day_date" class="block text-sm font-bold text-slate-700 mb-1.5">
                            {{ __('messages.trainer_off_days_date') }}
                            <span class="text-rose-500">*</span>
                        </label>
                        <input id="off_day_date" type="date" name="date" required
                            value="{{ old('date') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        @error('date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="off_day_note" class="block text-sm font-bold text-slate-700 mb-1.5">
                            {{ __('messages.trainer_off_days_note') }}
                        </label>
                        <input id="off_day_note" type="text" name="note"
                            value="{{ old('note') }}"
                            placeholder="{{ __('messages.trainer_off_days_note_placeholder') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        @error('note')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                        style="background:linear-gradient(135deg,#0b8f7f,#0D2444);">
                        <i class="fas fa-calendar-plus"></i>
                        {{ __('messages.trainer_off_days_add') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 text-center">
                <h2 class="text-base font-extrabold text-slate-900 inline-flex items-center gap-2">
                    <i class="fas fa-list text-teal-600"></i>
                    {{ __('messages.trainer_off_days_list_title') }}
                </h2>
            </div>

            @forelse($days as $day)
            <div class="flex items-center justify-between gap-4 px-4 sm:px-6 py-3.5 border-t border-slate-100 hover:bg-slate-50/70 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-teal-50 text-teal-800 border border-teal-100">
                        <span class="text-[10px] font-bold leading-none">{{ $day->date?->translatedFormat('M') }}</span>
                        <span class="text-lg font-black leading-none mt-0.5">{{ $day->date?->format('d') }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 tabular-nums" dir="ltr">{{ $day->date?->format('Y-m-d') }}</p>
                        <p class="text-sm text-slate-500 mt-0.5 truncate">
                            {{ $day->note ?: __('messages.trainer_off_days_no_note') }}
                        </p>
                    </div>
                </div>

                <form method="post" action="{{ route('dashboard.academy.off-days.destroy', $day) }}"
                    onsubmit="return confirm(@json(__('messages.confirm_delete')))" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 border border-rose-100 hover:bg-rose-100 transition"
                        title="{{ __('messages.delete') }}">
                        <i class="fas fa-trash-can"></i>
                        <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                    </button>
                </form>
            </div>
            @empty
            <div class="px-5 py-12 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-xl">
                    <i class="fas fa-calendar-days"></i>
                </div>
                <p class="font-bold text-slate-800">{{ __('messages.trainer_off_days_empty_title') }}</p>
                <p class="text-sm text-slate-500 mt-1">{{ __('messages.trainer_off_days_empty_hint') }}</p>
            </div>
            @endforelse
        </div>

        @if($days->hasPages())
        <div class="mt-4 flex justify-center">{{ $days->links() }}</div>
        @endif
    </div>
</section>
@endsection

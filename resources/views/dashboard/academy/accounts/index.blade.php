@extends('layouts.app')

@section('title', $meta['title'])

@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="{{ __('messages.home') }}" link="{{ route($meta['route'] . '.index') }}" second="{{ $meta['title'] }}" />
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route($meta['route'] . '.index') }}" method="GET" class="flex items-center">
                        <label for="search" class="sr-only">{{ __('messages.search') }}</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input value="{{ $search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="{{ __('messages.search') }}">
                        </div>
                    </form>
                </div>
                <div class="w-full md:w-auto flex justify-end !ml-0">
                    <a href="{{ route($meta['route'] . '.create') }}"
                        class="inline-flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fas fa-plus ml-2"></i>
                        {{ $meta['create_label'] }}
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">{{ __('messages.name') }}</th>
                            <th class="px-4 py-3">{{ __('messages.email') }}</th>
                            <th class="px-4 py-3">{{ __('messages.phone') }}</th>
                            @if($meta['role'] === 'trainer')
                            <th class="px-4 py-3">{{ __('messages.trainer_review_list_category') }}</th>
                            <th class="px-4 py-3">{{ __('messages.trainer_review_list_lang') }}</th>
                            <th class="px-4 py-3">{{ __('messages.trainer_documents') }}</th>
                            @endif
                            <th class="px-4 py-3">{{ __('messages.status') }}</th>
                            <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $account)
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
                            $docsReady = $meta['role'] === 'trainer'
                                && $account->avatar
                                && $account->course_category_id
                                && filled($account->linkedin_url)
                                && filled($account->trainer_bio);
                            $teachLangShort = ($account->teaching_language ?? 'ar') === 'en'
                                ? __('messages.become_trainer_teaching_lang_en')
                                : __('messages.become_trainer_teaching_lang_ar');
                        @endphp
                        <tr class="border-b dark:border-gray-700 {{ $meta['role'] === 'trainer' && $account->status === 'pending' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    @if($meta['role'] === 'trainer')
                                    <img src="{{ $account->avatarUrl() }}" alt="" class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0">
                                    @endif
                                    <span>{{ $account->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $account->email }}</td>
                            <td class="px-4 py-3">
                                @if($account->phone)
                                <span dir="ltr" class="inline-block text-left" style="unicode-bidi: plaintext;">{{ $account->phone }}</span>
                                @else
                                —
                                @endif
                            </td>
                            @if($meta['role'] === 'trainer')
                            <td class="px-4 py-3">
                                {{ $account->courseCategory?->title(app()->getLocale()) ?: '—' }}
                            </td>
                            <td class="px-4 py-3">{{ $teachLangShort }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold {{ $docsReady ? 'bg-green-100 text-green-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $docsReady ? __('messages.trainer_review_docs_ready') : __('messages.trainer_review_docs_incomplete') }}
                                </span>
                            </td>
                            @endif
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <a href="{{ route($meta['route'] . '.show', $account) }}"
                                    class="block py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    title="{{ __('messages.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route($meta['route'] . '.edit', $account) }}"
                                    class="block py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route($meta['route'] . '.destroy', $account) }}" method="POST"
                                    class="inline js-delete-form"
                                    data-delete-name="{{ $account->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="block w-full text-right py-2 px-2 text-sm text-black hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                        title="{{ __('messages.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $meta['role'] === 'trainer' ? 9 : 6 }}" class="text-center px-4 py-8 text-gray-500 bg-gray-50">
                                {{ $meta['empty'] }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</section>

<div id="deleteConfirmModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative mx-auto mt-24 w-[92%] max-w-md rounded-xl bg-white shadow-2xl">
        <div class="border-b px-5 py-4">
            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.confirm_delete') }}</h3>
        </div>
        <div class="px-5 py-4 text-sm text-gray-600">
            <p id="deleteConfirmText">{{ __('messages.confirm_delete') }}</p>
        </div>
        <div class="flex items-center justify-end gap-3 border-t px-5 py-4">
            <button type="button" id="deleteConfirmCancel"
                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                {{ __('messages.cancel') }}
            </button>
            <button type="button" id="deleteConfirmSubmit"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                {{ __('messages.delete') }}
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deleteConfirmModal');
        const text = document.getElementById('deleteConfirmText');
        const cancelBtn = document.getElementById('deleteConfirmCancel');
        const submitBtn = document.getElementById('deleteConfirmSubmit');
        let activeForm = null;

        document.querySelectorAll('.js-delete-form').forEach((form) => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                activeForm = form;
                const name = form.dataset.deleteName || '';
                text.textContent = name
                    ? `{{ __('messages.confirm_delete') }}: ${name}`
                    : `{{ __('messages.confirm_delete') }}`;
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        function closeModal() {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            activeForm = null;
        }

        cancelBtn.addEventListener('click', closeModal);
        modal.querySelector('.absolute.inset-0').addEventListener('click', closeModal);
        submitBtn.addEventListener('click', function () {
            if (activeForm) activeForm.submit();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endsection

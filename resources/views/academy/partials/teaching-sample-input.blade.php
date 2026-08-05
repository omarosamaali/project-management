@php
    $sampleType = old('teaching_sample_type', $sampleType ?? 'upload');
    if (! in_array($sampleType, ['upload', 'link'], true)) {
        $sampleType = 'upload';
    }
    $existingSampleUrl = $existingSampleUrl ?? null;
    $existingIsExternal = (bool) ($existingIsExternal ?? false);
    $inputId = $inputId ?? 'teaching_sample';
    $variant = $variant ?? 'become'; // become | register | profile
@endphp

<div class="teaching-sample-field" data-teaching-sample>
    <div class="teaching-sample-modes" role="tablist" aria-label="{{ __('messages.trainer_sample') }}">
        <label class="teaching-sample-mode {{ $sampleType === 'upload' ? 'is-active' : '' }}">
            <input type="radio" name="teaching_sample_type" value="upload" {{ $sampleType === 'upload' ? 'checked' : '' }}>
            <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
            <span>{{ __('messages.trainer_sample_mode_upload') }}</span>
        </label>
        <label class="teaching-sample-mode {{ $sampleType === 'link' ? 'is-active' : '' }}">
            <input type="radio" name="teaching_sample_type" value="link" {{ $sampleType === 'link' ? 'checked' : '' }}>
            <i class="fas fa-link" aria-hidden="true"></i>
            <span>{{ __('messages.trainer_sample_mode_link') }}</span>
        </label>
    </div>

    <div class="teaching-sample-panel" data-sample-panel="upload" {{ $sampleType === 'upload' ? '' : 'hidden' }}>
        @if($variant === 'become')
        <div class="bt-file-dropzone" data-file-dropzone="{{ $inputId }}">
            <input type="file" id="{{ $inputId }}" name="teaching_sample" accept="video/mp4,.mp4">
            <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
            <p>{{ __('messages.trainer_sample_drop') }}</p>
            <small>{{ __('messages.trainer_sample_formats') }}</small>
            <span class="bt-file-name" data-file-name></span>
        </div>
        @else
        <input id="{{ $inputId }}" type="file" name="teaching_sample" accept="video/mp4,.mp4"
            class="block mt-1 w-full text-sm rounded-xl border border-slate-300 bg-white px-3 py-2.5">
        @endif
        <p class="text-[11px] text-slate-400 mt-1">{{ __('messages.trainer_sample_hint_optional') }}</p>
    </div>

    <div class="teaching-sample-panel" data-sample-panel="link" {{ $sampleType === 'link' ? '' : 'hidden' }}>
        <input type="url" name="teaching_sample_link" id="{{ $inputId }}_link" dir="ltr"
            value="{{ old('teaching_sample_link', $existingIsExternal ? $existingSampleUrl : '') }}"
            placeholder="{{ __('messages.trainer_sample_link_placeholder') }}"
            class="block mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
        <p class="text-[11px] text-slate-400 mt-1">{{ __('messages.trainer_sample_link_hint') }}</p>
    </div>

    @if($existingSampleUrl)
    <a href="{{ $existingSampleUrl }}" target="_blank" rel="noopener"
        class="text-xs text-teal-700 font-bold mt-2 inline-flex items-center gap-1.5 hover:underline">
        <i class="fas {{ $existingIsExternal ? 'fa-external-link-alt' : 'fa-play-circle' }}"></i>
        {{ __('messages.trainer_review_open_sample') }}
        @if($existingIsExternal)
        <span class="text-slate-400 font-medium">({{ __('messages.trainer_sample_mode_link') }})</span>
        @endif
    </a>
    @endif

    <x-input-error :messages="$errors->get('teaching_sample')" class="mt-2" />
    <x-input-error :messages="$errors->get('teaching_sample_link')" class="mt-2" />
    <x-input-error :messages="$errors->get('teaching_sample_type')" class="mt-2" />
</div>

@once
<style>
    .teaching-sample-modes {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem;
        margin: .75rem 0 1rem;
    }
    .teaching-sample-mode {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .7rem .85rem;
        border-radius: .85rem;
        border: 1.5px solid #d5dee8;
        background: #fff;
        color: #475569;
        font-size: .82rem;
        font-weight: 800;
        cursor: pointer;
        transition: border-color .18s, background .18s, color .18s, box-shadow .18s;
    }
    .teaching-sample-mode input { position: absolute; opacity: 0; pointer-events: none; }
    .teaching-sample-mode i { font-size: .9rem; opacity: .85; }
    .teaching-sample-mode:hover { border-color: #94a3b8; }
    .teaching-sample-mode.is-active {
        border-color: #0b8f7f;
        background: rgba(11, 143, 127, .08);
        color: #0b5f55;
        box-shadow: 0 0 0 3px rgba(11, 143, 127, .12);
    }
</style>
<script>
(() => {
    const bindTeachingSample = (root) => {
        if (!root || root.dataset.bound === '1') return;
        root.dataset.bound = '1';
        const radios = root.querySelectorAll('input[name="teaching_sample_type"]');
        const panels = {
            upload: root.querySelector('[data-sample-panel="upload"]'),
            link: root.querySelector('[data-sample-panel="link"]'),
        };
        const sync = () => {
            const type = root.querySelector('input[name="teaching_sample_type"]:checked')?.value || 'upload';
            root.querySelectorAll('.teaching-sample-mode').forEach((el) => {
                el.classList.toggle('is-active', el.querySelector('input')?.value === type);
            });
            Object.entries(panels).forEach(([key, panel]) => {
                if (!panel) return;
                panel.hidden = key !== type;
                panel.querySelectorAll('input').forEach((input) => {
                    if (key === type) {
                        input.removeAttribute('disabled');
                    } else {
                        input.setAttribute('disabled', 'disabled');
                        if (input.type === 'file') input.value = '';
                    }
                });
            });
        };
        radios.forEach((radio) => radio.addEventListener('change', sync));
        sync();
    };

    const init = () => document.querySelectorAll('[data-teaching-sample]').forEach(bindTeachingSample);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endonce

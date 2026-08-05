{{-- Academy-themed multi-select for trainer off days --}}
<style>
    .rs-multi {
        --rs-teal: #0b8f7f;
        --rs-teal-soft: #e6f7f4;
        --rs-teal-mid: #0e6e63;
        --rs-ink: #061525;
        --rs-line: #d4e0ec;
        --rs-muted: #5a6d82;
        position: relative;
        font-size: 14px;
        color: var(--rs-ink);
        direction: rtl;
    }
    .rs-multi__control {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        min-height: 44px;
        padding: 4px 10px;
        background: #fff;
        border: 1.5px solid var(--rs-line);
        border-radius: 12px;
        cursor: pointer;
        transition: border-color 140ms, box-shadow 140ms;
    }
    .rs-multi__control:hover {
        border-color: #a8c5bf;
    }
    .rs-multi.is-focused .rs-multi__control {
        border-color: var(--rs-teal);
        box-shadow: 0 0 0 3px rgba(11, 143, 127, 0.18);
        outline: none;
    }
    .rs-multi.is-disabled .rs-multi__control {
        background: #f1f5f9;
        cursor: default;
    }
    .rs-multi__value-container {
        display: flex;
        flex: 1;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        min-width: 0;
        padding: 2px 0;
    }
    .rs-multi__placeholder {
        color: var(--rs-muted);
        margin: 0 2px;
        padding: 2px 0;
        font-weight: 500;
    }
    .rs-multi__multi-value {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        background: var(--rs-teal-soft);
        border: 1px solid rgba(11, 143, 127, 0.28);
        border-radius: 8px;
        margin: 2px;
        box-sizing: border-box;
    }
    .rs-multi__multi-value__label {
        padding: 4px 8px;
        font-size: 85%;
        font-weight: 700;
        color: var(--rs-teal-mid);
        border-radius: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .rs-multi__multi-value__remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        color: var(--rs-teal);
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        border: 0;
        background: transparent;
        line-height: 1;
        font-weight: 700;
    }
    .rs-multi__multi-value__remove:hover {
        background: rgba(232, 93, 76, 0.15);
        color: #c2410c;
    }
    .rs-multi__indicators {
        display: flex;
        align-items: center;
        align-self: stretch;
        flex-shrink: 0;
    }
    .rs-multi__indicator-separator {
        width: 1px;
        align-self: stretch;
        background: var(--rs-line);
        margin: 8px 0;
    }
    .rs-multi__dropdown-indicator {
        display: flex;
        padding: 8px;
        color: var(--rs-teal);
    }
    .rs-multi__clear-indicator {
        display: flex;
        padding: 8px;
        color: var(--rs-muted);
        cursor: pointer;
        border: 0;
        background: transparent;
    }
    .rs-multi__clear-indicator:hover,
    .rs-multi__dropdown-indicator:hover {
        color: var(--rs-ink);
    }
    .rs-multi__menu {
        position: absolute;
        z-index: 40;
        top: calc(100% + 6px);
        right: 0;
        left: 0;
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--rs-line);
        box-shadow: 0 12px 28px rgba(6, 21, 37, 0.12);
        margin-top: 0;
        overflow: hidden;
    }
    .rs-multi__menu-list {
        max-height: 220px;
        overflow-y: auto;
        padding: 6px 0;
    }
    .rs-multi__option {
        display: block;
        width: 100%;
        text-align: right;
        padding: 9px 14px;
        background: transparent;
        border: 0;
        color: var(--rs-ink);
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }
    .rs-multi__option:hover,
    .rs-multi__option.is-focused {
        background: var(--rs-teal-soft);
        color: var(--rs-teal-mid);
    }
    .rs-multi__option.is-selected {
        background: linear-gradient(135deg, #0b8f7f, #0D2444);
        color: #fff;
    }
    .rs-multi__option.is-disabled {
        color: #94a3b8;
        cursor: default;
    }
    .rs-multi__menu-notice {
        padding: 10px 14px;
        color: var(--rs-muted);
        text-align: center;
        font-size: 13px;
        font-weight: 600;
    }
    .rs-multi__hidden-inputs {
        display: none;
    }
    .rs-multi__empty {
        border: 1.5px dashed rgba(11, 143, 127, 0.35);
        background: var(--rs-teal-soft);
        border-radius: 12px;
        padding: 14px 16px;
        text-align: center;
        color: var(--rs-teal-mid);
        font-size: 13px;
        line-height: 1.6;
        font-weight: 600;
    }
    .rs-multi__empty a {
        color: var(--rs-ink);
        font-weight: 800;
        text-decoration: underline;
        text-underline-offset: 2px;
    }
</style>

<div class="mb-6" id="trainer-off-days-select-wrap">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ __('messages.course_off_days_label') }}
    </label>
    <p class="text-xs text-gray-500 mb-3">
        {{ __('messages.course_off_days_hint') }}
    </p>

    <div id="trainer-off-days-empty" class="rs-multi__empty hidden">
        {{ __('messages.course_off_days_empty') }}
        @if(auth()->user()?->isTrainer())
        <div class="mt-2">
            <a href="{{ route('dashboard.academy.off-days.index') }}">{{ __('messages.course_off_days_empty_link') }}</a>
        </div>
        @endif
    </div>

    <div id="trainer-off-days-rs" class="rs-multi hidden" tabindex="0">
        <div class="rs-multi__control" data-rs-control>
            <div class="rs-multi__value-container" data-rs-values>
                <span class="rs-multi__placeholder" data-rs-placeholder>{{ __('messages.course_off_days_placeholder') }}</span>
            </div>
            <div class="rs-multi__indicators">
                <button type="button" class="rs-multi__clear-indicator hidden" data-rs-clear title="Clear" aria-label="Clear">
                    <svg height="20" width="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                        <path d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.029-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z" fill="currentColor"></path>
                    </svg>
                </button>
                <span class="rs-multi__indicator-separator" data-rs-sep></span>
                <div class="rs-multi__dropdown-indicator" data-rs-dropdown>
                    <svg height="20" width="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                        <path d="M4.516 7.548c0.436-0.446 1.043-0.481 1.576 0l3.908 3.747 3.908-3.747c0.533-0.481 1.141-0.446 1.574 0 0.436 0.445 0.408 1.197 0 1.615-0.406 0.418-4.695 4.502-4.695 4.502-0.217 0.223-0.502 0.335-0.787 0.335s-0.57-0.112-0.789-0.335c0 0-4.287-4.084-4.695-4.502s-0.436-1.17 0-1.615z" fill="currentColor"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="rs-multi__menu hidden" data-rs-menu>
            <div class="rs-multi__menu-list" data-rs-menu-list></div>
        </div>
        <div class="rs-multi__hidden-inputs" data-rs-inputs></div>
    </div>
</div>

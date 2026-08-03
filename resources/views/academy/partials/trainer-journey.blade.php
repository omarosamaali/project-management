{{--
  Shared trainer journey stepper.
  Props: $journeyStep (int), $completedSteps (int), $allDone (bool, optional), $journeyHint (string, optional)
--}}
@php
    $journeyStep = (int) ($journeyStep ?? 1);
    $completedSteps = (int) ($completedSteps ?? max(0, $journeyStep - 1));
    $allDone = (bool) ($allDone ?? false);
    $journeyHint = $journeyHint ?? \App\Support\TrainerJourney::hintFor($journeyStep, $allDone);
    $journeySteps = [
        1 => [
            'icon' => 'fa-paper-plane',
            'label' => __('messages.become_trainer_step_apply'),
            'sub' => __('messages.become_trainer_step_apply_sub'),
        ],
        2 => [
            'icon' => 'fa-clipboard-check',
            'label' => __('messages.become_trainer_step_review'),
            'sub' => null,
        ],
        3 => [
            'icon' => 'fa-circle-check',
            'label' => __('messages.become_trainer_step_approve'),
            'sub' => null,
        ],
        4 => [
            'icon' => 'fa-chalkboard',
            'label' => __('messages.become_trainer_step_create'),
            'sub' => null,
        ],
        5 => [
            'icon' => 'fa-rocket',
            'label' => __('messages.become_trainer_step_publish'),
            'sub' => __('messages.become_trainer_step_publish_sub'),
        ],
    ];
@endphp

@once
<style>
    .tj-wrap {
        --tj-ink: #061525;
        --tj-muted: #5a6d82;
        --tj-sand: #f0f4f8;
        --tj-line: #d4e0ec;
        --tj-action: #ff3d7a;
        --tj-action-deep: #e11d62;
        --tj-notch: 14px;
        --tj-ease: cubic-bezier(.22, .8, .28, 1);
        --tj-font: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        --tj-display: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        font-family: var(--tj-font);
    }
    .tj-card {
        background: #fff;
        border: 1px solid var(--tj-line);
        border-radius: 1.35rem;
        box-shadow: 0 22px 48px rgba(6, 21, 37, .12);
        padding: clamp(1.25rem, 3vw, 1.85rem);
    }
    .tj-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .tj-head h2 {
        margin: 0;
        font-family: var(--tj-display);
        font-size: clamp(1.2rem, 2.2vw, 1.55rem);
        font-weight: 800;
        color: var(--tj-ink);
    }
    .tj-count {
        font-size: .88rem;
        font-weight: 700;
        color: var(--tj-muted);
        background: var(--tj-sand);
        padding: .4rem .85rem;
        border-radius: 999px;
    }
    .tj-track {
        display: flex;
        align-items: stretch;
        width: 100%;
        margin-bottom: 1rem;
        direction: rtl;
    }
    .tj-step {
        position: relative;
        flex: 1 1 0;
        min-width: 0;
        min-height: 5.1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .28rem;
        padding: .85rem calc(var(--tj-notch) + .55rem) .85rem calc(var(--tj-notch) + .35rem);
        margin-inline-end: calc(var(--tj-notch) * -0.72);
        text-align: center;
        color: #6b7f94;
        background: #cfd9e6;
        z-index: 1;
        clip-path: polygon(
            var(--tj-notch) 0%,
            100% 0%,
            calc(100% - var(--tj-notch)) 50%,
            100% 100%,
            var(--tj-notch) 100%,
            0% 50%
        );
        transition: background .35s var(--tj-ease), color .35s var(--tj-ease), transform .35s var(--tj-ease), filter .35s var(--tj-ease);
    }
    .tj-step:first-child {
        z-index: 5;
        clip-path: polygon(var(--tj-notch) 0%, 100% 0%, 100% 100%, var(--tj-notch) 100%, 0% 50%);
    }
    .tj-step:nth-child(2) { z-index: 4; }
    .tj-step:nth-child(3) { z-index: 3; }
    .tj-step:nth-child(4) { z-index: 2; }
    .tj-step:last-child { z-index: 1; margin-inline-end: 0; }
    .tj-step__icon {
        width: 1.55rem; height: 1.55rem;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .95rem; line-height: 1; opacity: .95;
    }
    .tj-step__label {
        font-size: clamp(.72rem, 1.35vw, .84rem);
        font-weight: 800; line-height: 1.25; max-width: 100%;
    }
    .tj-step__sub { font-size: .68rem; font-weight: 600; opacity: .88; line-height: 1.2; }
    .tj-step.is-locked .tj-step__icon { opacity: .7; }
    .tj-step.is-link {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }
    .tj-step.is-link:hover {
        filter: brightness(1.04);
    }
    .tj-step.is-link.is-done:hover,
    .tj-step.is-link.is-active:hover {
        transform: translateY(-3px);
    }
    .tj-step.is-locked {
        cursor: not-allowed;
        pointer-events: none;
    }
    .tj-step.is-done {
        background: linear-gradient(135deg, #12c8a0, #0b8f7f);
        color: #fff;
    }
    .tj-step.is-active {
        background: linear-gradient(135deg, var(--tj-action), var(--tj-action-deep));
        color: #fff;
        z-index: 6;
        filter: drop-shadow(0 10px 18px rgba(255, 61, 122, .28));
        transform: translateY(-2px);
    }
    [dir="ltr"] .tj-track { direction: ltr; }
    [dir="ltr"] .tj-step {
        padding: .85rem calc(var(--tj-notch) + .35rem) .85rem calc(var(--tj-notch) + .55rem);
        margin-inline-end: 0;
        margin-inline-start: calc(var(--tj-notch) * -0.72);
        clip-path: polygon(0% 0%, calc(100% - var(--tj-notch)) 0%, 100% 50%, calc(100% - var(--tj-notch)) 100%, 0% 100%, var(--tj-notch) 50%);
    }
    [dir="ltr"] .tj-step:first-child {
        margin-inline-start: 0;
        clip-path: polygon(0% 0%, calc(100% - var(--tj-notch)) 0%, 100% 50%, calc(100% - var(--tj-notch)) 100%, 0% 100%);
    }
    @media (max-width: 820px) {
        .tj-track { flex-direction: column; gap: .45rem; direction: inherit; }
        .tj-step,
        .tj-step:last-child,
        [dir="ltr"] .tj-step,
        [dir="ltr"] .tj-step:first-child {
            margin: 0;
            min-height: 3.6rem;
            padding: .75rem 1rem;
            border-radius: .85rem;
            clip-path: none;
            flex-direction: row;
            justify-content: flex-start;
            gap: .75rem;
            text-align: start;
            filter: none;
            box-shadow: inset 0 0 0 1px rgba(6, 21, 37, .04);
        }
        .tj-step.is-active { transform: none; box-shadow: 0 10px 22px rgba(255, 61, 122, .22); }
        .tj-step__text { display: flex; flex-direction: column; gap: .1rem; }
    }
    .tj-hint { margin: 0; color: var(--tj-muted); font-size: .9rem; line-height: 1.6; }
</style>
@endonce

<div class="tj-wrap" data-trainer-journey data-journey-step="{{ $journeyStep }}" data-journey-completed="{{ $completedSteps }}" data-journey-all-done="{{ $allDone ? '1' : '0' }}">
    <div class="tj-card">
        <div class="tj-head">
            <h2>{{ __('messages.become_trainer_journey_title') }}</h2>
            <span class="tj-count" data-journey-count>
                {{ __('messages.become_trainer_journey_progress', ['done' => $completedSteps, 'total' => 5]) }}
            </span>
        </div>
        <div class="tj-track" role="list" aria-label="{{ __('messages.become_trainer_journey_title') }}" data-journey-track>
            @foreach ($journeySteps as $step => $meta)
            @php
                if ($allDone) {
                    $state = 'is-done';
                } elseif ($step < $journeyStep) {
                    $state = 'is-done';
                } elseif ($step === $journeyStep) {
                    $state = 'is-active';
                } else {
                    $state = 'is-locked';
                }
                $icon = $state === 'is-done'
                    ? 'fa-check'
                    : ($state === 'is-locked' ? 'fa-lock' : $meta['icon']);
                $stepUrl = \App\Support\TrainerJourney::urlForStep($step, auth()->user());
                $canOpen = $state !== 'is-locked'
                    && $stepUrl
                    && \App\Support\TrainerJourney::stepIsAvailable($step, $journeyStep, $allDone);
                $tag = $canOpen ? 'a' : 'div';
            @endphp
            <{{ $tag }}
                @if($canOpen) href="{{ $stepUrl }}" @endif
                class="tj-step {{ $state }} {{ $canOpen ? 'is-link' : '' }}"
                role="listitem"
                data-journey-item="{{ $step }}"
                @if($canOpen) data-journey-href="{{ $stepUrl }}" @endif
                aria-current="{{ $state === 'is-active' ? 'step' : 'false' }}"
                @if(! $canOpen) aria-disabled="true" @endif
            >
                <span class="tj-step__icon" aria-hidden="true"><i class="fas {{ $icon }}"></i></span>
                <div class="tj-step__text">
                    <span class="tj-step__label">{{ $meta['label'] }}</span>
                    @if ($meta['sub'] && ($state === 'is-active' || ($step === 5 && ! $allDone)))
                    <span class="tj-step__sub">{{ $meta['sub'] }}</span>
                    @endif
                </div>
            </{{ $tag }}>
            @endforeach
        </div>
        <p class="tj-hint" data-journey-hint>{{ $journeyHint }}</p>
    </div>
</div>

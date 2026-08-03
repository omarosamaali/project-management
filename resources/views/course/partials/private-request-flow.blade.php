{{-- Trainee private-course flow stepper. Pass $activeStep (1-5) and optional $locale. --}}
@php
    $locale = $locale ?? app()->getLocale();
    $activeStep = (int) ($activeStep ?? 1);
    $steps = [
        1 => [
            'title' => __('messages.private_flow_step_1_title'),
            'body' => __('messages.private_flow_step_1_body'),
            'icon' => 'fa-paper-plane',
        ],
        2 => [
            'title' => __('messages.private_flow_step_2_title'),
            'body' => __('messages.private_flow_step_2_body'),
            'icon' => 'fa-calendar-check',
        ],
        3 => [
            'title' => __('messages.private_flow_step_3_title'),
            'body' => __('messages.private_flow_step_3_body'),
            'icon' => 'fa-handshake',
        ],
        4 => [
            'title' => __('messages.private_flow_step_4_title'),
            'body' => __('messages.private_flow_step_4_body'),
            'icon' => 'fa-credit-card',
        ],
        5 => [
            'title' => __('messages.private_flow_step_5_title'),
            'body' => __('messages.private_flow_step_5_body'),
            'icon' => 'fa-graduation-cap',
        ],
    ];
@endphp
<div class="pr-flow" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="pr-flow-head">
        <h2>{{ __('messages.private_flow_title') }}</h2>
        <p>{{ __('messages.private_flow_sub') }}</p>
    </div>
    <ol class="pr-flow-steps">
        @foreach($steps as $num => $step)
        @php
            $state = $num < $activeStep ? 'done' : ($num === $activeStep ? 'active' : 'todo');
        @endphp
        <li class="pr-flow-step is-{{ $state }}">
            <span class="pr-flow-badge" aria-hidden="true">
                @if($state === 'done')
                <i class="fas fa-check"></i>
                @else
                <i class="fas {{ $step['icon'] }}"></i>
                @endif
            </span>
            <div class="pr-flow-copy">
                <span class="pr-flow-num">{{ $locale === 'ar' ? 'الخطوة '.$num : 'Step '.$num }}</span>
                <strong>{{ $step['title'] }}</strong>
                <p>{{ $step['body'] }}</p>
            </div>
        </li>
        @endforeach
    </ol>
</div>

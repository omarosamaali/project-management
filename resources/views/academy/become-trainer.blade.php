@extends('layouts.user')

@section('title', __('messages.become_trainer_title'))

@section('content')
@include('academy.partials.styles')

@php
    $completedSteps = (int) ($completedSteps ?? max(0, (int) $journeyStep - 1));
    $allDone = (bool) ($allDone ?? false);
    $journeyHint = $journeyHint ?? \App\Support\TrainerJourney::hintFor((int) $journeyStep, $allDone);
    $showForm = ! $formBlocked;
    $openFormOnLoad = $errors->any() || old('role') === 'trainer';
@endphp

<style>
    .bt-page {
        --bt-card: #fff;
        --bt-soft: #f7fafc;
        --bt-ease: cubic-bezier(.22, .8, .28, 1);
        --bt-chevron-notch: 14px;
    }
    .bt-page a,
    .bt-page button,
    .bt-page summary,
    .bt-page .bt-btn,
    .bt-page .academy-cta,
    .bt-page .bt-look-item,
    .bt-page .bt-process-card,
    .bt-page .bt-faq-item,
    .bt-page .bt-faq-q,
    .bt-page .drag-image-dropzone,
    .bt-page .bt-chevron {
        transition:
            background-color .35s var(--bt-ease),
            background .35s var(--bt-ease),
            color .35s var(--bt-ease),
            border-color .35s var(--bt-ease),
            box-shadow .35s var(--bt-ease),
            transform .35s var(--bt-ease),
            opacity .35s var(--bt-ease),
            filter .35s var(--bt-ease);
    }

    .bt-hero {
        text-align: center;
        padding: clamp(2.75rem, 6vw, 4.5rem) clamp(1rem, 3vw, 2rem) clamp(1.5rem, 3vw, 2rem);
        max-width: var(--page-max);
        margin: 0 auto;
    }
    .bt-hero h1 {
        font-size: clamp(1.85rem, 4vw, 2.85rem);
        line-height: 1.2;
        margin: .35rem 0 .85rem;
        color: var(--ink);
    }
    .bt-hero p {
        max-width: 40rem;
        margin: 0 auto 1.35rem;
        color: var(--muted);
        line-height: 1.7;
        font-size: 1.02rem;
    }
    .bt-card {
        background: var(--bt-card);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: clamp(1.25rem, 3vw, 1.85rem);
    }
    .bt-journey-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .bt-journey-head h2 { margin: 0; font-size: clamp(1.2rem, 2.2vw, 1.55rem); color: var(--ink); }
    .bt-journey-count {
        font-size: .88rem;
        font-weight: 700;
        color: var(--muted);
        background: var(--sand);
        padding: .4rem .85rem;
        border-radius: 999px;
    }

    /* Interlocking chevron journey (easyT-style) */
    .bt-chevron-track {
        display: flex;
        align-items: stretch;
        width: 100%;
        margin-bottom: 1rem;
        direction: rtl;
    }
    .bt-chevron {
        --bg: #d8e2ec;
        --fg: #5a6d82;
        position: relative;
        flex: 1 1 0;
        min-width: 0;
        min-height: 5.1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .28rem;
        padding: .85rem calc(var(--bt-chevron-notch) + .55rem) .85rem calc(var(--bt-chevron-notch) + .35rem);
        margin-inline-end: calc(var(--bt-chevron-notch) * -0.72);
        text-align: center;
        color: var(--fg);
        background: var(--bg);
        z-index: 1;
        clip-path: polygon(
            var(--bt-chevron-notch) 0%,
            100% 0%,
            calc(100% - var(--bt-chevron-notch)) 50%,
            100% 100%,
            var(--bt-chevron-notch) 100%,
            0% 50%
        );
        filter: drop-shadow(0 1px 0 rgba(6, 21, 37, .04));
    }
    .bt-chevron:first-child {
        z-index: 5;
        clip-path: polygon(
            var(--bt-chevron-notch) 0%,
            100% 0%,
            100% 100%,
            var(--bt-chevron-notch) 100%,
            0% 50%
        );
    }
    .bt-chevron:nth-child(2) { z-index: 4; }
    .bt-chevron:nth-child(3) { z-index: 3; }
    .bt-chevron:nth-child(4) { z-index: 2; }
    .bt-chevron:last-child {
        z-index: 1;
        margin-inline-end: 0;
    }
    .bt-chevron__icon {
        width: 1.55rem;
        height: 1.55rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .95rem;
        line-height: 1;
        opacity: .95;
    }
    .bt-chevron__label {
        font-size: clamp(.72rem, 1.35vw, .84rem);
        font-weight: 800;
        line-height: 1.25;
        max-width: 100%;
    }
    .bt-chevron__sub {
        font-size: .68rem;
        font-weight: 600;
        opacity: .88;
        line-height: 1.2;
    }
    .bt-chevron.is-locked {
        --bg: #cfd9e6;
        --fg: #6b7f94;
    }
    .bt-chevron.is-locked .bt-chevron__icon { opacity: .7; }
    .bt-chevron.is-done {
        --bg: linear-gradient(135deg, #12c8a0, #0b8f7f);
        --fg: #fff;
        background: linear-gradient(135deg, #12c8a0, #0b8f7f);
        color: #fff;
    }
    .bt-chevron.is-active {
        --bg: linear-gradient(135deg, var(--action), var(--action-deep));
        --fg: #fff;
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        color: #fff;
        z-index: 6;
        filter: drop-shadow(0 10px 18px rgba(255, 61, 122, .28));
        transform: translateY(-2px);
    }
    .bt-chevron.is-active .bt-chevron__sub { opacity: .95; }
    [dir="ltr"] .bt-chevron-track { direction: ltr; }
    [dir="ltr"] .bt-chevron {
        padding: .85rem calc(var(--bt-chevron-notch) + .35rem) .85rem calc(var(--bt-chevron-notch) + .55rem);
        margin-inline-end: 0;
        margin-inline-start: calc(var(--bt-chevron-notch) * -0.72);
        clip-path: polygon(
            0% 0%,
            calc(100% - var(--bt-chevron-notch)) 0%,
            100% 50%,
            calc(100% - var(--bt-chevron-notch)) 100%,
            0% 100%,
            var(--bt-chevron-notch) 50%
        );
    }
    [dir="ltr"] .bt-chevron:first-child {
        margin-inline-start: 0;
        clip-path: polygon(
            0% 0%,
            calc(100% - var(--bt-chevron-notch)) 0%,
            100% 50%,
            calc(100% - var(--bt-chevron-notch)) 100%,
            0% 100%
        );
    }
    [dir="ltr"] .bt-chevron:last-child {
        clip-path: polygon(
            0% 0%,
            calc(100% - var(--bt-chevron-notch)) 0%,
            100% 50%,
            calc(100% - var(--bt-chevron-notch)) 100%,
            0% 100%,
            var(--bt-chevron-notch) 50%
        );
    }
    @media (max-width: 820px) {
        .bt-chevron-track {
            flex-direction: column;
            gap: .45rem;
            direction: inherit;
        }
        .bt-chevron,
        .bt-chevron:last-child,
        [dir="ltr"] .bt-chevron,
        [dir="ltr"] .bt-chevron:first-child,
        [dir="ltr"] .bt-chevron:last-child {
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
        .bt-chevron.is-active {
            transform: none;
            box-shadow: 0 10px 22px rgba(255, 61, 122, .22);
        }
        .bt-chevron__text { display: flex; flex-direction: column; gap: .1rem; }
    }

    .bt-journey-hint { margin: 0; color: var(--muted); font-size: .9rem; line-height: 1.6; }

    .bt-look-list { display: grid; gap: .9rem; margin-top: 1.5rem; }
    .bt-look-item {
        --look-accent: var(--action);
        position: relative;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem 1.15rem;
        align-items: center;
        padding: 1.15rem 1.25rem 1.15rem 1.35rem;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgba(6, 21, 37, .08);
        border-radius: 1.1rem;
        background:
            linear-gradient(135deg, rgba(255, 61, 122, .05), transparent 42%),
            #fff;
        box-shadow: 0 10px 28px rgba(6, 21, 37, .05);
    }
    .bt-look-item::before {
        content: '';
        position: absolute;
        inset-block: .85rem;
        inset-inline-start: 0;
        width: 4px;
        border-radius: 999px;
        background: var(--look-accent);
        box-shadow: 0 0 16px rgba(255, 61, 122, .35);
    }
    .bt-look-item::after {
        content: attr(data-step);
        position: absolute;
        inset-inline-end: .35rem;
        inset-block-end: -.35rem;
        font-family: var(--display);
        font-size: clamp(3.4rem, 8vw, 4.6rem);
        font-weight: 800;
        line-height: 1;
        color: rgba(255, 61, 122, .07);
        pointer-events: none;
        z-index: 0;
        user-select: none;
    }
    .bt-look-item:nth-child(2) { --look-accent: #e11d62; }
    .bt-look-item:nth-child(3) { --look-accent: #ff3d7a; }
    .bt-look-item:nth-child(4) { --look-accent: #ff4d6d; }
    .bt-look-item:nth-child(5) { --look-accent: #e11d62; }
    .bt-look-item:hover {
        transform: translateY(-3px);
        border-color: rgba(255, 61, 122, .28);
        box-shadow: 0 16px 34px rgba(255, 61, 122, .12);
        background:
            linear-gradient(135deg, rgba(255, 61, 122, .1), transparent 48%),
            #fff;
    }
    .bt-look-item:hover::after { color: rgba(255, 61, 122, .11); }
    .bt-look-num {
        position: relative;
        z-index: 1;
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 999px;
        display: inline-grid;
        place-items: center;
        background: var(--action);
        color: #fff;
        font-weight: 800;
        font-size: .95rem;
        line-height: 1;
        flex-shrink: 0;
        font-variant-numeric: tabular-nums;
        font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        box-shadow: 0 8px 18px rgba(255, 61, 122, .35);
    }
    .bt-look-body { position: relative; z-index: 1; min-width: 0; }
    .bt-look-item h3 { margin: 0 0 .3rem; font-size: 1.02rem; color: var(--ink); font-weight: 800; }
    .bt-look-item p { margin: 0; color: var(--muted); font-size: .9rem; line-height: 1.65; }

    .bt-look-shell {
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
    }
    .bt-look-shell .bt-look-head {
        text-align: center;
        max-width: 40rem;
        margin: 0 auto 0.25rem;
    }

    .bt-process-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.35rem;
    }
    @media (max-width: 800px) {
        .bt-process-grid { grid-template-columns: 1fr; }
    }
    .bt-process-card {
        --process-accent: var(--action);
        position: relative;
        overflow: hidden;
        isolation: isolate;
        background:
            linear-gradient(145deg, rgba(255, 61, 122, .07), transparent 48%),
            #fff;
        border: 1px solid rgba(255, 61, 122, .14);
        border-radius: 1.1rem;
        padding: 1.35rem 1.2rem 1.3rem;
        box-shadow: 0 12px 28px rgba(255, 61, 122, .06);
    }
    .bt-process-card:nth-child(2) { --process-accent: #e11d62; }
    .bt-process-card:nth-child(3) { --process-accent: #ff4d6d; }
    .bt-process-card::before {
        content: '';
        position: absolute;
        inset-block: .9rem;
        inset-inline-start: 0;
        width: 4px;
        border-radius: 999px;
        background: var(--process-accent);
        box-shadow: 0 0 14px rgba(255, 61, 122, .3);
    }
    .bt-process-card::after {
        content: attr(data-step);
        position: absolute;
        inset-inline-end: .2rem;
        inset-block-end: -.4rem;
        font-family: var(--display);
        font-size: clamp(3.2rem, 7vw, 4.4rem);
        font-weight: 800;
        line-height: 1;
        color: rgba(255, 61, 122, .08);
        pointer-events: none;
        z-index: 0;
        user-select: none;
    }
    .bt-process-card:hover {
        transform: translateY(-3px);
        border-color: rgba(255, 61, 122, .3);
        box-shadow: 0 16px 34px rgba(255, 61, 122, .14);
        background:
            linear-gradient(145deg, rgba(255, 61, 122, .12), transparent 52%),
            #fff;
    }
    .bt-process-card:hover::after { color: rgba(255, 61, 122, .12); }
    .bt-process-card .bt-n {
        position: relative;
        z-index: 1;
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 999px;
        display: inline-grid;
        place-items: center;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: .75rem;
        color: #fff;
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        box-shadow: 0 8px 18px rgba(255, 61, 122, .35);
        font-variant-numeric: tabular-nums;
        font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
    }
    .bt-process-card h3,
    .bt-process-card p { position: relative; z-index: 1; }
    .bt-process-card h3 { margin: 0 0 .4rem; font-size: 1.05rem; color: var(--ink); font-weight: 800; }
    .bt-process-card p { margin: 0; color: var(--muted); font-size: .9rem; line-height: 1.6; }

    .bt-tip {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: .85rem 1rem;
        align-items: start;
        margin: 1.5rem 0 0;
        padding: 1.15rem 1.25rem;
        border-radius: var(--radius-sm);
        border: 1px solid #f0d48a;
        background: linear-gradient(135deg, #fff8e8, #fff0c8);
        color: #5a4310;
    }
    .bt-tip i { color: #c98a00; font-size: 1.25rem; margin-top: .15rem; }
    .bt-tip strong { display: block; margin-bottom: .25rem; font-size: 1rem; }
    .bt-tip p { margin: 0; font-size: .92rem; line-height: 1.65; opacity: .92; }

    .bt-form-wrap { scroll-margin-top: 6rem; }
    .bt-form-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem 1rem;
        margin-bottom: 1.35rem;
    }
    .bt-form-head h2 { margin: 0 0 .35rem; font-size: clamp(1.35rem, 2.5vw, 1.75rem); color: var(--ink); }
    .bt-form-head p { margin: 0; color: var(--muted); font-size: .92rem; }
    .bt-step-badge {
        display: inline-flex;
        align-items: center;
        padding: .4rem .9rem;
        border-radius: 999px;
        background: rgba(255, 61, 122, .1);
        color: var(--action-deep);
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .bt-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem 1.15rem;
    }
    @media (max-width: 640px) {
        .bt-form-grid { grid-template-columns: 1fr; }
    }
    .bt-form-grid .bt-span-2 { grid-column: 1 / -1; }
    .bt-lang-field { margin-top: 1.15rem; }
    .bt-lang-field__title {
        margin: 0 0 .25rem;
        font-size: .95rem;
        font-weight: 800;
        color: var(--ink);
    }
    .bt-lang-field__hint {
        margin: 0 0 .75rem;
        font-size: .82rem;
        color: var(--muted);
        line-height: 1.5;
    }
    .bt-lang-pills {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
    }
    .bt-lang-pill {
        position: relative;
        cursor: pointer;
    }
    .bt-lang-pill input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .bt-lang-pill span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 6.5rem;
        padding: .65rem 1.15rem;
        border-radius: 999px;
        border: 1.5px solid #c5d3e0;
        background: #fff;
        color: var(--ink);
        font-size: .9rem;
        font-weight: 700;
        transition:
            background-color .3s var(--bt-ease),
            border-color .3s var(--bt-ease),
            color .3s var(--bt-ease),
            box-shadow .3s var(--bt-ease),
            transform .3s var(--bt-ease);
    }
    .bt-lang-pill:hover span {
        border-color: rgba(255, 61, 122, .4);
        transform: translateY(-1px);
    }
    .bt-lang-pill input:checked + span {
        background: var(--action);
        border-color: var(--action);
        color: #fff;
        box-shadow: 0 8px 18px rgba(255, 61, 122, .28);
    }
    .bt-lang-pill input:focus-visible + span {
        outline: 2px solid rgba(255, 61, 122, .45);
        outline-offset: 2px;
    }
    .bt-panel {
        opacity: 1;
        transform: none;
        transition: opacity .35s var(--bt-ease), transform .35s var(--bt-ease);
    }
    .bt-panel[hidden] { display: none !important; }
    .bt-panel.is-entering {
        opacity: 0;
        transform: translateY(10px);
    }
    .bt-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        justify-content: space-between;
        margin-top: 1.35rem;
    }
    .bt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .85rem 1.35rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: .95rem;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    .bt-btn:hover { transform: translateY(-2px); }
    .bt-btn:active { transform: translateY(0); filter: brightness(.98); }
    .bt-btn-primary {
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        color: #fff;
        box-shadow: 0 12px 28px rgba(255, 61, 122, .3);
        min-width: min(100%, 16rem);
    }
    .bt-btn-primary:hover { box-shadow: 0 14px 32px rgba(255, 61, 122, .38); filter: brightness(1.03); }
    .bt-btn-ghost {
        background: #fff;
        color: var(--ink);
        border: 1px solid var(--line);
    }
    .bt-btn-ghost:hover {
        border-color: rgba(255, 61, 122, .35);
        color: var(--action-deep);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .06);
    }
    .bt-submit-box {
        margin-top: 1.5rem;
        padding: 1.25rem;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(255, 61, 122, .22);
        background: linear-gradient(180deg, rgba(255, 61, 122, .06), rgba(255, 61, 122, .02));
        text-align: center;
    }
    .bt-submit-box .bt-terms {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        text-align: start;
        margin-bottom: 1rem;
        color: var(--ink);
        font-size: .92rem;
        line-height: 1.55;
    }
    .bt-submit-box .bt-btn-primary { width: min(100%, 22rem); }
    .bt-submit-hint { margin: .85rem 0 0; font-size: .8rem; color: var(--muted); }

    .bt-docs-guide {
        margin-bottom: 1.15rem;
        padding: 1rem 1.1rem;
        border-radius: var(--radius-sm);
        background: #0e3a5c;
        color: #fff;
        font-size: .9rem;
        line-height: 1.65;
    }
    .bt-docs-guide strong { display: block; margin-bottom: .35rem; color: #ffb4cb; }

    .bt-faq-list { display: grid; gap: .65rem; margin-top: 1rem; }
    .bt-faq-item {
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        background: #fff;
        overflow: hidden;
        transition: border-color .35s var(--bt-ease), box-shadow .35s var(--bt-ease);
    }
    .bt-faq-item:hover { border-color: rgba(255, 61, 122, .28); }
    .bt-faq-item.is-open {
        border-color: rgba(255, 61, 122, .35);
        box-shadow: 0 10px 24px rgba(6, 21, 37, .06);
    }
    .bt-faq-q {
        width: 100%;
        list-style: none;
        cursor: pointer;
        padding: 1rem 1.15rem;
        font-weight: 700;
        color: var(--ink);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        text-align: start;
        background: transparent;
        border: 0;
        font-family: inherit;
        font-size: inherit;
        line-height: 1.45;
    }
    .bt-faq-q:focus-visible {
        outline: 2px solid rgba(255, 61, 122, .4);
        outline-offset: -2px;
    }
    .bt-faq-q__icon {
        flex-shrink: 0;
        width: 1.5rem;
        height: 1.5rem;
        display: inline-grid;
        place-items: center;
        color: var(--muted);
        transition: transform .35s var(--bt-ease), color .35s var(--bt-ease);
    }
    .bt-faq-item.is-open .bt-faq-q__icon {
        transform: rotate(180deg);
        color: var(--action);
    }
    .bt-faq-panel {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows .4s var(--bt-ease);
    }
    .bt-faq-item.is-open .bt-faq-panel {
        grid-template-rows: 1fr;
    }
    .bt-faq-panel__inner {
        overflow: hidden;
        min-height: 0;
    }
    .bt-faq-a {
        padding: 0 1.15rem 1.1rem;
        color: var(--muted);
        font-size: .92rem;
        line-height: 1.65;
        opacity: 0;
        transform: translateY(-4px);
        transition: opacity .3s var(--bt-ease) .05s, transform .35s var(--bt-ease);
    }
    .bt-faq-item.is-open .bt-faq-a {
        opacity: 1;
        transform: none;
    }

    .bt-block-card {
        text-align: center;
        padding: 2rem 1.25rem;
    }
    .bt-block-card i {
        font-size: 2rem;
        color: var(--action);
        margin-bottom: .75rem;
    }
    .bt-block-card h2 { margin: 0 0 .5rem; color: var(--ink); font-size: 1.35rem; }
    .bt-block-card p { margin: 0 0 1.25rem; color: var(--muted); line-height: 1.65; max-width: 34rem; margin-inline: auto; }

    .bt-form-wrap .drag-image-dropzone {
        border-color: #c5d3e0;
        background: #fbfdff;
    }
    .bt-form-wrap .drag-image-dropzone:hover {
        border-color: var(--action);
        background: rgba(255, 61, 122, .04);
    }
    .bt-file-field { margin-top: .25rem; }
    .bt-file-dropzone {
        position: relative;
        border: 2px dashed #c5d3e0;
        border-radius: .85rem;
        padding: 1.35rem 1rem;
        text-align: center;
        background: #fbfdff;
        cursor: pointer;
        transition:
            border-color .3s var(--bt-ease),
            background-color .3s var(--bt-ease),
            box-shadow .3s var(--bt-ease);
    }
    .bt-file-dropzone:hover,
    .bt-file-dropzone.is-dragover {
        border-color: var(--action);
        background: rgba(255, 61, 122, .04);
        box-shadow: 0 8px 20px rgba(255, 61, 122, .08);
    }
    .bt-file-dropzone.is-invalid {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .bt-file-dropzone.is-ready {
        border-color: #12c8a0;
        background: rgba(18, 200, 160, .06);
    }
    .bt-file-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    .bt-file-dropzone i {
        font-size: 1.75rem;
        color: var(--action);
        margin-bottom: .45rem;
    }
    .bt-file-dropzone p { margin: 0; color: var(--ink); font-size: .9rem; font-weight: 700; }
    .bt-file-dropzone small { display: block; margin-top: .35rem; color: var(--muted); font-size: .78rem; }
    .bt-file-name {
        display: none;
        margin-top: .55rem;
        color: var(--action-deep);
        font-size: .82rem;
        font-weight: 700;
        word-break: break-all;
    }
    .bt-file-dropzone.is-ready .bt-file-name { display: block; }
    .bt-sample-guide {
        margin: .65rem 0 1rem;
        padding: 1rem 1.1rem;
        border-radius: .9rem;
        border: 1px solid var(--line);
        background: #f7fafc;
        color: var(--ink);
        font-size: .9rem;
        line-height: 1.65;
    }
    .bt-sample-guide__specs {
        margin-top: .75rem;
        padding: .85rem 1rem;
        border-radius: .7rem;
        background: #0e3a5c;
        color: #fff;
        font-size: .86rem;
        line-height: 1.6;
    }
    .bt-sample-guide__warn {
        margin-top: .75rem;
        padding: .7rem .9rem;
        border-radius: .7rem;
        background: rgba(255, 61, 122, .1);
        border: 1px solid rgba(255, 61, 122, .28);
        color: var(--action-deep);
        font-size: .84rem;
        font-weight: 700;
        line-height: 1.5;
    }
    .bt-bio-wrap { position: relative; }
    .bt-bio-wrap textarea {
        width: 100%;
        min-height: 9rem;
        border-radius: .85rem;
        border: 1px solid #c5d3e0;
        background: #fff;
        padding: .9rem 1rem 2.1rem;
        color: var(--ink);
        font-size: .95rem;
        line-height: 1.65;
        resize: vertical;
        transition: border-color .3s var(--bt-ease), box-shadow .3s var(--bt-ease);
    }
    .bt-bio-wrap textarea:focus {
        border-color: var(--action);
        box-shadow: 0 0 0 3px rgba(255, 61, 122, .15);
        outline: none;
    }
    .bt-bio-count {
        position: absolute;
        inset-inline-end: .85rem;
        bottom: .65rem;
        font-size: .78rem;
        font-weight: 700;
        color: var(--muted);
        pointer-events: none;
    }
    .bt-bio-count.is-ok { color: #0b8f7f; }
    .bt-bio-count.is-low { color: var(--action-deep); }
    .bt-form-wrap label.block,
    .bt-form-wrap .block.text-sm { color: var(--ink); font-weight: 600; }
    .bt-form-wrap input[type="text"],
    .bt-form-wrap input[type="email"],
    .bt-form-wrap input[type="password"],
    .bt-form-wrap input[type="tel"],
    .bt-form-wrap select {
        border-radius: .75rem;
        border-color: #c5d3e0;
        background: #fff;
        transition: border-color .3s var(--bt-ease), box-shadow .3s var(--bt-ease);
    }
    .bt-form-wrap input:focus,
    .bt-form-wrap select:focus {
        border-color: var(--action);
        box-shadow: 0 0 0 3px rgba(255, 61, 122, .15);
        outline: none;
    }
    .select2-container, .iti { width: 100%; }
    .bt-page .academy-cta {
        transition: transform .35s var(--bt-ease), box-shadow .35s var(--bt-ease), filter .35s var(--bt-ease);
    }
</style>

<div class="academy-page bt-page">
    <header class="bt-hero reveal is-in">
        <p class="academy-kicker">{{ __('messages.academy') }}</p>
        <h1 class="display">{{ __('messages.become_trainer_hero_title') }}</h1>
        <p>{{ __('messages.become_trainer_hero_sub') }}</p>
        @if ($showForm)
        <a href="#trainer-application" class="academy-cta">
            <i class="fas fa-paper-plane"></i>
            {{ __('messages.become_trainer_hero_cta') }}
        </a>
        @endif
    </header>

    <section class="academy-section is-tight reveal is-in" id="trainer-journey-section">
        @include('academy.partials.trainer-journey', [
            'journeyStep' => $journeyStep,
            'completedSteps' => $completedSteps,
            'allDone' => $allDone,
            'journeyHint' => $journeyHint,
        ])
    </section>

    <div id="bt-applied-banner" class="academy-section is-tight reveal is-in" hidden>
        <div class="bt-card bt-block-card">
            <i class="fas fa-hourglass-half"></i>
            <h2 class="display">{{ __('messages.become_trainer_pending_title') }}</h2>
            <p>{{ __('messages.become_trainer_pending_body') }}</p>
            <a href="{{ route('login', ['ui' => 'academy']) }}" class="bt-btn bt-btn-ghost">{{ __('messages.login') }}</a>
        </div>
    </div>

    <section class="academy-section is-tight reveal is-in">
        <div class="bt-look-shell">
            <div class="bt-look-head">
                <h2 class="display academy-h2" style="font-size:clamp(1.35rem,2.5vw,1.85rem);">{{ __('messages.become_trainer_lookfor_title') }}</h2>
                <p class="academy-sub" style="margin-inline:auto;text-align:center;">{{ __('messages.become_trainer_lookfor_sub') }}</p>
            </div>
            <div class="bt-look-list">
                @foreach (range(1, 5) as $i)
                <div class="bt-look-item" data-step="{{ $i }}">
                    <span class="bt-look-num">{{ $i }}</span>
                    <div class="bt-look-body">
                        <h3>{{ __('messages.become_trainer_lookfor_'.$i.'_title') }}</h3>
                        <p>{{ __('messages.become_trainer_lookfor_'.$i.'_body') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="academy-section is-tight reveal is-in">
        <div class="text-center">
            <h2 class="display academy-h2">{{ __('messages.become_trainer_process_title') }}</h2>
        </div>
        <div class="bt-process-grid">
            @foreach (range(1, 3) as $i)
            <article class="bt-process-card" data-step="{{ $i }}">
                <div class="bt-n">{{ $i }}</div>
                <h3>{{ __('messages.become_trainer_process_'.$i.'_title') }}</h3>
                <p>{{ __('messages.become_trainer_process_'.$i.'_body') }}</p>
            </article>
            @endforeach
        </div>
        <div class="bt-tip">
            <i class="fas fa-lightbulb"></i>
            <div>
                <strong>{{ __('messages.become_trainer_tip_title') }}</strong>
                <p>{{ __('messages.become_trainer_tip_body') }}</p>
            </div>
        </div>
    </section>

    <section class="academy-section is-tight reveal is-in bt-form-wrap" id="trainer-application" data-bt-form-section>
        @if ($formBlocked && $user && $user->isTrainer() && $user->isPendingApproval())
        <div class="bt-card bt-block-card">
            <i class="fas fa-hourglass-half"></i>
            <h2 class="display">{{ __('messages.become_trainer_pending_title') }}</h2>
            <p>{{ __('messages.become_trainer_pending_body') }}</p>
            <a href="{{ route('login', ['ui' => 'academy']) }}" class="bt-btn bt-btn-ghost">{{ __('messages.login') }}</a>
        </div>
        @elseif ($formBlocked)
        <div class="bt-card bt-block-card">
            <i class="fas fa-user-lock"></i>
            <h2 class="display">{{ __('messages.become_trainer_logged_in_block_title') }}</h2>
            <p>{{ __('messages.become_trainer_logged_in_block_body') }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <input type="hidden" name="redirect" value="{{ route('academy.become-trainer') }}">
                <button type="submit" class="bt-btn bt-btn-primary">
                    <i class="fas fa-right-from-bracket"></i>
                    {{ __('messages.become_trainer_logout_cta') }}
                </button>
            </form>
        </div>
        @else
        <div class="bt-card">
            @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <div class="font-bold mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تعذر إرسال الطلب — صحّح الأخطاء التالية:' : 'Could not submit — please fix the following:' }}</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="become-trainer-form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-0">
                @csrf
                <input type="hidden" name="ui" value="academy">
                <input type="hidden" name="account_type" value="personal">
                <input type="hidden" name="role" value="trainer">

                <div class="bt-panel" data-step-panel="1">
                    <div class="bt-form-head">
                        <div>
                            <h2 class="display">{{ __('messages.become_trainer_form_intro_title') }}</h2>
                            <p>{{ __('messages.become_trainer_form_intro_sub') }}</p>
                        </div>
                        <span class="bt-step-badge">{{ __('messages.become_trainer_form_step_of', ['current' => 1, 'total' => 2]) }}</span>
                    </div>

                    <div class="bt-form-grid">
                        <div>
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('messages.email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="country-select2-host is-pink">
                            <x-input-label for="country_select2" :value="__('messages.country')" />
                            <select id="country_select2" name="country" class="block mt-1 w-full rtl:text-right" required>
                                <option value="" disabled selected>{{ app()->getLocale() === 'ar' ? 'اختر دولتك' : 'Select your country' }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('messages.phone')" />
                            <x-text-input id="phone" class="placeholder-gray-500 block mt-1 w-full rtl:text-right" type="tel" name="phone" :value="old('phone')" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('messages.password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    @php $selectedTeachLang = old('teaching_language', 'ar'); @endphp
                    <div class="bt-lang-field">
                        <p class="bt-lang-field__title">{{ __('messages.become_trainer_teaching_lang') }}</p>
                        <p class="bt-lang-field__hint">{{ __('messages.become_trainer_teaching_lang_hint') }}</p>
                        <div class="bt-lang-pills" role="radiogroup" aria-label="{{ __('messages.become_trainer_teaching_lang') }}">
                            <label class="bt-lang-pill">
                                <input type="radio" name="teaching_language" value="ar" {{ $selectedTeachLang === 'ar' ? 'checked' : '' }}>
                                <span>{{ __('messages.become_trainer_teaching_lang_ar') }}</span>
                            </label>
                            <label class="bt-lang-pill">
                                <input type="radio" name="teaching_language" value="en" {{ $selectedTeachLang === 'en' ? 'checked' : '' }}>
                                <span>{{ __('messages.become_trainer_teaching_lang_en') }}</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('teaching_language')" class="mt-2" />
                    </div>

                    <div class="bt-actions">
                        <span></span>
                        <button type="button" class="bt-btn bt-btn-primary" data-next-step>
                            {{ __('messages.become_trainer_next') }}
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                        </button>
                    </div>
                </div>

                <div class="bt-panel" data-step-panel="2" hidden>
                    <div class="bt-form-head">
                        <div>
                            <h2 class="display">{{ __('messages.become_trainer_form_docs_title') }}</h2>
                            <p>{{ __('messages.become_trainer_form_docs_sub') }}</p>
                        </div>
                        <span class="bt-step-badge">{{ __('messages.become_trainer_form_step_of', ['current' => 2, 'total' => 2]) }}</span>
                    </div>

                    <div class="bt-docs-guide">
                        <strong>{{ __('messages.become_trainer_docs_guide_title') }}</strong>
                        {{ __('messages.become_trainer_docs_guide_body') }}
                    </div>

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="course_category_id" :value="__('messages.trainer_category')" />
                            <select id="course_category_id" name="course_category_id" class="block mt-1 w-full" required>
                                <option value="">{{ __('messages.trainer_category_placeholder') }}</option>
                                @foreach (($categories ?? []) as $category)
                                <option value="{{ $category->id }}" @selected((string) old('course_category_id') === (string) $category->id)>
                                    {{ $category->title($locale) }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_category_id')" class="mt-2" />
                        </div>

                        @include('dashboard.course-categories.partials.drag-image-input', [
                            'name' => 'avatar',
                            'label' => __('messages.trainer_avatar'),
                            'hint' => __('messages.trainer_avatar_hint'),
                            'required' => true,
                            'previewRounded' => true,
                        ])
                        <div class="bt-avatar-example rounded-xl border border-slate-200 bg-slate-50 p-3 flex items-center gap-3">
                            <img src="{{ asset('images/trainer-avatar-example.jpg') }}" alt="{{ __('messages.trainer_avatar_example_alt') }}"
                                class="w-16 h-16 rounded-full object-cover border border-slate-200 flex-shrink-0">
                            <p class="text-xs text-slate-600 leading-relaxed m-0">{{ __('messages.trainer_avatar_professional_note') }}</p>
                        </div>

                        <div>
                            <x-input-label for="linkedin_url" :value="__('messages.trainer_linkedin')" />
                            <input id="linkedin_url" type="url" name="linkedin_url" required
                                value="{{ old('linkedin_url') }}"
                                placeholder="https://www.linkedin.com/in/your-profile"
                                class="block mt-1 w-full" dir="ltr">
                            <p class="text-[11px] text-slate-400 mt-1">{{ __('messages.trainer_linkedin_hint') }}</p>
                            <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                        </div>

                        <div class="bt-file-field">
                            <label class="block text-sm font-medium mb-1">
                                {{ __('messages.trainer_sample') }}
                                <span class="text-slate-400 text-xs font-normal">({{ __('messages.optional') }})</span>
                            </label>
                            <div class="bt-sample-guide">
                                <p class="m-0">{{ __('messages.trainer_sample_intro') }}</p>
                                <div class="bt-sample-guide__specs">{{ __('messages.trainer_sample_specs') }}</div>
                                <div class="bt-sample-guide__warn">
                                    <i class="fas fa-triangle-exclamation ml-1"></i>
                                    {{ __('messages.trainer_sample_warn') }}
                                </div>
                            </div>
                            @include('academy.partials.teaching-sample-input', [
                                'variant' => 'become',
                                'sampleType' => old('teaching_sample_type', 'upload'),
                            ])
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1" for="trainer_bio">
                                {{ __('messages.trainer_bio') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="bt-bio-wrap">
                                <textarea id="trainer_bio" name="trainer_bio" required minlength="120" maxlength="2000"
                                    placeholder="{{ __('messages.trainer_bio_placeholder') }}">{{ old('trainer_bio') }}</textarea>
                                <span class="bt-bio-count" data-bio-count>0 / 120+</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">{{ __('messages.trainer_bio_hint') }}</p>
                            <x-input-error :messages="$errors->get('trainer_bio')" class="mt-2" />
                        </div>
                    </div>

                    <div class="bt-submit-box">
                        <label class="bt-terms">
                            <input type="checkbox" name="accept_terms" value="1" class="mt-1" {{ old('accept_terms') ? 'checked' : '' }} required>
                            <span>
                                {{ __('messages.trainer_terms_agree') }}
                                <button type="button" id="open-trainer-terms" class="font-bold underline underline-offset-2" style="color:var(--action-deep);">
                                    {{ __('messages.trainer_terms_link') }}
                                </button>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('accept_terms')" class="mt-2 text-start" />

                        <div class="bt-actions" style="justify-content:center;margin-top:0;">
                            <button type="button" class="bt-btn bt-btn-ghost" data-prev-step>
                                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                                {{ __('messages.become_trainer_back') }}
                            </button>
                            <button type="submit" class="bt-btn bt-btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                {{ __('messages.become_trainer_submit') }}
                            </button>
                        </div>
                        <p class="bt-submit-hint">{{ __('messages.become_trainer_submit_hint') }}</p>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </section>

    <section class="academy-section is-tight reveal is-in bt-faq">
        <h2 class="display academy-h2">{{ __('messages.become_trainer_faq_title') }}</h2>
        <div class="bt-faq-list" data-faq-accordion>
            @foreach (range(1, 5) as $i)
            <div class="bt-faq-item {{ $i === 1 ? 'is-open' : '' }}">
                <button type="button" class="bt-faq-q" aria-expanded="{{ $i === 1 ? 'true' : 'false' }}">
                    <span>{{ __('messages.become_trainer_faq_'.$i.'_q') }}</span>
                    <span class="bt-faq-q__icon" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="bt-faq-panel">
                    <div class="bt-faq-panel__inner">
                        <div class="bt-faq-a">{{ __('messages.become_trainer_faq_'.$i.'_a') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>

@if ($showForm)
{{-- Terms modal --}}
<div id="trainer-terms-modal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50" data-close-terms></div>
    <div class="relative mx-auto my-8 w-[94%] max-w-2xl max-h-[85vh] overflow-hidden rounded-xl bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between gap-3 border-b px-5 py-4">
            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.trainer_terms_title') }}</h3>
            <button type="button" class="text-gray-500 hover:text-gray-800 text-xl leading-none" data-close-terms aria-label="Close">&times;</button>
        </div>
        <div class="px-5 py-4 overflow-y-auto text-sm text-gray-700 leading-7 space-y-3">
            {!! __('messages.trainer_terms_body') !!}
        </div>
        <div class="border-t px-5 py-3 flex justify-end">
            <button type="button" class="px-5 py-2.5 rounded-full text-sm font-bold text-white"
                style="background:linear-gradient(135deg,#ff3d7a,#e11d62);" data-close-terms>
                {{ __('messages.close') }}
            </button>
        </div>
    </div>
</div>

@include('dashboard.course-categories.partials.drag-image-script')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    (function () {
        const form = document.getElementById('become-trainer-form');
        if (!form) return;

        const panels = {
            1: form.querySelector('[data-step-panel="1"]'),
            2: form.querySelector('[data-step-panel="2"]'),
        };
        let step = {{ $openFormOnLoad && ($errors->has('course_category_id') || $errors->has('avatar') || $errors->has('linkedin_url') || $errors->has('teaching_sample') || $errors->has('teaching_sample_link') || $errors->has('trainer_bio') || $errors->has('accept_terms')) ? 2 : 1 }};

        function showStep(n, animate = true) {
            step = n;
            Object.entries(panels).forEach(([key, el]) => {
                if (!el) return;
                const active = Number(key) === n;
                if (!active) {
                    el.hidden = true;
                    el.classList.remove('is-entering');
                    return;
                }
                el.hidden = false;
                if (animate) {
                    el.classList.add('is-entering');
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => el.classList.remove('is-entering'));
                    });
                }
            });
            if (n === 2) {
                panels[2]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function step1Valid() {
            const required = ['name', 'email', 'phone', 'country', 'password', 'password_confirmation'];
            for (const name of required) {
                const el = form.querySelector(`[name="${name}"]`);
                if (!el || !String(el.value || '').trim()) {
                    el?.focus();
                    el?.reportValidity?.();
                    return false;
                }
            }
            const pass = form.querySelector('[name="password"]');
            const conf = form.querySelector('[name="password_confirmation"]');
            if (pass && conf && pass.value !== conf.value) {
                conf.setCustomValidity(@json(app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور غير متطابق' : 'Password confirmation does not match'));
                conf.reportValidity();
                conf.setCustomValidity('');
                return false;
            }
            return true;
        }

        form.querySelector('[data-next-step]')?.addEventListener('click', () => {
            if (step1Valid()) showStep(2);
        });
        form.querySelector('[data-prev-step]')?.addEventListener('click', () => showStep(1));
        showStep(step, false);

        @if ($openFormOnLoad)
        document.getElementById('trainer-application')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif

        form.addEventListener('submit', (e) => {
            let valid = true;
            const avatarInput = form.querySelector('input[name="avatar"]');
            const maxAvatarBytes = 2 * 1024 * 1024;
            if (avatarInput) {
                const zone = avatarInput.closest('.drag-image-dropzone');
                const file = avatarInput.files?.[0];
                const hasFile = !!file;
                zone?.classList.toggle('border-red-500', !hasFile);
                zone?.classList.toggle('bg-red-50', !hasFile);
                if (!hasFile) {
                    valid = false;
                } else if (file.size > maxAvatarBytes) {
                    valid = false;
                    zone?.classList.add('border-red-500', 'bg-red-50');
                    alert(@json(__('messages.trainer_image_max_2')));
                    avatarInput.focus();
                }
            }

            const linkedin = form.querySelector('#linkedin_url');
            if (!linkedin || !String(linkedin.value || '').trim()) {
                valid = false;
                linkedin?.focus();
            }

            const bio = form.querySelector('#trainer_bio');
            const bioLen = (bio?.value || '').trim().length;
            if (bioLen < 120) {
                valid = false;
                bio?.focus();
            }

            if (!valid) {
                e.preventDefault();
                showStep(2);
                // Avoid clearing a real size error with the generic incomplete alert.
                if (!(avatarInput?.files?.[0]?.size > maxAvatarBytes)) {
                    alert(@json(__('messages.trainer_apply_incomplete_alert')));
                }
            }
        });

        form.querySelectorAll('[data-file-dropzone]').forEach((zone) => {
            const input = zone.querySelector('input[type="file"]');
            const nameEl = zone.querySelector('[data-file-name]');
            function syncFile() {
                const file = input?.files?.[0];
                zone.classList.toggle('is-ready', !!file);
                zone.classList.toggle('is-invalid', false);
                if (nameEl) nameEl.textContent = file ? file.name : '';
            }
            input?.addEventListener('change', syncFile);
            ['dragenter', 'dragover'].forEach((evt) => {
                zone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    zone.classList.add('is-dragover');
                });
            });
            ['dragleave', 'drop'].forEach((evt) => {
                zone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    zone.classList.remove('is-dragover');
                });
            });
            syncFile();
        });

        const bioInput = form.querySelector('#trainer_bio');
        const bioCount = form.querySelector('[data-bio-count]');
        function syncBioCount() {
            const len = (bioInput?.value || '').length;
            if (!bioCount) return;
            bioCount.textContent = len + ' / 120+';
            bioCount.classList.toggle('is-ok', len >= 120);
            bioCount.classList.toggle('is-low', len > 0 && len < 120);
        }
        bioInput?.addEventListener('input', syncBioCount);
        syncBioCount();

        const termsModal = document.getElementById('trainer-terms-modal');
        const openTerms = document.getElementById('open-trainer-terms');
        function setTermsOpen(open) {
            if (!termsModal) return;
            termsModal.classList.toggle('hidden', !open);
            termsModal.setAttribute('aria-hidden', open ? 'false' : 'true');
            document.body.classList.toggle('overflow-hidden', open);
        }
        openTerms?.addEventListener('click', (e) => {
            e.preventDefault();
            setTermsOpen(true);
        });
        termsModal?.querySelectorAll('[data-close-terms]').forEach((el) => {
            el.addEventListener('click', () => setTermsOpen(false));
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') setTermsOpen(false);
        });
    })();
</script>
@include('partials.country-select2', [
    'selector' => '#country_select2',
    'oldCountry' => old('country', ''),
    'variant' => 'pink',
])
@endif

<script>
(function () {
    const faqRoot = document.querySelector('[data-faq-accordion]');
    if (faqRoot) {
        faqRoot.querySelectorAll('.bt-faq-item').forEach((item) => {
            const btn = item.querySelector('.bt-faq-q');
            btn?.addEventListener('click', () => {
                const willOpen = !item.classList.contains('is-open');
                faqRoot.querySelectorAll('.bt-faq-item.is-open').forEach((openItem) => {
                    if (openItem === item) return;
                    openItem.classList.remove('is-open');
                    openItem.querySelector('.bt-faq-q')?.setAttribute('aria-expanded', 'false');
                });
                item.classList.toggle('is-open', willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });
    }

    const KEY = @json(\App\Support\TrainerJourney::STORAGE_KEY);
    const serverStep = {{ (int) $journeyStep }};
    const hasAuthTrainer = @json((bool) ($user && $user->isTrainer()));
    const labels = {
        apply: @json(__('messages.become_trainer_step_apply_sub')),
        publish: @json(__('messages.become_trainer_step_publish_sub')),
        progress: @json(__('messages.become_trainer_journey_progress')),
        hints: {
            1: @json(__('messages.become_trainer_journey_hint')),
            2: @json(__('messages.become_trainer_journey_hint_review')),
            3: @json(__('messages.become_trainer_journey_hint_approve')),
            4: @json(__('messages.become_trainer_journey_hint_create')),
            5: @json(__('messages.become_trainer_journey_hint_publish')),
        }
    };

    function readStore() {
        try {
            return JSON.parse(localStorage.getItem(KEY) || 'null');
        } catch (e) {
            return null;
        }
    }

    function writeStore(payload) {
        try {
            localStorage.setItem(KEY, JSON.stringify(Object.assign({ at: Date.now() }, payload)));
        } catch (e) {}
    }

    function paintJourney(step, allDone) {
        const root = document.querySelector('[data-trainer-journey]');
        if (!root) return;
        const completed = allDone ? 5 : Math.max(0, step - 1);
        root.dataset.journeyStep = String(step);
        root.dataset.journeyCompleted = String(completed);
        root.dataset.journeyAllDone = allDone ? '1' : '0';

        const count = root.querySelector('[data-journey-count]');
        if (count) {
            count.textContent = labels.progress
                .replace(':done', String(completed))
                .replace(':total', '5');
        }

        const hint = root.querySelector('[data-journey-hint]');
        if (hint) hint.textContent = labels.hints[step] || labels.hints[1];

        root.querySelectorAll('[data-journey-item]').forEach((el) => {
            const n = Number(el.getAttribute('data-journey-item'));
            let state = 'is-locked';
            if (allDone || n < step) state = 'is-done';
            else if (n === step) state = 'is-active';
            el.classList.remove('is-done', 'is-active', 'is-locked', 'is-link');
            el.classList.add(state);
            el.setAttribute('aria-current', state === 'is-active' ? 'step' : 'false');

            const href = el.getAttribute('data-journey-href');
            const canOpen = state !== 'is-locked' && !!href;
            if (canOpen) {
                el.classList.add('is-link');
                if (el.tagName === 'A') el.setAttribute('href', href);
                el.removeAttribute('aria-disabled');
            } else {
                if (el.tagName === 'A') el.removeAttribute('href');
                el.setAttribute('aria-disabled', 'true');
            }

            const icon = el.querySelector('.tj-step__icon i');
            if (icon) {
                icon.className = 'fas ' + (state === 'is-done' ? 'fa-check' : (state === 'is-locked' ? 'fa-lock' : (
                    n === 1 ? 'fa-paper-plane' : n === 2 ? 'fa-clipboard-check' : n === 3 ? 'fa-circle-check' : n === 4 ? 'fa-chalkboard' : 'fa-rocket'
                )));
            }
            let sub = el.querySelector('.tj-step__sub');
            if (state === 'is-active' && n === 1) {
                if (!sub) {
                    sub = document.createElement('span');
                    sub.className = 'tj-step__sub';
                    el.querySelector('.tj-step__text')?.appendChild(sub);
                }
                sub.textContent = labels.apply;
            } else if (!allDone && n === 5 && (state === 'is-active' || state === 'is-locked')) {
                if (!sub) {
                    sub = document.createElement('span');
                    sub.className = 'tj-step__sub';
                    el.querySelector('.tj-step__text')?.appendChild(sub);
                }
                sub.textContent = labels.publish;
            } else if (sub && !(state === 'is-active' && n === 1)) {
                if (n !== 5) sub.remove();
            }
        });
    }

    // Successful application is recorded only after server redirect to login?trainer_applied=1.
    // Do NOT write localStorage on submit — a server validation failure would still leave a
    // false "pending approval" state and hide the form.

    const hasServerErrors = @json($errors->any());
    const stored = readStore();
    let step = serverStep;
    let allDone = @json($allDone);

    if (hasServerErrors && !hasAuthTrainer) {
        // Discard any premature client "applied" flag so the form stays visible for fixes.
        if (stored && stored.source === 'application') {
            try { localStorage.removeItem(KEY); } catch (e) {}
        }
        paintJourney(serverStep, false);
        const formSection = document.querySelector('[data-bt-form-section]');
        const banner = document.getElementById('bt-applied-banner');
        if (formSection) formSection.hidden = false;
        if (banner) banner.hidden = true;
    } else if (hasAuthTrainer) {
        writeStore({ step: serverStep, completed: {{ (int) $completedSteps }}, allDone: allDone, source: 'server' });
        paintJourney(serverStep, allDone);
    } else if (stored && Number(stored.step) > step) {
        step = Math.min(5, Number(stored.step) || 2);
        allDone = !!stored.allDone;
        paintJourney(step, allDone);

        if (step >= 2) {
            const formSection = document.querySelector('[data-bt-form-section]');
            const banner = document.getElementById('bt-applied-banner');
            if (formSection) formSection.hidden = true;
            if (banner) banner.hidden = false;
        }
    } else {
        paintJourney(step, allDone);
    }
})();
</script>
@endsection

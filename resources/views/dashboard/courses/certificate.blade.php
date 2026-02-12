@extends('layouts.app')

@section('title', 'شهادة حضور - ' . $payment->user->name)

{{-- @push('styles') --}}
<link
    href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Playfair+Display:ital,wght@0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap"
    rel="stylesheet">
{{-- @endpush --}}

@section('content')
<div class="cert-page">

    {{-- أزرار التحكم --}}
    <div class="control-bar no-print">
        <button style="color:white;" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path
                    d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
            </svg>
            طباعة الشهادة
        </button>
    </div>

    {{-- تصميم الشهادة --}}
    <div class="certificate" id="certificate">

        {{-- الإطار الخارجي --}}
        <div class="outer-border"></div>
        <div class="inner-border"></div>

        {{-- زخارف الأركان --}}
        <div class="corner corner-tl">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 2 L78 2 L78 10 L10 10 L10 78 L2 78 Z" fill="none" stroke="#000" stroke-width="1.5" />
                <circle cx="10" cy="10" r="4" fill="#000" />
                <circle cx="2" cy="2" r="2" fill="#000" />
                <path d="M18 2 L18 18 L2 18" fill="none" stroke="#000" stroke-width="0.75" stroke-dasharray="2 2" />
            </svg>
        </div>
        <div class="corner corner-tr">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M78 2 L2 2 L2 10 L70 10 L70 78 L78 78 Z" fill="none" stroke="#000" stroke-width="1.5" />
                <circle cx="70" cy="10" r="4" fill="#000" />
                <circle cx="78" cy="2" r="2" fill="#000" />
                <path d="M62 2 L62 18 L78 18" fill="none" stroke="#000" stroke-width="0.75" stroke-dasharray="2 2" />
            </svg>
        </div>
        <div class="corner corner-bl">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 78 L78 78 L78 70 L10 70 L10 2 L2 2 Z" fill="none" stroke="#000" stroke-width="1.5" />
                <circle cx="10" cy="70" r="4" fill="#000" />
                <circle cx="2" cy="78" r="2" fill="#000" />
                <path d="M18 78 L18 62 L2 62" fill="none" stroke="#000" stroke-width="0.75" stroke-dasharray="2 2" />
            </svg>
        </div>
        <div class="corner corner-br">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M78 78 L2 78 L2 70 L70 70 L70 2 L78 2 Z" fill="none" stroke="#000" stroke-width="1.5" />
                <circle cx="70" cy="70" r="4" fill="#000" />
                <circle cx="78" cy="78" r="2" fill="#000" />
                <path d="M62 78 L62 62 L78 62" fill="none" stroke="#000" stroke-width="0.75"
                    stroke-dasharray="2 2" />
            </svg>
        </div>

        {{-- الشعاع الذهبي في الخلفية --}}
        <div class="bg-rays"></div>

        {{-- الشعار المائي --}}
        <div class="watermark">
            <img src="{{ asset('assets/images/logo.webp') }}" alt="">
        </div>

        {{-- المحتوى --}}
        <div class="cert-content">

            {{-- الهيدر --}}
            <header class="cert-header">
                <div class="logo-wrap">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Logo">
                </div>
                <div class="divider-ornament">
                    <span></span>
                    <svg viewBox="0 0 40 20" fill="none">
                        <path d="M0 10 Q10 0 20 10 Q30 20 40 10" stroke="#000" stroke-width="1.5" fill="none" />
                        <circle cx="20" cy="10" r="3" fill="#000" />
                    </svg>
                    <span></span>
                </div>
                <div class="cert-title">
                    <h2>افادة حضور دورة تدريبية</h2>
                    <p>Certificate of Completion</p>
                </div>
            </header>

            {{-- الفاصل الزخرفي --}}
            <div class="ornamental-rule">
                <div class="rule-line"></div>
                <div class="rule-diamond"></div>
                <div class="rule-line"></div>
            </div>

            {{-- اسم المتدرب --}}
            <section class="trainee-section">
                <p class="certify-text">نشهد بأن المتدرب / <em>This is to certify that</em></p>
                <h1 class="trainee-name">{{ $payment->user->name }}</h1>
                <div class="name-underline">
                    <svg viewBox="0 0 300 12" preserveAspectRatio="none">
                        <path d="M0 6 Q75 0 150 6 Q225 12 300 6" stroke="#000" stroke-width="1.5" fill="none" />
                    </svg>
                </div>
            </section>

            {{-- نص الشهادة --}}
            <section class="course-section">
                <p class="completion-text">قد أتمَّ بنجاح الدورة التدريبية بعنوان</p>
                <h2 class="course-name-ar">{{ $payment->course->name_ar }}</h2>
                <p class="course-name-en">{{ $payment->course->name_en }}</p>
            </section>

            {{-- الفاصل الزخرفي السفلي --}}
            <div class="ornamental-rule">
                <div class="rule-line"></div>
                <div class="rule-diamond"></div>
                <div class="rule-line"></div>
            </div>

            {{-- الذيل: التاريخ والختم --}}
            <footer class="cert-footer">
                <div class="footer-date">
                    <p class="footer-label">تاريخ الدورة</p>
                    <p class="footer-value">{{ $payment->course->start_date->format('Y-m-d') }}</p>
                </div>

                {{-- <div class="footer-seal">
                    <div class="seal-ring">
                        <img src="{{ asset('assets/images/evorq-seal.webp') }}" alt="Seal">
                    </div>
                </div> --}}

                <div class="footer-org">
                    {{-- <p class="footer-label" style="text-align: center;">المؤسسة المانحة</p> --}}
                    <p class="footer-org-ar" style="text-align: center; font-size: 2rem;"><span style="font-weight: bold">إيفورك</span> للتكنولوجيا</p>
                    <p class="footer-org-en" style="text-align: center;">EVORQ TECHNOLOGIES</p>
                </div>
            </footer>

        </div>{{-- /cert-content --}}
    </div>{{-- /certificate --}}
</div>{{-- /cert-page --}}

<style>

    :root {
        --gold: #000000;
        --gold-light: #000000;
        --gold-dark: #0f0f0f;
        --navy: #0D2444;
        --navy-mid: #1A3A6E;
        --parchment: #FDFAF3;
        --ink: #1C1C2E;
        --ink-soft: #4A4A6A;
    }

    /* ── Page wrapper ── */
    .cert-page {
        background: #e8e4dc;
        background-image:
            radial-gradient(ellipse at 30% 20%, rgba(184, 150, 12, 0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 70% 80%, rgba(13, 36, 68, 0.06) 0%, transparent 50%);
        min-height: 100vh;
        padding: 2rem 1rem 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'Amiri', serif;
        direction: rtl;
    }

    /* ── Control bar ── */
    .control-bar {
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
    }

    .control-bar button {
        display: flex;
        align-items: center;
        gap: .5rem;
        background: var(--navy);
        color: var(--gold-light);
        border: 1px solid var(--gold-dark);
        padding: .65rem 1.6rem;
        border-radius: 4px;
        font-family: 'Amiri', serif;
        font-size: 1rem;
        cursor: pointer;
        letter-spacing: .03em;
        transition: background .2s, color .2s;
    }

    .control-bar button:hover {
        background: var(--navy-mid);
        color: #fff;
    }

    .control-bar button svg {
        width: 18px;
        height: 18px;
    }

    /* ── Certificate shell ── */
    .certificate {
        position: relative;
        background: var(--parchment);
        background-image:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1' fill='%23000' fill-opacity='.07'/%3E%3C/svg%3E"),
            linear-gradient(160deg, rgba(255, 255, 255, 0.6) 0%, rgba(253, 250, 243, 1) 40%, rgba(248, 242, 225, 0.8) 100%);
        width: 100%;
        max-width: 900px;
        min-height: 640px;
        padding: 60px 70px 50px;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, .06),
            0 20px 60px rgba(13, 36, 68, .18),
            inset 0 0 120px rgba(184, 150, 12, .04);
        overflow: hidden;
    }

    /* ── Borders ── */
    .outer-border {
        position: absolute;
        inset: 10px;
        border: 2px solid var(--gold);
        pointer-events: none;
        z-index: 1;
    }

    .inner-border {
        position: absolute;
        inset: 16px;
        border: 1px solid rgba(184, 150, 12, .35);
        pointer-events: none;
        z-index: 1;
    }

    /* ── Corners ── */
    .corner {
        position: absolute;
        width: 80px;
        height: 80px;
        z-index: 3;
    }

    .corner-tl {
        top: 6px;
        left: 6px;
    }

    .corner-tr {
        top: 6px;
        right: 6px;
        transform: scaleX(-1);
    }

    .corner-bl {
        bottom: 6px;
        left: 6px;
        transform: scaleY(-1);
    }

    .corner-br {
        bottom: 6px;
        right: 6px;
        transform: scale(-1);
    }

    /* ── Background rays ── */
    .bg-rays {
        position: absolute;
        inset: 0;
        background-image: repeating-conic-gradient(from 0deg at 50% 50%,
                transparent 0deg,
                transparent 8.5deg,
                rgba(184, 150, 12, .025) 8.5deg,
                rgba(184, 150, 12, .025) 9deg);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Watermark ── */
    .watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
    }

    .watermark img {
        width: 45%;
        opacity: .045;
        filter: grayscale(100%);
    }

    /* ── Content layer ── */
    .cert-content {
        position: relative;
        z-index: 5;
        text-align: center;
    }

    /* ── Header ── */
    .cert-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .75rem;
        gap: 1rem;
    }

    .logo-wrap img {
        height: 72px;
        object-fit: contain;
    }

    .cert-title {
        text-align: right;
    }

    .cert-title h2 {
        font-family: 'Amiri', serif;
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--navy);
        margin: 0;
        letter-spacing: .02em;
    }

    .cert-title p {
        font-family: 'Cormorant Garamond', serif;
        font-size: .95rem;
        color: var(--gold-dark);
        letter-spacing: .15em;
        text-transform: uppercase;
        margin: .1rem 0 0;
    }

    /* Divider between logo and title */
    .divider-ornament {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .35rem;
    }

    .divider-ornament span {
        display: block;
        width: 1px;
        height: 28px;
        background: linear-gradient(to bottom, transparent, var(--gold), transparent);
    }

    .divider-ornament svg {
        width: 40px;
    }

    /* ── Ornamental rule ── */
    .ornamental-rule {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: .9rem 0;
    }

    .rule-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--gold), transparent);
    }

    .rule-diamond {
        width: 10px;
        height: 10px;
        background: var(--gold);
        transform: rotate(45deg);
        flex-shrink: 0;
    }

    .rule-diamond::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        background: var(--parchment);
        transform: rotate(45deg) translate(-50%, -50%);
        margin: 2px auto;
    }

    /* ── Trainee section ── */
    .trainee-section {
        margin: 1rem 0 .5rem;
    }

    .certify-text {
        font-family: 'Amiri', serif;
        font-size: 1.15rem;
        color: var(--ink-soft);
        margin-bottom: .6rem;
    }

    .certify-text em {
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        letter-spacing: .05em;
    }

    .trainee-name {
        font-family: 'Amiri', serif;
        font-size: 3.6rem;
        font-weight: 700;
        color: var(--navy);
        margin: .1rem 0 .3rem;
        line-height: 1.1;
        text-shadow: 0 2px 12px rgba(13, 36, 68, .08);
        letter-spacing: .02em;
    }

    .name-underline {
        width: 320px;
        margin: 0 auto;
    }

    .name-underline svg {
        width: 100%;
        height: 12px;
    }

    /* ── Course section ── */
    .course-section {
        margin: .6rem 0 .5rem;
    }

    .completion-text {
        font-family: 'Amiri', serif;
        font-size: 1.1rem;
        color: var(--ink-soft);
        margin-bottom: .6rem;
    }

    .course-name-ar {
        font-family: 'Amiri', serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--navy-mid);
        margin: .3rem 0 .25rem;
        line-height: 1.3;
    }

    .course-name-en {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.1rem;
        font-style: italic;
        color: var(--gold-dark);
        letter-spacing: .06em;
        margin: 0;
    }

    /* ── Footer ── */
    .cert-footer {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 1.5rem;
        margin-top: .9rem;
    }

    .footer-date {
        text-align: right;
    }

    .footer-org {
        text-align: left;
    }

    .footer-label {
        font-family: 'Amiri', serif;
        font-size: .8rem;
        color: var(--gold-dark);
        letter-spacing: .1em;
        text-transform: uppercase;
        margin: 0 0 .25rem;
    }

    .footer-value {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.05rem;
        color: var(--ink);
        font-weight: 600;
        margin: 0;
        letter-spacing: .04em;
    }

    .footer-org-ar {
        font-family: 'Amiri', serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--navy);
        margin: 0;
    }

    .footer-org-en {
        font-family: 'Cormorant Garamond', serif;
        font-size: .78rem;
        letter-spacing: .15em;
        color: var(--gold-dark);
        margin: .15rem 0 0;
    }

    /* Seal */
    .footer-seal {
        display: flex;
        justify-content: center;
    }

    .seal-ring {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 2px dashed rgba(184, 150, 12, .4);
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle, rgba(184, 150, 12, .04) 0%, transparent 70%);
    }

    .seal-ring img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        opacity: .82;
    }

    /* ── Print ── */
    @media print {
        .no-print {
            display: none !important;
        }

        body,
        .cert-page {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .certificate {
            max-width: 100% !important;
            box-shadow: none !important;
            page-break-inside: avoid;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .outer-border,
        .inner-border,
        .bg-rays,
        .watermark,
        .corner {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection
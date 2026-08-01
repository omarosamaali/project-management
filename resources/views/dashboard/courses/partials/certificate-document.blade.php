{{-- Shared certificate markup — used by the real certificate page and academy trust preview.
     Updates here automatically reflect everywhere this partial is included. --}}
@php
    $traineeName = $traineeName ?? '';
    $courseNameAr = $courseNameAr ?? '';
    $courseNameEn = $courseNameEn ?? '';
    $courseDate = $courseDate ?? '';
    $certificateId = $certificateId ?? 'certificate';
    $certificateClass = trim((string) ($certificateClass ?? ''));
@endphp
<div class="certificate{{ $certificateClass !== '' ? ' '.$certificateClass : '' }}" id="{{ $certificateId }}">

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
            <h1 class="trainee-name">{{ $traineeName }}</h1>
            <div class="name-underline">
                <svg viewBox="0 0 300 12" preserveAspectRatio="none">
                    <path d="M0 6 Q75 0 150 6 Q225 12 300 6" stroke="#000" stroke-width="1.5" fill="none" />
                </svg>
            </div>
        </section>

        {{-- نص الشهادة --}}
        <section class="course-section">
            <p class="completion-text">قد أتمَّ بنجاح الدورة التدريبية بعنوان</p>
            <h2 class="course-name-ar">{{ $courseNameAr }}</h2>
            <p class="course-name-en">{{ $courseNameEn }}</p>
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
                <p class="footer-value">{{ $courseDate }}</p>
            </div>

            {{-- <div class="footer-seal">
                <div class="seal-ring">
                    <img src="{{ asset('assets/images/evorq-seal.webp') }}" alt="Seal">
                </div>
            </div> --}}

            <div class="footer-org">
                <p class="footer-org-ar"><span>إيفورك</span> للتكنولوجيا</p>
                <p class="footer-org-en">EVORQ TECHNOLOGIES</p>
            </div>
            <div class="footer-spacer" aria-hidden="true"></div>
        </footer>

    </div>{{-- /cert-content --}}
</div>{{-- /certificate --}}

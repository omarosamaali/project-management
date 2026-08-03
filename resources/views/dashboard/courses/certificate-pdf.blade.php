<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: 700;
            src: url('{{ storage_path('fonts/Amiri-Bold.ttf') }}') format('truetype');
        }

        /* Exact academy certificate canvas: 900×640 CSS px → 675×480 pt */
        @page { margin: 0; size: 675pt 480pt; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            margin: 0;
            padding: 0;
            width: 675pt;
            height: 480pt;
            overflow: hidden;
            background: #FDFAF3;
            color: #1C1C2E;
            font-family: 'Amiri', DejaVu Sans, sans-serif;
        }
        .ar {
            direction: ltr;
            unicode-bidi: bidi-override;
        }
        .certificate {
            width: 675pt;
            height: 480pt;
            background: #FDFAF3;
            border: 2.5pt solid #0D2444;
            padding: 10pt;
            overflow: hidden;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .inner {
            border: 1pt solid #000;
            width: 100%;
            height: 100%;
            padding: 22pt 36pt 18pt;
            position: relative;
            overflow: hidden;
        }
        .corner {
            position: absolute;
            width: 18pt;
            height: 18pt;
            border: 1.2pt solid #000;
        }
        .corner-tl { top: 5pt; left: 5pt; border-right: 0; border-bottom: 0; }
        .corner-tr { top: 5pt; right: 5pt; border-left: 0; border-bottom: 0; }
        .corner-bl { bottom: 5pt; left: 5pt; border-right: 0; border-top: 0; }
        .corner-br { bottom: 5pt; right: 5pt; border-left: 0; border-top: 0; }
        .watermark {
            position: absolute;
            top: 34%;
            left: 50%;
            width: 150pt;
            margin-left: -75pt;
            opacity: 0.08;
            text-align: center;
        }
        .watermark img { width: 150pt; height: auto; }
        .header { width: 100%; margin-bottom: 4pt; }
        .header td { vertical-align: middle; }
        .logo { width: 58pt; height: auto; }
        .ornament { text-align: center; font-size: 11pt; color: #000; }
        .title-ar {
            font-family: 'Amiri', DejaVu Sans, sans-serif;
            font-size: 16pt;
            font-weight: 700;
            color: #0D2444;
            margin: 0 0 2pt;
            text-align: left;
        }
        .title-en {
            font-size: 8.5pt;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
            color: #0f0f0f;
            margin: 0;
            text-align: left;
            direction: ltr;
        }
        .rule-wrap { width: 100%; margin: 10pt 0; }
        .rule-wrap td { vertical-align: middle; }
        .rule-line { border-top: 1pt solid #000; height: 0; }
        .rule-diamond { width: 14pt; text-align: center; font-size: 7pt; color: #000; }
        .certify {
            text-align: center;
            font-size: 11pt;
            color: #4A4A6A;
            margin: 6pt 0 4pt;
        }
        .certify-en { font-style: italic; direction: ltr; unicode-bidi: embed; }
        .name {
            text-align: center;
            font-family: 'Amiri', DejaVu Sans, sans-serif;
            font-size: 28pt;
            font-weight: 700;
            color: #0D2444;
            margin: 4pt 0 2pt;
            line-height: 1.15;
        }
        .underline {
            width: 40%;
            margin: 0 auto 8pt;
            border-bottom: 1.2pt solid #000;
        }
        .completion {
            text-align: center;
            font-size: 11pt;
            color: #4A4A6A;
            margin: 4pt 0 3pt;
        }
        .course-ar {
            text-align: center;
            font-family: 'Amiri', DejaVu Sans, sans-serif;
            font-size: 16pt;
            font-weight: 700;
            color: #1A3A6E;
            margin: 2pt 0;
        }
        .course-en {
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #0f0f0f;
            margin: 0 0 6pt;
            direction: ltr;
        }
        .footer { width: 100%; margin-top: 8pt; }
        .footer td { vertical-align: bottom; font-size: 9.5pt; }
        .footer-label { color: #0f0f0f; margin: 0 0 2pt; text-align: right; }
        .footer-value {
            margin: 0;
            font-weight: 700;
            color: #1C1C2E;
            text-align: right;
            direction: ltr;
        }
        .org-ar {
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            color: #0D2444;
            margin: 0;
        }
        .org-en {
            text-align: center;
            font-size: 8.5pt;
            letter-spacing: 1.5pt;
            color: #0f0f0f;
            margin: 2pt 0 0;
            direction: ltr;
        }
    </style>
</head>
<body>
@php
    use App\Support\CertificateArabic;

    $courseDate = optional($payment->course->start_date)->format('Y-m-d')
        ?? optional($payment->created_at)->format('Y-m-d')
        ?? now()->format('Y-m-d');

    $logoCandidates = [
        public_path('assets/images/logo-cert.png'),
        public_path('assets/images/white-logo.png'),
        public_path('assets/images/logo.webp'),
    ];
    $logo = collect($logoCandidates)->first(fn ($path) => is_file($path));

    $titleAr = CertificateArabic::glyphs('افادة حضور دورة تدريبية');
    $certifyAr = CertificateArabic::glyphs('نشهد بأن المتدرب');
    $completion = CertificateArabic::glyphs('قد أتمَّ بنجاح الدورة التدريبية بعنوان');
    $courseAr = CertificateArabic::glyphs((string) ($payment->course->name_ar ?? ''));
    $trainee = CertificateArabic::glyphs((string) ($payment->user->name ?? ''));
    $dateLabel = CertificateArabic::glyphs('تاريخ الدورة');
    $orgAr = CertificateArabic::glyphs('إيفورك للتكنولوجيا');
@endphp
<div class="certificate">
    <div class="inner">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        @if($logo)
        <div class="watermark">
            <img src="{{ $logo }}" alt="">
        </div>
        @endif

        <table class="header" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 58%; text-align: left;">
                    <p class="title-ar ar">{{ $titleAr }}</p>
                    <p class="title-en">CERTIFICATE OF COMPLETION</p>
                </td>
                <td style="width: 12%;" class="ornament">◆</td>
                <td style="width: 30%; text-align: right;">
                    @if($logo)
                    <img class="logo" src="{{ $logo }}" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>

        <table class="rule-wrap" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 46%;"><div class="rule-line"></div></td>
                <td class="rule-diamond">◆</td>
                <td style="width: 46%;"><div class="rule-line"></div></td>
            </tr>
        </table>

        <p class="certify">
            <span class="ar">{{ $certifyAr }}</span>
            <span class="certify-en"> / This is to certify that</span>
        </p>
        <p class="name ar">{{ $trainee }}</p>
        <div class="underline"></div>

        <p class="completion ar">{{ $completion }}</p>
        <p class="course-ar ar">{{ $courseAr }}</p>
        <p class="course-en">{{ $payment->course->name_en }}</p>

        <table class="rule-wrap" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 46%;"><div class="rule-line"></div></td>
                <td class="rule-diamond">◆</td>
                <td style="width: 46%;"><div class="rule-line"></div></td>
            </tr>
        </table>

        <table class="footer" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 33%;">&nbsp;</td>
                <td style="width: 34%; text-align: center;">
                    <p class="org-ar ar">{{ $orgAr }}</p>
                    <p class="org-en">EVORQ TECHNOLOGIES</p>
                </td>
                <td style="width: 33%;">
                    <p class="footer-label ar">{{ $dateLabel }}</p>
                    <p class="footer-value">{{ $courseDate }}</p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>

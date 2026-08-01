<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة حضور - {{ $payment->user->name }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap"
        rel="stylesheet">
    @include('dashboard.courses.partials.certificate-document-styles')
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #d6d2c8;
            font-family: 'Amiri', serif;
        }

        .cert-viewer {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem 1rem 2rem;
        }

        .cert-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            width: min(100%, 210mm);
            display: flex;
            justify-content: center;
            gap: .65rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            padding: .75rem;
            background: rgba(13, 36, 68, .94);
            border-radius: .75rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.18);
        }

        .cert-toolbar button,
        .cert-toolbar a {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border: 0;
            border-radius: .5rem;
            padding: .55rem 1.1rem;
            font-family: 'Amiri', serif;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            background: #0b8f7f;
        }

        .cert-toolbar .is-muted {
            background: rgba(255,255,255,.12);
        }

        .cert-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            box-shadow: 0 18px 50px rgba(0,0,0,.22);
            padding: 12mm;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        /* Portrait A4 certificate — fills the sheet fully */
        .certificate.is-portrait {
            width: 100%;
            max-width: none;
            min-height: 100%;
            height: 100%;
            padding: 28px 26px 32px;
            box-shadow: none;
        }

        .certificate.is-portrait .logo-wrap img { height: 64px; }
        .certificate.is-portrait .cert-title h2 { font-size: 1.45rem; }
        .certificate.is-portrait .cert-title p { font-size: .88rem; }
        .certificate.is-portrait .trainee-name { font-size: 2.7rem; }
        .certificate.is-portrait .course-name-ar { font-size: 1.55rem; }
        .certificate.is-portrait .course-name-en { font-size: 1rem; }
        .certificate.is-portrait .certify-text,
        .certificate.is-portrait .completion-text { font-size: 1.05rem; }
        .certificate.is-portrait .name-underline { width: min(280px, 70%); }
        .certificate.is-portrait .cert-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: calc(297mm - 24mm - 8px);
            height: 100%;
            padding: 10px 8px 6px;
        }
        .certificate.is-portrait .trainee-section { margin: 1.4rem 0 .8rem; }
        .certificate.is-portrait .course-section { margin: .9rem 0 .8rem; }
        .certificate.is-portrait .cert-footer { margin-top: auto; padding-top: 1rem; }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html, body {
                background: #fff !important;
                width: 210mm;
                height: 297mm;
            }

            .cert-toolbar { display: none !important; }

            .cert-viewer {
                min-height: 0;
                padding: 0;
                display: block;
            }

            .cert-sheet {
                width: 210mm;
                min-height: 297mm;
                height: 297mm;
                margin: 0;
                padding: 12mm;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .certificate.is-portrait {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 900px) {
            .cert-sheet {
                width: min(100%, 210mm);
                min-height: auto;
                padding: 4vw;
                transform-origin: top center;
            }
            .certificate.is-portrait .cert-content {
                min-height: 0;
            }
            .certificate.is-portrait .trainee-name { font-size: clamp(1.8rem, 8vw, 2.7rem); }
        }
    </style>
</head>
<body>
@php
    $courseDate = optional($payment->course->start_date)->format('Y-m-d')
        ?? optional($payment->created_at)->format('Y-m-d')
        ?? now()->format('Y-m-d');
@endphp

<div class="cert-viewer">
    <div class="cert-toolbar no-print">
        <button type="button" id="printCertificateBtn">
            طباعة / حفظ PDF
        </button>
        <a href="{{ route('dashboard.my_courses.index') }}" class="is-muted">العودة لدوراتي</a>
    </div>

    <div class="cert-sheet" id="certSheet">
        @include('dashboard.courses.partials.certificate-document', [
            'traineeName' => $payment->user->name,
            'courseNameAr' => $payment->course->name_ar,
            'courseNameEn' => $payment->course->name_en,
            'courseDate' => $courseDate,
            'certificateId' => 'certificate',
            'certificateClass' => 'is-portrait',
        ])
    </div>
</div>

<script>
    document.getElementById('printCertificateBtn')?.addEventListener('click', function () {
        window.print();
    });
</script>
</body>
</html>

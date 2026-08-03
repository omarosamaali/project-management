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
            width: min(100%, 900px);
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

        /* Same landscape frame as academy homepage certificate (900 × 640). */
        .cert-sheet {
            width: min(100%, 900px);
            aspect-ratio: 900 / 640;
            background: transparent;
            box-shadow: 0 18px 50px rgba(0,0,0,.22);
            display: flex;
            align-items: stretch;
            justify-content: center;
            overflow: hidden;
            border-radius: .35rem;
        }

        .cert-sheet .certificate {
            width: 900px;
            min-height: 640px;
            height: 640px;
            max-width: none;
            box-shadow: none;
            flex-shrink: 0;
        }

        @page {
            size: 900px 640px;
            margin: 0;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html, body {
                background: #fff !important;
                width: 900px;
                height: 640px;
            }

            .cert-toolbar { display: none !important; }

            .cert-viewer {
                min-height: 0;
                padding: 0;
                display: block;
            }

            .cert-sheet {
                width: 900px;
                height: 640px;
                aspect-ratio: auto;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                page-break-after: avoid;
                page-break-inside: avoid;
                overflow: visible;
            }

            [data-cert-scale] {
                transform: none !important;
                width: 900px !important;
            }

            .cert-sheet .certificate {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 940px) {
            .cert-sheet {
                width: min(100%, 900px);
                height: auto;
            }
            .cert-sheet-scale {
                width: 900px;
                transform-origin: top center;
            }
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
        <a href="{{ route('dashboard.courses.certificate', ['payment' => $payment->id, 'pdf' => 1]) }}" class="is-muted">
            عرض PDF
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}" class="is-muted">العودة لدوراتي</a>
    </div>

    <div class="cert-sheet" id="certSheet" data-cert-frame>
        <div data-cert-scale>
            @include('dashboard.courses.partials.certificate-document', [
                'traineeName' => $payment->user->name,
                'courseNameAr' => $payment->course->name_ar,
                'courseNameEn' => $payment->course->name_en,
                'courseDate' => $courseDate,
                'certificateId' => 'certificate',
            ])
        </div>
    </div>
</div>

<script>
    (function () {
        function fitCertificate() {
            var frame = document.querySelector('[data-cert-frame]');
            var scaleEl = document.querySelector('[data-cert-scale]');
            if (!frame || !scaleEl) return;
            var frameW = frame.clientWidth;
            if (!frameW) return;
            var scale = frameW / 900;
            scaleEl.style.width = '900px';
            scaleEl.style.transformOrigin = 'top left';
            scaleEl.style.transform = 'scale(' + scale + ')';
            frame.style.height = (640 * scale) + 'px';
        }

        fitCertificate();
        window.addEventListener('resize', fitCertificate);
        document.getElementById('printCertificateBtn')?.addEventListener('click', function () {
            window.print();
        });
    })();
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.certificate_pdf_viewer_title') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap"
        rel="stylesheet">
    @include('dashboard.courses.partials.certificate-document-styles')
    <style>
        :root {
            --bg: #0f172a;
            --panel: #1e293b;
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent-2: #059669;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: "Cairo", "Segoe UI", Tahoma, sans-serif;
        }
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: .75rem 1rem;
            background: var(--panel);
            border-bottom: 1px solid rgba(148, 163, 184, .25);
        }
        .toolbar h1 { margin: 0; font-size: .95rem; font-weight: 800; }
        .toolbar p { margin: .15rem 0 0; font-size: .75rem; color: var(--muted); }
        .actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem .9rem;
            border-radius: .7rem;
            border: 0;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: .82rem;
            cursor: pointer;
            background: #334155;
        }
        .btn-primary { background: var(--accent-2); }
        .stage {
            padding: 1.25rem 1rem 2rem;
            display: flex;
            justify-content: center;
        }
        .cert-frame {
            width: min(100%, 900px);
            overflow: hidden;
            border-radius: .35rem;
            box-shadow: 0 18px 50px rgba(0,0,0,.45);
            background: #FDFAF3;
        }
        .cert-scale {
            width: 900px;
            transform-origin: top left;
        }
        .cert-scale .certificate {
            width: 900px;
            min-height: 640px;
            height: 640px;
            box-shadow: none;
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
            html, body { background: #fff !important; }
            .toolbar { display: none !important; }
            .stage { padding: 0; }
            .cert-frame {
                width: 900px;
                height: 640px;
                box-shadow: none;
                border-radius: 0;
                overflow: visible;
            }
            .cert-scale {
                transform: none !important;
                width: 900px !important;
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
    <div class="toolbar no-print">
        <div>
            <h1>{{ __('messages.certificate_pdf_viewer_title') }}</h1>
            <p>{{ $payment->course->name_ar ?? '' }} — {{ $payment->user->name ?? '' }}</p>
        </div>
        <div class="actions">
            <button type="button" id="downloadBtn" class="btn btn-primary">
                {{ __('messages.certificate_pdf_download') }}
            </button>
            <button type="button" id="printBtn" class="btn">
                طباعة / حفظ PDF
            </button>
            <a href="{{ route('dashboard.courses.certificate', $payment->id) }}" class="btn">
                {{ __('messages.certificate_html_view') }}
            </a>
        </div>
    </div>

    <div class="stage">
        <div class="cert-frame" data-cert-frame>
            <div class="cert-scale" data-cert-scale>
                @include('dashboard.courses.partials.certificate-document', [
                    'traineeName' => $payment->user->name,
                    'courseNameAr' => $payment->course->name_ar,
                    'courseNameEn' => $payment->course->name_en,
                    'courseDate' => $courseDate,
                    'certificateId' => 'certificate-pdf-view',
                ])
            </div>
        </div>
    </div>

    <script>
    (function () {
        const streamUrl = @json($streamUrl);
        const fileName = @json($filename);

        function fitCertificate() {
            const frame = document.querySelector('[data-cert-frame]');
            const scaleEl = document.querySelector('[data-cert-scale]');
            if (!frame || !scaleEl) return;
            const frameW = frame.clientWidth;
            if (!frameW) return;
            const scale = frameW / 900;
            scaleEl.style.transform = 'scale(' + scale + ')';
            frame.style.height = (640 * scale) + 'px';
        }

        fitCertificate();
        window.addEventListener('resize', fitCertificate);

        document.getElementById('printBtn')?.addEventListener('click', function () {
            window.print();
        });

        function base64ToPdfBlob(b64) {
            const binary = atob(b64);
            const len = binary.length;
            const bytes = new Uint8Array(len);
            for (let i = 0; i < len; i++) bytes[i] = binary.charCodeAt(i);
            return new Blob([bytes], { type: 'application/pdf' });
        }

        document.getElementById('downloadBtn')?.addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            fetch(streamUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(async (res) => {
                if (!res.ok) throw new Error('fail');
                const payload = await res.json();
                if (!payload || !payload.ok || !payload.data) throw new Error('fail');
                const blob = base64ToPdfBlob(payload.data);
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                a.rel = 'noopener';
                document.body.appendChild(a);
                a.click();
                a.remove();
                setTimeout(() => URL.revokeObjectURL(url), 1500);
            }).catch(() => {
                // Fallback: browser print → Save as PDF keeps exact HTML fonts/spacing.
                window.print();
            }).finally(() => {
                btn.disabled = false;
            });
        });
    })();
    </script>
</body>
</html>

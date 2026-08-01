<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #1C1C2E;
            direction: rtl;
            background: #FDFAF3;
        }
        .sheet {
            border: 2px solid #000;
            padding: 18px 16px;
            min-height: 250mm;
            position: relative;
        }
        .inner {
            border: 1px solid rgba(0,0,0,.25);
            padding: 22px 18px 18px;
            min-height: 240mm;
        }
        .header {
            width: 100%;
            margin-bottom: 18px;
        }
        .header td { vertical-align: middle; }
        .logo { width: 72px; height: auto; }
        .title-ar {
            font-size: 20px;
            font-weight: bold;
            color: #0D2444;
            margin: 0 0 4px;
            text-align: right;
        }
        .title-en {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #0f0f0f;
            margin: 0;
            text-align: right;
        }
        .rule {
            border-top: 1px solid #000;
            margin: 16px 0;
            height: 0;
        }
        .certify {
            text-align: center;
            font-size: 14px;
            color: #4A4A6A;
            margin: 10px 0 8px;
        }
        .name {
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            color: #0D2444;
            margin: 8px 0 6px;
            line-height: 1.2;
        }
        .underline {
            width: 55%;
            margin: 0 auto 16px;
            border-bottom: 1px solid #000;
        }
        .completion {
            text-align: center;
            font-size: 14px;
            color: #4A4A6A;
            margin: 8px 0;
        }
        .course-ar {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #1A3A6E;
            margin: 8px 0 4px;
        }
        .course-en {
            text-align: center;
            font-size: 13px;
            font-style: italic;
            color: #0f0f0f;
            margin: 0 0 18px;
        }
        .footer {
            width: 100%;
            margin-top: 40px;
        }
        .footer td {
            vertical-align: bottom;
            font-size: 12px;
        }
        .footer-label {
            color: #0f0f0f;
            margin: 0 0 4px;
            letter-spacing: 1px;
        }
        .footer-value {
            margin: 0;
            font-weight: bold;
            color: #1C1C2E;
        }
        .org-ar {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #0D2444;
            margin: 0;
        }
        .org-en {
            text-align: center;
            font-size: 11px;
            letter-spacing: 2px;
            color: #0f0f0f;
            margin: 4px 0 0;
        }
    </style>
</head>
<body>
@php
    $courseDate = optional($payment->course->start_date)->format('Y-m-d')
        ?? optional($payment->created_at)->format('Y-m-d')
        ?? now()->format('Y-m-d');
    $logoCandidates = [
        public_path('assets/images/academy_logo.png'),
        public_path('assets/images/white-logo.png'),
        public_path('assets/images/logo.webp'),
    ];
    $logo = collect($logoCandidates)->first(fn ($path) => is_file($path));
@endphp
<div class="sheet">
    <div class="inner">
        <table class="header" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 30%; text-align: right;">
                    @if($logo)
                    <img class="logo" src="{{ $logo }}" alt="Logo">
                    @endif
                </td>
                <td style="width: 70%; text-align: right;">
                    <p class="title-ar">افادة حضور دورة تدريبية</p>
                    <p class="title-en">Certificate of Completion</p>
                </td>
            </tr>
        </table>

        <div class="rule"></div>

        <p class="certify">نشهد بأن المتدرب / This is to certify that</p>
        <p class="name">{{ $payment->user->name }}</p>
        <div class="underline"></div>

        <p class="completion">قد أتمَّ بنجاح الدورة التدريبية بعنوان</p>
        <p class="course-ar">{{ $payment->course->name_ar }}</p>
        <p class="course-en">{{ $payment->course->name_en }}</p>

        <div class="rule"></div>

        <table class="footer" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 33%; text-align: right;">
                    <p class="footer-label">تاريخ الدورة</p>
                    <p class="footer-value">{{ $courseDate }}</p>
                </td>
                <td style="width: 34%; text-align: center;">
                    <p class="org-ar">إيفورك للتكنولوجيا</p>
                    <p class="org-en">EVORQ TECHNOLOGIES</p>
                </td>
                <td style="width: 33%;">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>

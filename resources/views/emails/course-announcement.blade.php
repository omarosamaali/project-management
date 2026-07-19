<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#eef1f6; font-family:'Segoe UI', Tahoma, Arial, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        أطلقنا دورة تدريبية جديدة: {{ $courseName }} — سجّل الآن.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef1f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(13,36,68,.12);">

                    {{-- Header / brand --}}
                    <tr>
                        <td style="background-color:#0D2444; background-image:linear-gradient(135deg,#0D2444 0%,#1A3A6E 100%); padding:28px 24px; text-align:center;">
                            <img src="{{ $logoUrl }}" alt="EVORQ" width="140"
                                style="display:inline-block; max-width:140px; height:auto; margin:0 auto;">
                        </td>
                    </tr>

                    {{-- Course image --}}
                    <tr>
                        <td style="padding:0;">
                            <img src="{{ $imageUrl }}" alt="{{ $courseName }}" width="600"
                                style="display:block; width:100%; max-width:600px; height:auto; object-fit:cover;">
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 32px 8px 32px; text-align:right;">
                            <span style="display:inline-block; background-color:#e8f0fe; color:#1A3A6E; font-size:13px; font-weight:700; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                دورة تدريبية جديدة
                            </span>
                            <h1 style="margin:0 0 12px 0; color:#0D2444; font-size:24px; line-height:1.4;">
                                {{ $courseName }}
                            </h1>
                            <p style="margin:0 0 8px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                السلام عليكم {{ $userName }}،
                            </p>
                            <p style="margin:0 0 20px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                يسعدنا إبلاغك بإطلاق دورة تدريبية جديدة لدى <strong>إيفورك للتكنولوجيا</strong>.
                                سارِع بحجز مقعدك والاطلاع على التفاصيل الكاملة والتسجيل من خلال الزر أدناه.
                            </p>

                            @if(!empty($courseDescription))
                            <p style="margin:0 0 24px 0; color:#6b7280; font-size:15px; line-height:1.9;">
                                {{ $courseDescription }}
                            </p>
                            @endif
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:0 32px 32px 32px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:10px; background-color:#0D2444; background-image:linear-gradient(135deg,#0D2444 0%,#1A3A6E 100%);">
                                        <a href="{{ $courseUrl }}" target="_blank"
                                            style="display:inline-block; padding:14px 40px; color:#ffffff; font-size:16px; font-weight:700; text-decoration:none; border-radius:10px;">
                                            عرض الدورة والتسجيل
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0; color:#9ca3af; font-size:13px; word-break:break-all;">
                                أو انسخ الرابط: <a href="{{ $courseUrl }}" style="color:#1A3A6E;">{{ $courseUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f7f8fa; padding:24px 32px; text-align:center; border-top:1px solid #eceef2;">
                            <p style="margin:0 0 4px 0; color:#0D2444; font-size:15px; font-weight:700;">
                                إيفورك للتكنولوجيا — EVORQ TECHNOLOGIES
                            </p>
                            <p style="margin:0; color:#9ca3af; font-size:12px;">
                                هذه رسالة آلية، لمزيد من الاستفسارات تواصل معنا عبر
                                <a href="mailto:info@evorq.com" style="color:#1A3A6E;">info@evorq.com</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب محاضر بانتظار الموافقة</title>
</head>
<body style="margin:0; padding:0; background-color:#eef1f6; font-family:'Segoe UI', Tahoma, Arial, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        محاضر جديد بانتظار موافقتك في أكاديمية إيفورك: {{ $trainerName }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef1f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(13,36,68,.12);">

                    <tr>
                        <td style="background-color:#0D2444; background-image:linear-gradient(135deg,#0D2444 0%,#1A3A6E 100%); padding:28px 24px; text-align:center;">
                            <img src="{{ $logoUrl }}" alt="EVORQ" width="140"
                                style="display:inline-block; max-width:140px; height:auto; margin:0 auto;">
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#d97706; background-image:linear-gradient(90deg,#d97706 0%,#f59e0b 100%); padding:18px 32px; text-align:center;">
                            <p style="margin:0; color:#ffffff; font-size:18px; font-weight:800; letter-spacing:.02em;">
                                طلب محاضر جديد بانتظار الموافقة
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 8px 32px; text-align:right;">
                            <span style="display:inline-block; background-color:#fff7ed; color:#c2410c; font-size:13px; font-weight:700; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                يحتاج مراجعة الإدارة
                            </span>
                            <h1 style="margin:0 0 12px 0; color:#0D2444; font-size:22px; line-height:1.4;">
                                مرحباً،
                            </h1>
                            <p style="margin:0 0 16px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                تم تسجيل محاضر جديد في
                                <strong>أكاديمية إيفورك</strong>
                                وحسابه بانتظار موافقتك قبل أن يتمكن من الدخول.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px 0;">
                                <tr>
                                    <td style="background-color:#f7f8fa; border:1px solid #e5e9f0; border-radius:12px; padding:16px 18px;">
                                        <p style="margin:0 0 10px 0; color:#6b7280; font-size:13px;">اسم المحاضر</p>
                                        <p style="margin:0 0 14px 0; color:#0D2444; font-size:17px; font-weight:700;">{{ $trainerName }}</p>

                                        <p style="margin:0 0 4px 0; color:#6b7280; font-size:13px;">البريد الإلكتروني</p>
                                        <p style="margin:0 0 14px 0; color:#0D2444; font-size:15px; font-weight:600;">{{ $trainerEmail }}</p>

                                        @if(!empty($trainerPhone))
                                        <p style="margin:0 0 4px 0; color:#6b7280; font-size:13px;">الهاتف</p>
                                        <p style="margin:0 0 14px 0; color:#0D2444; font-size:15px; font-weight:600; direction:ltr; text-align:right;">{{ $trainerPhone }}</p>
                                        @endif

                                        @if(!empty($categoryName))
                                        <p style="margin:0 0 4px 0; color:#6b7280; font-size:13px;">مجال التدريب</p>
                                        <p style="margin:0; color:#0D2444; font-size:15px; font-weight:700;">{{ $categoryName }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 32px 32px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:10px; background-color:#0D2444; background-image:linear-gradient(135deg,#0D2444 0%,#1A3A6E 100%);">
                                        <a href="{{ $reviewUrl }}" target="_blank"
                                            style="display:inline-block; padding:14px 40px; color:#ffffff; font-size:16px; font-weight:700; text-decoration:none; border-radius:10px;">
                                            مراجعة الطلب والموافقة
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0; color:#9ca3af; font-size:13px; word-break:break-all;">
                                أو انسخ الرابط:
                                <a href="{{ $reviewUrl }}" style="color:#1A3A6E;">{{ $reviewUrl }}</a>
                            </p>
                        </td>
                    </tr>

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

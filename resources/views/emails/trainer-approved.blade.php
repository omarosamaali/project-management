<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمت الموافقة على حسابك</title>
</head>
<body style="margin:0; padding:0; background-color:#eef1f6; font-family:'Segoe UI', Tahoma, Arial, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        مبروك! تمت الموافقة على حسابك كمحاضر في أكاديمية إيفورك.
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
                        <td style="background-color:#b8893d; background-image:linear-gradient(90deg,#b8893d 0%,#d4a85a 100%); padding:18px 32px; text-align:center;">
                            <p style="margin:0; color:#ffffff; font-size:18px; font-weight:800; letter-spacing:.02em;">
                                ✓ تمت الموافقة على حسابك
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 8px 32px; text-align:right;">
                            <span style="display:inline-block; background-color:#eaf7f0; color:#1f7a4d; font-size:13px; font-weight:700; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                حساب محاضر مفعّل
                            </span>
                            <h1 style="margin:0 0 12px 0; color:#0D2444; font-size:24px; line-height:1.4;">
                                مرحباً {{ $userName }}
                            </h1>
                            <p style="margin:0 0 16px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                يسعدنا إبلاغك بأن طلب تسجيلك كمحاضر في
                                <strong>أكاديمية إيفورك</strong>
                                قد تمت الموافقة عليه من قبل الإدارة.
                            </p>
                            <p style="margin:0 0 16px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                يمكنك الآن تسجيل الدخول والبدء في إنشاء وإدارة دوراتك التدريبية.
                            </p>

                            @if(!empty($categoryName))
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px 0;">
                                <tr>
                                    <td style="background-color:#f7f8fa; border:1px solid #e5e9f0; border-radius:12px; padding:14px 18px;">
                                        <p style="margin:0; color:#6b7280; font-size:13px;">مجال التدريب</p>
                                        <p style="margin:4px 0 0 0; color:#0D2444; font-size:16px; font-weight:700;">{{ $categoryName }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 32px 32px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:10px; background-color:#0D2444; background-image:linear-gradient(135deg,#0D2444 0%,#1A3A6E 100%);">
                                        <a href="{{ $loginUrl }}" target="_blank"
                                            style="display:inline-block; padding:14px 40px; color:#ffffff; font-size:16px; font-weight:700; text-decoration:none; border-radius:10px;">
                                            تسجيل الدخول الآن
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0; color:#9ca3af; font-size:13px; word-break:break-all;">
                                أو انسخ الرابط:
                                <a href="{{ $loginUrl }}" style="color:#1A3A6E;">{{ $loginUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 28px 32px; text-align:right;">
                            <p style="margin:0 0 10px 0; color:#0D2444; font-size:15px; font-weight:700;">الخطوات التالية:</p>
                            <ul style="margin:0; padding:0 18px 0 0; color:#4A4A6A; font-size:14px; line-height:1.9;">
                                <li>سجّل الدخول بحسابك</li>
                                <li>أنشئ دورتك الأولى وأضف المحتوى التعليمي</li>
                                <li>تابع المتدربين والتقييمات من لوحة التحكم</li>
                            </ul>
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

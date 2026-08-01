<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق — أكاديمية إيفورك</title>
</head>
<body style="margin:0; padding:0; background-color:#eef1f6; font-family:'Segoe UI', Tahoma, Arial, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        كود التحقق الخاص بك في أكاديمية إيفورك: {{ $otp }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef1f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(13,36,68,.12);">

                    {{-- Brand header --}}
                    <tr>
                        <td style="background-color:#061525; background-image:linear-gradient(135deg,#061525 0%,#0e3a5c 100%); padding:28px 24px; text-align:center;">
                            <img src="{{ $logoUrl }}" alt="أكاديمية إيفورك" width="160"
                                style="display:inline-block; max-width:160px; height:auto; margin:0 auto;">
                        </td>
                    </tr>

                    {{-- Accent bar --}}
                    <tr>
                        <td style="background-color:#ff3d7a; background-image:linear-gradient(90deg,#ff3d7a 0%,#e11d62 100%); padding:16px 32px; text-align:center;">
                            <p style="margin:0; color:#ffffff; font-size:17px; font-weight:800; letter-spacing:.02em;">
                                تأكيد الحساب — كود التحقق
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 32px 12px 32px; text-align:right;">
                            <span style="display:inline-block; background-color:#ffe4ec; color:#e11d62; font-size:12px; font-weight:800; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                استخدام لمرة واحدة
                            </span>

                            <h1 style="margin:0 0 12px 0; color:#061525; font-size:24px; line-height:1.4;">
                                مرحباً {{ $userName }}
                            </h1>
                            <p style="margin:0 0 8px 0; color:#4A4A6A; font-size:16px; line-height:1.9;">
                                استخدم الكود التالي لإكمال تأكيد حسابك في
                                <strong style="color:#061525;">أكاديمية إيفورك</strong>.
                            </p>
                            <p style="margin:0 0 24px 0; color:#6b7280; font-size:14px; line-height:1.7;">
                                لا تشارك هذا الكود مع أي شخص. صلاحية الكود لعملية تحقق واحدة فقط.
                            </p>
                        </td>
                    </tr>

                    {{-- OTP block --}}
                    <tr>
                        <td style="padding:0 32px 28px 32px;" align="center">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f8fa; border:1px solid #e5e9f0; border-radius:14px;">
                                <tr>
                                    <td style="padding:22px 20px; text-align:center;">
                                        <p style="margin:0 0 10px 0; color:#6b7280; font-size:13px; font-weight:700;">
                                            كود التحقق
                                        </p>
                                        <p style="margin:0; color:#061525; font-size:40px; font-weight:800; letter-spacing:0.35em; direction:ltr; font-family:'Segoe UI', Consolas, monospace;">
                                            {{ $otp }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:0 32px 32px 32px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:10px; background-color:#ff3d7a; background-image:linear-gradient(135deg,#ff3d7a 0%,#e11d62 100%);">
                                        <a href="{{ $verifyUrl }}" target="_blank"
                                            style="display:inline-block; padding:14px 36px; color:#ffffff; font-size:16px; font-weight:700; text-decoration:none; border-radius:10px;">
                                            فتح صفحة التحقق
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0; color:#9ca3af; font-size:12px; word-break:break-all; line-height:1.6;">
                                أو انسخ الرابط:
                                <a href="{{ $verifyUrl }}" style="color:#0e3a5c;">{{ $verifyUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Tips --}}
                    <tr>
                        <td style="padding:0 32px 28px 32px; text-align:right;">
                            <p style="margin:0 0 10px 0; color:#061525; font-size:15px; font-weight:700;">ملاحظات هامة:</p>
                            <ul style="margin:0; padding:0 18px 0 0; color:#4A4A6A; font-size:14px; line-height:1.9;">
                                <li>أدخل الكود في صفحة التحقق مباشرة بعد استلامه</li>
                                <li>إذا لم تطلب هذا الكود، يمكنك تجاهل الرسالة بأمان</li>
                                <li>لطلب كود جديد استخدم زر إعادة الإرسال من صفحة التحقق</li>
                            </ul>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f7f8fa; padding:24px 32px; text-align:center; border-top:1px solid #eceef2;">
                            <p style="margin:0 0 4px 0; color:#061525; font-size:15px; font-weight:700;">
                                أكاديمية إيفورك — EVORQ Academy
                            </p>
                            <p style="margin:0; color:#9ca3af; font-size:12px;">
                                هذه رسالة آلية، للاستفسار تواصل معنا عبر
                                <a href="mailto:info@evorq.com" style="color:#0e3a5c;">info@evorq.com</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

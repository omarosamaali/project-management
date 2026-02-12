<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppOTPService;
use App\Models\User;

class OTPController extends Controller
{

    public function resend($type)
    {
        try {
            $user = User::find(Auth::id());

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            $newOtp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $user->otp = $newOtp;
            $user->save();

            if ($type === 'whatsapp') {
                // 1. تنظيف الرقم من المسافات تماماً
                $cleanPhone = str_replace(' ', '', $user->phone);

                // 2. التأكد من وجود كود الدولة (لو الرقم مصري وبيبدأ بـ 12 أو 012)
                // معظم خدمات الواتساب بتحتاج الرقم يبدأ بـ 201
                if (!str_starts_with($cleanPhone, '20') && !str_starts_with($cleanPhone, '+')) {
                    // لو بيبدأ بـ 012 شيل الصفر وحط 20
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '20' . substr($cleanPhone, 1);
                    } else {
                        // لو بيبدأ بـ 12 علطول حط 20
                        $cleanPhone = '20' . $cleanPhone;
                    }
                }

                Log::info("الرقم النهائي المرسل للـ API: " . $cleanPhone);

                $whatsappService = new \App\Services\WhatsAppOTPService();
                $whatsappService->sendOTP($cleanPhone, $newOtp);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال كود الواتساب للرقم: ' . $user->phone
                ]);
            }

            if ($type === 'email') {
                Mail::raw("كود التحقق الخاص بك هو: $newOtp", function ($message) use ($user) {
                    $message->to($user->email)->subject('تأكيد الحساب - كود جديد');
                });

                return response()->json(['success' => true, 'message' => 'تم إرسال الكود للإيميل']);
            }

            return response()->json(['success' => false, 'message' => 'نوع غير معروف'], 400);
        } catch (\Exception $e) {
            Log::error("خطأ الـ Resend: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'فشل: ' . $e->getMessage()], 500);
        }
    }


    // عرض صفحة التحقق
    public function showVerifyPage()
    {
        return view('auth.otp-verify');
    }

    // التحقق من كود الواتساب
    public function verifyWhatsapp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:4']);

        // جلب أحدث بيانات من الداتابيز فوراً
        $user = \App\Models\User::find(Auth::id());

        \Illuminate\Support\Facades\Log::info("تحقق واتساب - المدخل: [" . $request->otp . "] - المسجل: [" . ($user->otp ?? 'NULL') . "]");

        if ($user && !empty($user->otp) && strval($request->otp) === strval($user->otp)) {
            $user->update(['whatsapp_verified' => true]);

            return redirect()->back()->with('success_whatsapp', 'تأكيد الواتساب: تم التأكيد بنجاح ✅');
        }

        return back()->withErrors(['whatsapp_otp' => 'الكود غير صحيح، تأكد من إدخال آخر كود وصلك']);
    }
    public function verifyEmail(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:4']);

        // جلب المستخدم مباشرة من الجدول بالـ ID لضمان أحدث بيانات
        $user = \App\Models\User::find(auth()->id());

        \Illuminate\Support\Facades\Log::info("فحص نهائي - المدخل: [" . $request->otp . "] - الموجود في الجدول: [" . $user->otp . "]");

        if ($user && !empty($user->otp) && strval($request->otp) === strval($user->otp)) {
            $user->email_verified_at = now();
            $user->save(); // استخدام save أضمن من update في بعض الحالات

            return redirect()->back()->with('success', 'تم تأكيد البريد بنجاح ✅');
        }

        return back()->withErrors(['email_otp' => 'كود التحقق غير صالح']);
    }
}

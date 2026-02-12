<?php

namespace App\Http\Controllers;

use App\Models\User;
// use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail; // تأكد من إضافة هذا السطر في الأعلى

class PartnerRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-partner');
    }

    /**
     * إرسال كود التحقق عبر الواتساب (AJAX)
     */
    public function sendOtp(Request $request)
    {
        // 1. التحقق من وجود رقم الهاتف في الطلب
        $request->validate([
            'phone' => 'required|string',
        ]);

        // 2. توليد كود مكون من 4 أرقام (مثلاً: 5821)
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // 3. تخزين الكود في الجلسة (Session) للتحقق منه لاحقاً
        Session::put('whatsapp_otp_' . $request->phone, $otp);
        Session::put('whatsapp_otp_expiry_' . $request->phone, now()->addMinutes(10));

        // 4. استدعاء خدمة الواتساب لإرسال الكود
        $whatsappService = new WhatsAppOTPService();
        $isEnglish = app()->getLocale() !== 'ar';

        $result = $whatsappService->sendOTP($request->phone, $otp, $isEnglish);

        // 5. الرد على المتصفح بنتيجة الإرسال
        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال الكود المكون من 4 أرقام إلى واتساب'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'فشل في إرسال الكود، يرجى التحقق من الرقم'
        ], 500);
    }

    /**
     * تخزين بيانات الشريك
     */
    /**
     * تخزين بيانات الشريك وإرسال كود التحقق للواتساب
     */ 

    /**
     * تخزين بيانات الشريك وإرسال كود التحقق (واتساب + إيميل)
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'phone'              => 'required|string',
            'skills'             => 'required|array',
            'password'           => 'required|string|min:8|confirmed',
            'avatar'             => 'required|image|max:5120',
            'id_card_path'       => 'required|image|max:5120',
            'verification_video' => 'required|mimes:mp4,mov,avi|max:20480',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 2. رفع الملفات
                $avatarPath = $request->file('avatar')->store('partners/avatars', 'public');
                $idCardPath = $request->file('id_card_path')->store('partners/documents', 'public');
                $videoPath  = $request->file('verification_video')->store('partners/videos', 'public');

                // 3. توليد الكود
                $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                // 4. تنظيف الرقم (لضمان صيغة 2012...)
                $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
                $cleanPhone = ltrim($cleanPhone, '0');
                if (!str_starts_with($cleanPhone, '20') && !str_starts_with($cleanPhone, '966')) {
                    $cleanPhone = '20' . $cleanPhone;
                }

                // 5. نص الرسالة (مطابق للصورة تماماً)
                $messageText = "{$otpCode} هو كود التحقق الخاص بك. للحفاظ على أمانك، لا تشارك هذا الكود مع أي شخص.\n\nتنتهي صلاحيتها خلال 5 دقائق.";

                // 6. إنشاء المستخدم
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'phone'    => $request->phone,
                    'password' => \Hash::make($request->password),
                    'role'     => 'independent_partner',
                    'status'   => 'pending',
                    'otp'      => $otpCode,
                    // ... باقي الحقول
                ]);

                // 7. إرسال الإيميل
                try {
                    \Mail::raw($messageText, function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('كود التحقق الخاص بك');
                    });
                } catch (\Exception $e) {
                    \Log::error("Email Error: " . $e->getMessage());
                }

                // 8. إرسال الواتساب (تمرير النص المخصص)
                try {
                    $whatsappService = new \App\Services\WhatsAppOTPService();
                    // ملاحظة: تأكد أن ميثود sendOTP في الخدمة تقبل نصاً مخصصاً أو عدلها لترسل $messageText
                    $whatsappService->sendOTP($cleanPhone, $otpCode, false, $messageText);
                } catch (\Exception $e) {
                    \Log::error("WhatsApp Error: " . $e->getMessage());
                }

                return redirect()->route('login')->with('success', 'تم إرسال الكود للواتساب والإيميل.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ: ' . $e->getMessage())->withInput();
        }
    }
}

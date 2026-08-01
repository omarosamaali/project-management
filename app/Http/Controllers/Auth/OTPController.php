<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AcademyOtpMail;
use App\Models\User;
use App\Support\AuthUi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function resend($type)
    {
        try {
            $user = User::find(Auth::id());

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if ($type === 'email') {
                $this->issueAndSendEmailOtp($user);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني',
                ]);
            }

            if ($type === 'whatsapp') {
                if ($user->isTrainee()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'التحقق للمتدرب يتم عبر البريد الإلكتروني فقط',
                    ], 400);
                }

                $newOtp = $this->generateOtp();
                $user->otp = $newOtp;
                $user->save();

                $cleanPhone = str_replace(' ', '', (string) $user->phone);

                if ($cleanPhone !== '' && ! str_starts_with($cleanPhone, '20') && ! str_starts_with($cleanPhone, '+')) {
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '20'.substr($cleanPhone, 1);
                    } else {
                        $cleanPhone = '20'.$cleanPhone;
                    }
                }

                Log::info('الرقم النهائي المرسل للـ API: '.$cleanPhone);

                $whatsappService = new \App\Services\WhatsAppOTPService();
                $whatsappService->sendOTP($cleanPhone, $newOtp);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال كود الواتساب للرقم: '.$user->phone,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'نوع غير معروف'], 400);
        } catch (\Exception $e) {
            Log::error('خطأ الـ Resend: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'فشل: '.$e->getMessage()], 500);
        }
    }

    public function showVerifyPage()
    {
        $user = Auth::user();

        if ($user && $user->isTrainee()) {
            AuthUi::resolve(AuthUi::ACADEMY);

            if (empty($user->otp) && is_null($user->email_verified_at)) {
                $this->issueAndSendEmailOtp($user->fresh());
            }

            return view('auth.academy.otp-verify');
        }

        return view('auth.otp-verify');
    }

    public function verifyWhatsapp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:4']);

        $user = User::find(Auth::id());

        Log::info('تحقق واتساب - المدخل: ['.$request->otp.'] - المسجل: ['.($user->otp ?? 'NULL').']');

        if ($user && ! empty($user->otp) && strval($request->otp) === strval($user->otp)) {
            $user->update(['whatsapp_verified' => true]);

            return redirect()->back()->with('success_whatsapp', 'تأكيد الواتساب: تم التأكيد بنجاح ✅');
        }

        return back()->withErrors(['whatsapp_otp' => 'الكود غير صحيح، تأكد من إدخال آخر كود وصلك']);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:4']);

        $user = User::find(auth()->id());

        Log::info('فحص نهائي - المدخل: ['.$request->otp.'] - الموجود في الجدول: ['.($user->otp ?? 'NULL').']');

        if ($user && ! empty($user->otp) && strval($request->otp) === strval($user->otp)) {
            $user->email_verified_at = now();
            $user->otp = null;
            $user->save();

            if ($user->isTrainee()) {
                $intended = session()->pull('url.intended', route('academy.index'));

                return redirect()
                    ->to($intended)
                    ->with('success', 'تم تأكيد بريدك الإلكتروني بنجاح. مرحباً بك في الأكاديمية!');
            }

            return redirect()->back()->with('success', 'تم تأكيد البريد بنجاح ✅');
        }

        return back()->withErrors(['email_otp' => 'كود التحقق غير صالح']);
    }

    public function issueAndSendEmailOtp(User $user): string
    {
        $otp = $this->generateOtp();
        $user->otp = $otp;
        $user->save();

        Mail::to($user->email, $user->name)->send(new AcademyOtpMail($user, $otp));

        return $otp;
    }

    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}

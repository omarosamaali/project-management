<?php

namespace App\Http\Controllers;

use App\Services\ZiinaSystemPaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\System;
use App\Models\Payment;
use App\Models\SpecialRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;

class ZiinaPaymentController extends Controller
{
    private $ziinaHandler;

    public function __construct()
    {
        $this->ziinaHandler = new ZiinaSystemPaymentHandler();
    }

    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'system_id' => 'required|exists:systems,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        }

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        try {
            $system = System::findOrFail($request->system_id);

            $basePrice = (float) $system->price;
            $fees = ($basePrice * 0.079) + 2;
            $totalAmount = $basePrice + $fees;

            $successUrl = route('payment.success');
            $cancelUrl = route('payment.cancel');
            // $isTest = config('services.ziina.test_mode', true);

            $response = $this->ziinaHandler->createSystemPaymentIntent(
                $system,
                $successUrl,
                $cancelUrl,
                // $isTest
            );

            Payment::create([
                'user_id' => auth()->id(),
                'system_id' => $system->id,
                'payment_id' => $response['id'] ?? null,
                'amount' => $totalAmount,
                'original_price' => $basePrice,
                'fees' => round($fees, 2),
                'status' => 'pending',
                'payment_method' => 'ziina',
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $response['redirect_url'],
                'total_amount' => $totalAmount,
                'fees' => round($fees, 2),
            ]);
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'system_id' => $request->system_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        $systemId = $request->query('system_id');

        Log::info('بدء معالجة العودة من الدفع', ['id' => $paymentIntentId]);

        if ($paymentIntentId) {
            try {
                $paymentIntent = $this->ziinaHandler->getPaymentIntent($paymentIntentId);
                $payment = Payment::where('payment_id', $paymentIntentId)->first();

                if ($payment && ($paymentIntent['status'] === 'completed' || $paymentIntent['status'] === 'paid')) {
                    $payment->update(['status' => 'completed']);
                    Log::info('تم تحديث حالة الدفع في جدول Payments');

                    $payment->user->systems()->syncWithoutDetaching([$payment->system_id]);

                    try {
                        $newRequest = \App\Models\Requests::create([
                            'order_number' => 'REQ-' . strtoupper(substr($paymentIntentId, 0, 8)),
                            'system_id'    => $payment->system_id,
                            'client_id'    => $payment->user_id,
                            'status'       => 'جديد',
                        ]);

                        Log::info('تم إنشاء الطلب بنجاح في جدول requests', ['id' => $newRequest->id]);
                    } catch (\Exception $e) {
                        Log::error('فشل إنشاء السجل في جدول requests: ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::error('خطأ عام في دالة success: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard.requests.index')->with('success', 'تمت عملية الدفع بنجاح');
    }

    public function cancel(Request $request)
    {
        $systemId = $request->query('system_id');
        return view('payment.cancel', compact('systemId'));
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Ziina-Signature');

        if (!$this->ziinaHandler->validateWebhook($payload, $signature)) {
            Log::warning('Invalid webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        $paymentIntentId = $data['payment_intent_id'] ?? null;
        $status = $data['status'] ?? null;

        if ($paymentIntentId && $status) {
            $payment = Payment::where('payment_id', $paymentIntentId)->first();

            if ($payment) {
                $payment->update(['status' => $status === 'paid' ? 'completed' : $status]);

                if ($status === 'paid') {
                    $payment->user->systems()->syncWithoutDetaching([$payment->system_id]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // ============================================
    // دوال المشاريع الخاصة - Special Requests
    // ============================================

    public function createSpecialRequestPayment(Request $request)
    {
        try {
            $request->validate([
                'special_request_id' => 'required|exists:special_requests,id',
            ]);

            $specialRequest = SpecialRequest::findOrFail($request->special_request_id);

            if ($specialRequest->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول لهذا المشروع'
                ], 403);
            }

            if (!$specialRequest->price || $specialRequest->price <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تحديد سعر للمشروع'
                ], 400);
            }

            if ($specialRequest->total_paid >= $specialRequest->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم دفع هذا المشروع مسبقاً أو جزء منه'
                ], 400);
            }

            $basePrice = (float) $specialRequest->price;
            $fees = ($basePrice * 0.079) + 2;
            $totalAmount = $basePrice + $fees;

            $successUrl = route('payment.special-request.return') . '?special_request_id=' . $specialRequest->id;
            $cancelUrl = route('payment.cancel');
            // $isTest = config('services.ziina.test_mode', true);

            $response = $this->ziinaHandler->createInstallmentPaymentIntent(
                $specialRequest,
                $successUrl,
                $cancelUrl,
                // $isTest
            );

            $paymentData = [
                'user_id' => Auth::id(),
                'special_request_id' => $specialRequest->id,
                'request_payment_id' => null,
                'payment_id' => $response['id'] ?? null,
                'amount' => $totalAmount,
                'original_price' => $basePrice,
                'fees' => round($fees, 2),
                'status' => 'pending',
                'payment_method' => 'ziina',
                'currency' => 'AED',
                'system_id' => null,
            ];

            \App\Models\Payment::create($paymentData);
            $specialRequest->update(['status' => 'بانتظار الدفع']);

            return response()->json([
                'success' => true,
                'payment_url' => $response['redirect_url'],
                'total_amount' => $totalAmount,
                'fees' => round($fees, 2),
            ]);
        } catch (\Exception $e) {
            Log::error('Special Request Full Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في معالجة الدفع: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleReturn(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        $specialRequestId = $request->query('special_request_id');

        Log::info('بدء معالجة العودة من دفع الطلب الخاص', [
            'payment_id' => $paymentIntentId,
            'special_request_id' => $specialRequestId
        ]);

        if ($paymentIntentId) {
            try {
                $paymentIntent = $this->ziinaHandler->getPaymentIntent($paymentIntentId);
                $payment = Payment::where('payment_id', $paymentIntentId)->first();

                if ($payment && ($paymentIntent['status'] === 'completed' || $paymentIntent['status'] === 'paid')) {
                    $payment->update(['status' => 'completed']);
                    Log::info('تم تحديث حالة الدفع');

                    $specialRequest = SpecialRequest::find($payment->special_request_id);
                    if ($specialRequest) {
                        $specialRequest->update(['status' => 'in_progress']);
                        Log::info('تم تحديث حالة الطلب الخاص إلى قيد التنفيذ');
                    }

                    return redirect()
                        ->route('special-request.details', $payment->special_request_id)
                        ->with('success', 'تمت عملية الدفع بنجاح! سيتم البدء في تنفيذ مشروعك قريباً');
                } else {
                    Log::warning('حالة الدفع غير مكتملة', ['status' => $paymentIntent['status']]);
                }
            } catch (\Exception $e) {
                Log::error('خطأ في معالجة العودة: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('special-request.show')
            ->with('error', 'حدث خطأ في عملية الدفع');
    }

    public function handleCallback(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Ziina-Signature');

        Log::info('Received special request payment callback', ['payload' => $payload]);

        if (!$this->ziinaHandler->validateWebhook($payload, $signature)) {
            Log::warning('Invalid webhook signature for special request payment');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        $paymentIntentId = $data['payment_intent_id'] ?? null;
        $status = $data['status'] ?? null;

        if ($paymentIntentId && $status) {
            $payment = Payment::where('payment_id', $paymentIntentId)
                ->whereNotNull('special_request_id')
                ->first();

            if ($payment) {
                $payment->update(['status' => $status === 'paid' ? 'completed' : $status]);

                if ($status === 'paid') {
                    $specialRequest = SpecialRequest::find($payment->special_request_id);
                    if ($specialRequest) {
                        $specialRequest->update(['status' => 'in_progress']);
                        Log::info('تم تحديث حالة الطلب الخاص عبر callback');
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function initiateInstallmentPayment(Request $request, $paymentId)
    {
        try {
            $installment = \App\Models\RequestPayment::findOrFail($paymentId);

            if (!$installment->special_request_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الدفعة غير مرتبطة بطلب خاص'
                ], 400);
            }

            $specialRequest = $installment->specialRequest;

            if ($specialRequest->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بدفع هذه الدفعة'
                ], 403);
            }

            if ($installment->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم دفع هذه الدفعة مسبقًا'
                ], 400);
            }

            $basePrice = (float) $installment->amount;
            $fees = ($basePrice * 0.079) + 2;
            $totalAmount = $basePrice + $fees;

            $successUrl = route('payment.installment.return', ['installment' => $installment->id]);
            $cancelUrl = route('dashboard.special-request.show', $specialRequest->id);
            // $isTest = config('services.ziina.test_mode', true);

            $response = $this->ziinaHandler->createInstallmentPaymentIntent(
                $installment,
                $successUrl,
                $cancelUrl,
                // $isTest
            );

            $paymentData = [
                'user_id' => Auth::id(),
                'special_request_id' => $specialRequest->id,
                'request_payment_id' => $installment->id,
                'payment_id' => $response['id'] ?? null,
                'amount' => $totalAmount,
                'original_price' => $basePrice,
                'fees' => round($fees, 2),
                'status' => 'pending',
                'payment_method' => 'ziina',
                'currency' => 'AED',
                'system_id' => null,
            ];

            \App\Models\Payment::create($paymentData);
            $installment->update(['status' => 'pending']);

            return response()->json([
                'success' => true,
                'payment_url' => $response['redirect_url'],
                'total_amount' => $totalAmount,
                'fees' => round($fees, 2),
            ]);
        } catch (\Exception $e) {
            Log::error('Installment Payment Error: ' . $e->getMessage(), [
                'installment_id' => $paymentId ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع'
            ], 500);
        }
    }

    public function handleInstallmentReturn(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        $installmentId = $request->query('installment_id');

        if (!$paymentIntentId || !$installmentId) {
            return redirect()->route('dashboard')->with('error', 'بيانات الدفع غير كاملة');
        }

        try {
            $paymentIntent = $this->ziinaHandler->getPaymentIntent($paymentIntentId);
            $payment = Payment::where('payment_id', $paymentIntentId)->first();

            if ($payment && in_array($paymentIntent['status'], ['completed', 'paid'])) {
                $payment->update(['status' => 'completed']);

                $installment = \App\Models\RequestPayment::find($installmentId);
                if ($installment) {
                    $installment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    $specialRequest = $installment->specialRequest;
                    $specialRequest->refreshPaymentStatus();
                }

                return redirect()
                    ->route('special-request.details', $installment->special_request_id)
                    ->with('success', 'تم دفع الدفعة بنجاح!');
            }
        } catch (\Exception $e) {
            Log::error('Installment return error: ' . $e->getMessage());
        }

        return redirect()
            ->route('special-request.details', $installment->special_request_id)
            ->with('error', 'فشل في تأكيد الدفع');
    }

    public function createCoursePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        }

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        try {
            $course = Course::findOrFail($request->course_id);

            // التحقق من الاشتراك السابق
            if ($course->students()->where('user_id', auth()->id())->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت مشترك بالفعل في هذه الدورة'
                ], 400);
            }

            // التحقق من المقاعد المتاحة
            $current_enrolled = Payment::where('course_id', $course->id)
                ->where('status', '!=', 'failed')
                ->count();

            $actual_remaining = ($course->counter ?? 0) - $current_enrolled;

            if ($actual_remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، اكتمل العدد ولا توجد مقاعد شاغرة'
                ], 400);
            }

            $basePrice = (float) $course->price;

            // ✅ إذا كانت الدورة مجانية، اشترك مباشرة بدون دفع
            if ($basePrice == 0) {
                // إنشاء سجل دفع مجاني
                $payment = Payment::create([
                    'user_id'        => auth()->id(),
                    'course_id'      => $course->id,
                    'payment_id'     => 'FREE-' . time() . '-' . auth()->id(),
                    'amount'         => 0,
                    'original_price' => 0,
                    'fees'           => 0,
                    'status'         => 'completed',
                    'payment_method' => 'free',
                    'currency'       => 'AED',
                ]);

                // إضافة المستخدم للدورة
                $course->students()->attach(auth()->id(), [
                    'enrolled_at' => now()
                ]);

                Log::info('اشتراك مجاني في الدورة', [
                    'user_id' => auth()->id(),
                    'course_id' => $course->id,
                    'payment_id' => $payment->id
                ]);

                return response()->json([
                    'success' => true,
                    'is_free' => true,
                    'message' => 'تم اشتراكك في الدورة بنجاح! 🎉',
                    'redirect_url' => route('courses.show', $course->id)
                ]);
            }

            // ✅ إذا كانت الدورة مدفوعة، استمر بعملية الدفع
            $fees = ($basePrice * 0.079) + 2;
            $totalAmount = $basePrice + $fees;
            $successUrl = route('course.payment.success');
            $cancelUrl  = route('course.payment.cancel');

            // $isTest = config('services.ziina.test_mode', true);

            Log::info('إعداد دفع الدورة', [
                'course_id' => $course->id,
                'price' => $basePrice,
                'total' => $totalAmount,
                'success_url' => $successUrl
            ]);

            $response = $this->ziinaHandler->createSystemPaymentIntent(
                $course,
                $successUrl,
                $cancelUrl,
                // $isTest
            );

            Log::info('تم إنشاء payment intent بنجاح للدورة', [
                'course_id' => $course->id,
                'payment_id' => $response['id'] ?? null,
                'redirect_url' => $response['redirect_url'] ?? null
            ]);

            Payment::create([
                'user_id'        => auth()->id(),
                'course_id'      => $course->id,
                'payment_id'     => $response['id'] ?? null,
                'amount'         => $totalAmount,
                'original_price' => $basePrice,
                'fees'           => round($fees, 2),
                'status'         => 'pending',
                'payment_method' => 'ziina',
                'currency'       => 'AED',
            ]);

            return response()->json([
                'success'      => true,
                'is_free'      => false,
                'payment_url'  => $response['redirect_url'],
                'total_amount' => $totalAmount,
                'fees'         => round($fees, 2),
            ]);
        } catch (\Exception $e) {
            Log::error('فشل إنشاء دفع الدورة', [
                'error'     => $e->getMessage(),
                'course_id' => $request->course_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage()
            ], 500);
        }
    }

    public function courseSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');

        if (!$paymentIntentId) {
            \Log::error("payment_intent_id مفقود", ['url' => $request->fullUrl()]);
            return redirect()->route('system.index')->with('error', 'بيانات الدفع غير مكتملة');
        }

        try {
            $paymentIntent = $this->ziinaHandler->getPaymentIntent($paymentIntentId);
            $status = $paymentIntent['status'] ?? '';

            if (in_array($status, ['completed', 'paid', 'succeeded'])) {

                // ✅ استرجع الـ course_id من قاعدة البيانات مش من الـ URL
                $payment = \App\Models\Payment::where('payment_id', $paymentIntentId)->first();

                if (!$payment || !$payment->course_id) {
                    \Log::error("سجل الدفع غير موجود", ['payment_intent_id' => $paymentIntentId]);
                    return redirect()->route('system.index')->with('error', 'سجل الدفع غير موجود');
                }

                $courseId = $payment->course_id;
                $user = auth()->user();
                $course = \App\Models\Course::find($courseId);

                if ($course && $user) {
                    \App\Models\Payment::where('payment_id', $paymentIntentId)
                        ->update(['status' => 'completed']);

                    $user->update(['whatsapp_verified' => 1]);

                    if (!$course->students()->where('user_id', $user->id)->exists()) {
                        $course->students()->attach($user->id, [
                            'price_paid'  => $course->price,
                            'status'      => 'active',
                            'enrolled_at' => now(),
                        ]);

                        try {
                            $whatsapp = new \App\Services\WhatsAppOTPService();
                            $courseName = app()->getLocale() == 'ar' ? $course->name_ar : $course->name_en;
                            $whatsapp->sendCourseConfirmation(
                                $user->phone,
                                $user->name,
                                $courseName,
                                $course
                            );
                        } catch (\Exception $e) {
                            \Log::error("عطل في إرسال رسالة الواتساب: " . $e->getMessage());
                        }
                    }

                    return redirect()->route('courses.show', $courseId)
                        ->with('success', 'تم تفعيل الاشتراك بنجاح! سيصلك تأكيد عبر واتساب ✅');
                }
            }

            \Log::warning("حالة الدفع غير مكتملة", ['status' => $status]);
            return redirect()->route('system.index')->with('error', 'فشل التحقق من حالة الدفع');
        } catch (\Exception $e) {
            \Log::error('خطأ في courseSuccess: ' . $e->getMessage());
            return redirect()->route('system.index')->with('error', 'حدث خطأ فني');
        }
    }

    private function sendWhatsAppConfirmation($user, $course)
    {
        try {
            // تنظيف رقم الهاتف
            $phone = str_replace([' ', '+'], '', $user->phone);
            if (!str_starts_with($phone, '20')) {
                $phone = '20' . ltrim($phone, '0');
            }

            $courseName = app()->getLocale() == 'ar' ? $course->name_ar : $course->name_en;

            // إعداد الطلب لـ API 4Jawaly بناءً على الـ JSON المرسل من المدير
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Basic b0ZhZlVyaVZMQkVoTGtab3lkU1FMOXZzYktiUU02OEc1emVqQkJhYjoxd0l5Wjhkd2lTWER6d1ozc0Fhdmp5dUQwWHRvS1R6czNFMU10Wmd5OHlKa1R0Y0FmWFM1Q2JVQ2t2NEs3b3hBRzVvV2dEU3FDcG5ldDhGajJaMUVvWTNkem9pb0xUNFBmaW01',
                'Content-Type'  => 'application/json',
                'accept'        => 'application/json',
            ])->post('https://api-users.4jawaly.com/api/v1/whatsapp/669', [
                "path" => "message/template",
                "params" => [
                    "phone" => $phone,
                    "template" => "trabar",
                    "language" => ["policy" => "deterministic", "code" => "ar"],
                    "namespace" => "d62f7444_aa0b_40b8_8f46_0bb55ef2862e",
                    "params" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $user->name],   // BODY_1: اسم المشترك
                                ["type" => "text", "text" => $courseName]    // BODY_2: اسم الدورة
                            ]
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("فشل إرسال واتساب: " . $e->getMessage());
        }
    }

    public function courseCancel(Request $request)
    {
        $courseId = $request->query('course_id');
        Log::info('Course payment cancelled by user', ['course_id' => $courseId]);

        return redirect()->route('courses.show', $courseId ?? 1)
            ->with('error', 'تم إلغاء عملية الدفع');
    }
}

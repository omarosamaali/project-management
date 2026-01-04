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
            $isTest = config('services.ziina.test_mode', true);

            $response = $this->ziinaHandler->createSystemPaymentIntent(
                $system,
                $successUrl,
                $cancelUrl,
                $isTest
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
    // دوال الطلبات الخاصة - Special Requests
    // ============================================

    /**
     * إنشاء payment intent للطلب الخاص كاملاً
     */
    public function createSpecialRequestPayment(Request $request)
    {
        try {
            $request->validate([
                'special_request_id' => 'required|exists:special_requests,id',
            ]);

            $specialRequest = SpecialRequest::findOrFail($request->special_request_id);

            // التحقق من ملكية المشروع
            if ($specialRequest->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول لهذا المشروع'
                ], 403);
            }

            // التحقق من وجود سعر
            if (!$specialRequest->price || $specialRequest->price <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تحديد سعر للمشروع'
                ], 400);
            }

            // التحقق من عدم الدفع المسبق
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
            $isTest = config('services.ziina.test_mode', true);

            // استخدم نفس الدالة اللي شغالة للدفعات الجزئية، بس بنمرر الـ specialRequest كـ "installment" وهمي
            $response = $this->ziinaHandler->createInstallmentPaymentIntent(
                $specialRequest, // نمرر الـ specialRequest بدل الدفعة
                $successUrl,
                $cancelUrl,
                $isTest
            );

            // حفظ معلومات الدفع
            $paymentData = [
                'user_id' => Auth::id(),
                'special_request_id' => $specialRequest->id,
                'request_payment_id' => null, // عشان نعرف إنه دفع كامل
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

            // تحديث حالة المشروع
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

                    // تحديث حالة الطلب الخاص
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
            // جلب الدفعة (request_payment)
            $installment = \App\Models\RequestPayment::findOrFail($paymentId);

            // التحقق من أن الدفعة تابعة لطلب خاص
            if (!$installment->special_request_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الدفعة غير مرتبطة بطلب خاص'
                ], 400);
            }

            $specialRequest = $installment->specialRequest;

            // التحقق من ملكية المشروع
            if ($specialRequest->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بدفع هذه الدفعة'
                ], 403);
            }

            // التحقق من أن الدفعة غير مدفوعة
            if ($installment->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم دفع هذه الدفعة مسبقًا'
                ], 400);
            }

            $basePrice = (float) $installment->amount;
            $fees = ($basePrice * 0.079) + 2; // 7.9% + 2 AED
            $totalAmount = $basePrice + $fees;

            $successUrl = route('payment.installment.return', ['installment' => $installment->id]);
            $cancelUrl = route('dashboard.special-request.show', $specialRequest->id);
            // أو أي صفحة مناسبة
            $isTest = config('services.ziina.test_mode', true);

            // إنشاء payment intent مع Ziina
            $response = $this->ziinaHandler->createInstallmentPaymentIntent(
                $installment,
                $successUrl,
                $cancelUrl,
                $isTest
            );

            // حفظ معلومات الدفع في جدول payments (أو جدول منفصل لو عايز)
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
                'system_id' => null,  // ← أضف السطر ده
            ];

            \App\Models\Payment::create($paymentData);

            // تحديث حالة الدفعة إلى pending
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
        $installmentId = $request->query('installment_id'); // هتحتاج تمرره في metadata من Ziina

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

                    // تحديث إجمالي المدفوع في SpecialRequest (لو عندك logic جاهزة)
                    $specialRequest = $installment->specialRequest;
                    $specialRequest->refreshPaymentStatus(); // أو أي دالة عندك لحساب total_paid
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
}

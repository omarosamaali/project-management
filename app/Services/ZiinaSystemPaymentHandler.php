<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ZiinaSystemPaymentHandler
{
    private $apiKey;
    private $baseUrl;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.ziina.api_key');
        $this->baseUrl = config('services.ziina.base_url', 'https://api-v2.ziina.com/api');

        if (empty($this->apiKey)) {
            throw new Exception('Ziina API key is not configured');
        }
    }

    public function calculatePriceWithFees($basePrice)
    {
        $fees = ($basePrice * 0.079) + 2;
        return $basePrice + $fees;
    }

    /**
     * إنشاء payment intent للدفعة الجزئية
     */
    public function createInstallmentPaymentIntent($model, $successUrl, $cancelUrl, $isTest = true)
    {
        // $model ممكن يكون RequestPayment أو SpecialRequest
        $isFullPayment = $model instanceof \App\Models\SpecialRequest;

        $baseAmount = $isFullPayment ? $model->price : $model->amount;
        $description = $isFullPayment
            ? 'دفع طلب خاص كامل (طلب #' . $model->id . ')'
            : 'دفع دفعة جزئية - ' . ($model->payment_name ?? 'دفعة') . ' (طلب #' . $model->special_request_id . ')';

        $fees = ($baseAmount * 0.079) + 2;
        $totalAmount = $baseAmount + $fees;
        $amountInFils = round($totalAmount * 100);

        $payload = [
            'amount' => $amountInFils,
            'currency_code' => 'AED',
            'description' => $description,
            'customer_details' => [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'success_url' => $successUrl,
            'failure_url' => $cancelUrl,
            'metadata' => $isFullPayment ? [
                'special_request_id' => $model->id,
                'user_id' => auth()->id(),
                'type' => 'full_payment',
            ] : [
                'installment_id' => $model->id,
                'special_request_id' => $model->special_request_id,
                'user_id' => auth()->id(),
            ],
        ];

        $response = $this->makeApiCall('/payment_intent', 'POST', $payload);

        if (!isset($response['id'])) {
            throw new Exception('فشل إنشاء payment intent: ' . json_encode($response));
        }

        return $response;
    }


    public function verifyPayment($paymentIntentId)
    {
        $paymentIntent = $this->getPaymentIntent($paymentIntentId);
        return in_array($paymentIntent['status'] ?? '', ['completed', 'paid', 'succeeded']);
    }

    public function createSystemPaymentIntent($system, $successUrl, $cancelUrl, $isTest = true)
    {
        try {
            if (!$system) {
                throw new Exception('System data is missing');
            }

            $basePrice = $system->price ?? 0;
            $fees = ($basePrice * 0.079) + 2;
            $totalPrice = $basePrice + $fees;

            if ($totalPrice < 2) {
                throw new Exception("System price ($totalPrice AED) is below minimum (2 AED)");
            }

            $amountInFils = (int)($totalPrice * 100);

            $title = $system->name ?? 'نظام';
            $title = mb_substr($title, 0, 80);

            $message = "شراء {$title}";

            if (mb_strlen($message) < 10) {
                $message .= " - طلب شراء";
            }

            $data = [
                'amount' => $amountInFils,
                'currency_code' => 'AED',
                'message' => $message,
                'success_url' => $successUrl . '?payment_intent_id={PAYMENT_INTENT_ID}&system_id=' . $system->id,
                'cancel_url' => $cancelUrl . '?system_id=' . $system->id,
                'metadata' => [
                    'system_id' => (string)$system->id,
                    'system_name' => $system->name ?? '',
                    'customer_id' => (string)(auth()->id() ?? 'guest'),
                    'base_price' => $basePrice,
                    'fees' => round($fees, 2),
                    'total_price' => $totalPrice,
                    'environment' => app()->environment()
                ]
            ];

            // if ($isTest || app()->environment('local', 'testing')) {
            //     $data['test'] = true;
            // }

            Log::info('Creating Ziina payment intent for system', [
                'system_id' => $system->id,
                'amount' => $amountInFils,
                'base_price' => $basePrice,
                'fees' => $fees,
                'total_price' => $totalPrice,
                'message' => $message,
                // 'test_mode' => $isTest
            ]);

            $response = $this->makeApiCall('/payment_intent', 'POST', $data);

            if (!isset($response['redirect_url'])) {
                throw new Exception('Payment intent created but no redirect URL received');
            }

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create Ziina payment intent for system', [
                'system_id' => $system->id ?? null,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ إنشاء payment intent للكورسات - دالة منفصلة
     */
    public function createCoursePaymentIntent($course, $successUrl, $cancelUrl, $isTest = true)
    {
        try {
            if (!$course) {
                throw new Exception('Course data is missing');
            }

            $basePrice = $course->price ?? 0;
            $fees = ($basePrice * 0.079) + 2;
            $totalPrice = $basePrice + $fees;

            if ($totalPrice < 2) {
                throw new Exception("Course price ($totalPrice AED) is below minimum (2 AED)");
            }

            $amountInFils = (int)($totalPrice * 100);

            // استخدام اسم الكورس
            $courseName = $course->name_ar ?? $course->name_en ?? 'دورة تدريبية';
            $courseName = mb_substr($courseName, 0, 80);

            $message = "اشتراك في الدورة: {$courseName}";

            if (mb_strlen($message) < 10) {
                $message .= " - تسجيل في دورة";
            }

            $data = [
                'amount' => $amountInFils,
                'currency_code' => 'AED',
                'message' => $message,
                'success_url' => $successUrl, // ✅ الـ URL بالفعل يحتوي على placeholder
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'course_id' => (string)$course->id,
                    'course_name' => $courseName,
                    'customer_id' => (string)(auth()->id() ?? 'guest'),
                    'base_price' => $basePrice,
                    'fees' => round($fees, 2),
                    'total_price' => $totalPrice,
                    'type' => 'course_enrollment',
                    'environment' => app()->environment()
                ]
            ];

            // if ($isTest || app()->environment('local', 'testing')) {
            //     $data['test'] = true;
            // }

            Log::info('Creating Ziina payment intent for course', [
                'course_id' => $course->id,
                'amount' => $amountInFils,
                'base_price' => $basePrice,
                'fees' => $fees,
                'total_price' => $totalPrice,
                'message' => $message,
                'success_url' => $successUrl,
                // 'test_mode' => $isTest
            ]);

            $response = $this->makeApiCall('/payment_intent', 'POST', $data);

            if (!isset($response['redirect_url'])) {
                throw new Exception('Payment intent created but no redirect URL received');
            }

            Log::info('✅ Course payment intent created successfully', [
                'payment_intent_id' => $response['id'] ?? null,
                'redirect_url' => $response['redirect_url'] ?? null
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create Ziina payment intent for course', [
                'course_id' => $course->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * إنشاء payment intent لطلب خاص (Special Request)
     */
    public function createSpecialRequestPaymentIntent($specialRequest, $successUrl, $cancelUrl, $isTest = true)
    {
        try {
            if (!$specialRequest) {
                throw new Exception('Special request data is missing');
            }

            $basePrice = $specialRequest->price ?? 0;
            $fees = ($basePrice * 0.079) + 2;
            $totalPrice = $basePrice + $fees;

            if ($totalPrice < 2) {
                throw new Exception("Special request price ($totalPrice AED) is below minimum (2 AED)");
            }

            $amountInFils = (int)($totalPrice * 100);

            $title = $specialRequest->title ?? 'طلب خاص';
            $title = mb_substr($title, 0, 80);

            $message = "دفع مشروع: {$title}";

            if (mb_strlen($message) < 10) {
                $message .= " - طلب خاص";
            }

            $data = [
                'amount' => $amountInFils,
                'currency_code' => 'AED',
                'message' => $message,
                'success_url' => $successUrl . '?payment_intent_id={PAYMENT_INTENT_ID}&special_request_id=' . $specialRequest->id,
                'cancel_url' => $cancelUrl . '?special_request_id=' . $specialRequest->id,
                'metadata' => [
                    'special_request_id' => (string)$specialRequest->id,
                    'project_title' => $specialRequest->title ?? '',
                    'project_type' => $specialRequest->project_type ?? '',
                    'customer_id' => (string)(auth()->id() ?? 'guest'),
                    'base_price' => $basePrice,
                    'fees' => round($fees, 2),
                    'total_price' => $totalPrice,
                    'type' => 'special_request',
                    'environment' => app()->environment()
                ]
            ];

            // if ($isTest || app()->environment('local', 'testing')) {
            //     $data['test'] = true;
            // }

            Log::info('Creating Ziina payment intent for special request', [
                'special_request_id' => $specialRequest->id,
                'amount' => $amountInFils,
                'base_price' => $basePrice,
                'fees' => $fees,
                'total_price' => $totalPrice,
                'message' => $message,
                // 'test_mode' => $isTest
            ]);

            $response = $this->makeApiCall('/payment_intent', 'POST', $data);

            if (!isset($response['redirect_url'])) {
                throw new Exception('Payment intent created but no redirect URL received');
            }

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create Ziina payment intent for special request', [
                'special_request_id' => $specialRequest->id ?? null,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPaymentIntent($paymentIntentId)
    {
        try {
            if (empty($paymentIntentId)) {
                throw new Exception('Payment intent ID is required');
            }

            $response = $this->makeApiCall('/payment_intent/' . $paymentIntentId, 'GET');

            Log::info('Retrieved payment intent status', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $response['status'] ?? 'unknown'
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to get payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function makeApiCall($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Laravel-App/' . app()->version()
        ];

        Log::info('Making Ziina API call', [
            'method' => $method,
            'endpoint' => $endpoint,
            'url' => $url
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('cURL error in Ziina API call', [
                'error' => $curlError,
                'url' => $url
            ]);
            throw new Exception('Network error: ' . $curlError);
        }

        if (empty($response)) {
            Log::error('Empty response from Ziina API', [
                'http_code' => $httpCode,
                'url' => $url
            ]);
            throw new Exception('Empty response from payment service');
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON response from Ziina API', [
                'response' => substr($response, 0, 500),
                'json_error' => json_last_error_msg()
            ]);
            throw new Exception('Invalid response format from payment service: ' . json_last_error_msg());
        }

        if ($httpCode >= 400) {
            $errorMessage = $decodedResponse['message'] ?? 'Unknown API error';
            Log::error('Ziina API error', [
                'http_code' => $httpCode,
                'error_message' => $errorMessage,
                'full_response' => $decodedResponse
            ]);
            throw new Exception('Payment service error: ' . $errorMessage);
        }

        return $decodedResponse;
    }

    public function validateWebhook($payload, $signature, $secret = null)
    {
        $webhookSecret = $secret ?? config('services.ziina.webhook_secret');

        if (empty($webhookSecret)) {
            Log::info('Webhook validation skipped - no secret configured');
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }
}

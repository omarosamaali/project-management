<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOTPService
{
    private $appId = "oFafUriVLBEhLkZoydSQL9vsbKbQM68G5zejBBab";
    private $appSecret = "1wIyZ8dwiSXDzwZ3sAavjyuD0XtoKTzs3E1MtZgy8yJkTtcAfXS5CbUCkv4K7oxAG5oWgDSqCpnet8Fj2Z1EoY3dzoioLT4Pfim5"; 
    private $projectId = 669;
    private $baseUrl = "https://api-users.4jawaly.com/api/v1/whatsapp/";
    private $namespace = "d62f7444_aa0b_40b8_8f46_0bb55ef2862e";

    public function sendOTP($phoneNumber, $code, $isEnglish = false)
    {
        $template = $isEnglish ? 'general_notices_en' : 'general_notices_ar';
        $language = $isEnglish ? 'en' : 'ar';

        $params = [
            ["type" => "body", "parameters" => [["type" => "text", "text" => (string)$code]]],
            ["index" => "0", "sub_type" => "URL", "type" => "button", "parameters" => [["type" => "text", "text" => (string)$code]]]
        ];

        return $this->executeRequest($phoneNumber, $template, $language, $params);
    }

    public function sendCourseConfirmation($phone, $userName, $courseName, $course)
    {
        $imageUrl = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80';

        $params = [
            [
                "type" => "header",
                "parameters" => [
                    [
                        "type" => "image",
                        "image" => [
                            "link" => $imageUrl
                        ]
                    ]
                ]
            ],
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $userName],
                    ["type" => "text", "text" => (string) $courseName]
                ]
            ]
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }

    private function executeRequest($phone, $template, $lang, $params)
    {
        // تنظيف رقم الجوال
        $cleanPhone = preg_replace('/[^0-9]/', '', trim($phone));
        $cleanPhone = ltrim($cleanPhone, '0');

        if (!str_starts_with($cleanPhone, '20')) {
            $cleanPhone = '20' . $cleanPhone;
        }

        if (strlen($cleanPhone) !== 12) {
            Log::warning("رقم واتساب غير صالح", [
                'original' => $phone,
                'cleaned'  => $cleanPhone,
                'length'   => strlen($cleanPhone)
            ]);

            // محاولة تسجيل حتى لو الرقم غلط
            $this->logWhatsAppMessage(
                $cleanPhone,
                $template,
                $params,
                'invalid_phone',
                null,
                'رقم الهاتف غير صالح (طول غير 12 رقم)'
            );

            return false;
        }

        Log::info("[WHATSAPP] رقم منظف وجاهز للإرسال", [
            'phone' => $cleanPhone,
            'template' => $template,
            'user_id' => auth()->id() ?? 'غير مسجل'
        ]);

        // محاولة إنشاء سجل قبل الإرسال
        try {
            $messageRecord = $this->logWhatsAppMessage(
                $cleanPhone,
                $template,
                $params,
                'pending',
                null,
                $this->generateMessagePreview($params, $template)
            );

            Log::info("[WHATSAPP] تم إنشاء سجل الرسالة بنجاح", [
                'message_id' => $messageRecord->id,
                'phone' => $cleanPhone
            ]);
        } catch (\Exception $e) {
            Log::error("[WHATSAPP] فشل إنشاء سجل الرسالة قبل الإرسال", [
                'error' => $e->getMessage(),
                'phone' => $cleanPhone,
                'template' => $template
            ]);

            // لو فشل التخزين، نكمل الإرسال لكن من غير تسجيل
            $messageRecord = null;
        }

        $token = base64_encode("$this->appId:$this->appSecret");

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Basic " . $token,
                    'Content-Type'  => 'application/json',
                    'accept'        => 'application/json',
                ])
                ->timeout(30) // ← أضف timeout عشان ما يعلقش
                ->post($this->baseUrl . $this->projectId, [
                    "path" => "message/template",
                    "params" => [
                        "phone"     => $cleanPhone,
                        "template"  => $template,
                        "language"  => ["policy" => "deterministic", "code" => $lang],
                        "namespace" => $this->namespace,
                        "params"    => $params
                    ]
                ]);

            $responseBody = $response->body();
            $responseData = json_decode($responseBody, true) ?? [];

            Log::info("[WHATSAPP] رد الـ API", [
                'status' => $response->status(),
                'body'   => $responseBody,
                'phone'  => $cleanPhone,
                'template' => $template
            ]);

            if ($response->successful() && isset($responseData['sent']) && $responseData['sent'] === true) {
                if ($messageRecord) {
                    $messageRecord->update([
                        'status'     => 'sent',
                        'message_id' => $responseData['id'] ?? null,
                    ]);
                    Log::info("[WHATSAPP] تم تحديث الرسالة إلى sent", ['id' => $messageRecord->id]);
                }

                return true;
            }

            // فشل الإرسال
            $errorMsg = $responseData['error'] ?? $responseBody ?? 'فشل غير معروف';

            if ($messageRecord) {
                $messageRecord->update([
                    'status'        => 'failed',
                    'error_message' => $errorMsg,
                ]);
                Log::error("[WHATSAPP] تم تحديث الرسالة إلى failed", [
                    'id' => $messageRecord->id,
                    'error' => $errorMsg
                ]);
            }

            return false;
        } catch (\Exception $e) {
            Log::error("[WHATSAPP] استثناء أثناء إرسال الرسالة", [
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'phone'    => $cleanPhone,
                'template' => $template
            ]);

            if ($messageRecord) {
                $messageRecord->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * تسجيل محاولة إرسال رسالة في قاعدة البيانات
     */
    private function logWhatsAppMessage($phone, $template, $params, $status, $messageId = null, $contentPreview = null)
    {
        return \App\Models\WhatsAppMessage::create([
            'user_id'         => auth()->id() ?? null,
            'phone'           => $phone,
            'template'        => $template,
            'type'            => 'outgoing',
            'message_content' => $contentPreview,
            'payload'         => $params,
            'message_id'      => $messageId,
            'status'          => $status,
            'sent_at'         => now(),
        ]);
    }

    /**
     * توليد معاينة نصية للرسالة لتخزينها
     */
    private function generateMessagePreview($params, $template)
    {
        if ($template === 'trabar') {
            $bodyParams = $params[1]['parameters'] ?? [];
            $name   = $bodyParams[0]['text'] ?? 'غير معروف';
            $course = $bodyParams[1]['text'] ?? 'غير معروف';
            return "تأكيد اشتراك: مرحبا {$name}، تم اشتراكك في دورة {$course}";
        }

        if ($template === 'general_notices_ar' || $template === 'general_notices_en') {
            $code = $params[0]['parameters'][0]['text'] ?? 'غير معروف';
            return "كود التحقق: {$code}";
        }

        return "رسالة قالب: {$template}";
    }
}

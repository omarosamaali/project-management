<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOTPService
{
    private $appId     = "oFafUriVLBEhLkZoydSQL9vsbKbQM68G5zejBBab";
    private $appSecret = "1wIyZ8dwiSXDzwZ3sAavjyuD0XtoKTzs3E1MtZgy8yJkTtcAfXS5CbUCkv4K7oxAG5oWgDSqCpnet8Fj2Z1EoY3dzoioLT4Pfim5";
    private $projectId = 669;
    private $baseUrl   = "https://api-users.4jawaly.com/api/v1/whatsapp/";
    private $namespace = "d62f7444_aa0b_40b8_8f46_0bb55ef2862e";

    // ── OTP ───────────────────────────────────────────
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

    // ── إشعار صرف الراتب ─────────────────────────────
    /**
     * يُرسل إشعار واتساب للموظف عند صرف راتبه
     * BODY_1 = اسم الموظف
     * BODY_2 = تفاصيل الراتب
     */
    public function sendSalaryNotification(string $phone, string $employeeName, float $totalDue, string $currency, string $month, string $year): bool
    {
        $imageUrl = 'https://evorq.online/assets/images/salaray.jpeg';
        $bodyText = "تم صرف راتبك لشهر {$month}/{$year} بمبلغ {$totalDue} {$currency}";

        $params = [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $employeeName],
                    ["type" => "text", "text" => $bodyText],
                ]
            ],
            [
                "type" => "header",
                "parameters" => [
                    ["type" => "image", "image" => ["link" => $imageUrl]]
                ]
            ]
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }

    // ── تأكيد الدورة ─────────────────────────────────
    public function sendCourseConfirmation($phone, $userName, $courseName, $course)
    {
        $imageUrl = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80';

        $params = [
            [
                "type" => "header",
                "parameters" => [["type" => "image", "image" => ["link" => $imageUrl]]]
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

    // ── إشعار تذكرة دعم فني للـ Partner ─────────────
    /**
     * يُرسل إشعار واتساب للـ partner عند فتح تذكرة دعم فني مرتبطة بمشروعه
     *
     * @param string $phone        رقم هاتف الـ partner
     * @param string $partnerName  اسم الـ partner
     * @param string $projectName  اسم المشروع / النظام
     * @param string $subject      موضوع التذكرة
     */
    /**
     * إشعار تذكرة دعم فني — template: trabar
     * BODY_1 = اسم الـ partner
     * BODY_2 = نص الإشعار
     * FILE_URL = صورة ثابتة في الـ header
     */
    public function sendTicketNotification(string $phone, string $partnerName, string $projectName, string $ticketId): bool
    {
        $imageUrl   = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80';
        $bodyText   = "لديك تذكرة دعم فني جديدة، المشروع: {$projectName}، رقم التذكرة: #{$ticketId}";

        $params = [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $partnerName],
                    ["type" => "text", "text" => $bodyText],
                ]
            ],
            [
                "type" => "header",
                "parameters" => [
                    [
                        "type"  => "image",
                        "image" => ["link" => $imageUrl]
                    ]
                ]
            ]
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }


    // ── executeRequest ────────────────────────────────
    private function executeRequest($phone, $template, $lang, $params)
    {
        // شيل كل حاجة غير أرقام (بما فيها + و - و مسافات)
        $cleanPhone = preg_replace('/[^0-9]/', '', trim($phone));
        // شيل الصفر الأول فقط لو مفيش كود دولة
        // أكواد الدول — نتحقق قبل ما نضيف 20
        $knownCodes = ['971', '966', '965', '968', '974', '973', '970', '962', '963', '961', '20'];
        $hasCode    = false;
        foreach ($knownCodes as $code) {
            if (str_starts_with($cleanPhone, $code)) {
                $hasCode = true;
                break;
            }
        }
        // لو مفيش كود دولة → مصري → شيل الصفر وضيف 20
        if (!$hasCode) {
            $cleanPhone = '20' . ltrim($cleanPhone, '0');
        }

        // طول صالح دولياً 10–15 رقم
        if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
            Log::warning("[WHATSAPP] رقم غير صالح", [
                'original' => $phone,
                'cleaned'  => $cleanPhone,
                'length'   => strlen($cleanPhone),
            ]);

            $this->logWhatsAppMessage(
                $cleanPhone,
                $template,
                $params,
                'invalid_phone',
                null,
                'رقم الهاتف غير صالح'
            );

            return false;
        }

        Log::info("[WHATSAPP] إرسال", [
            'phone'    => $cleanPhone,
            'template' => $template,
            'user_id'  => auth()->id() ?? 'غير مسجل'
        ]);

        try {
            $messageRecord = $this->logWhatsAppMessage(
                $cleanPhone,
                $template,
                $params,
                'pending',
                null,
                $this->generateMessagePreview($params, $template)
            );
        } catch (\Exception $e) {
            Log::error("[WHATSAPP] فشل إنشاء سجل الرسالة", ['error' => $e->getMessage()]);
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
                ->timeout(30)
                ->post($this->baseUrl . $this->projectId, [
                    "path"   => "message/template",
                    "params" => [
                        "phone"     => $cleanPhone,
                        "template"  => $template,
                        "language"  => ["policy" => "deterministic", "code" => $lang],
                        "namespace" => $this->namespace,
                        "params"    => $params
                    ]
                ]);

            $responseData = json_decode($response->body(), true) ?? [];

            Log::info("[WHATSAPP] رد API", [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'phone'    => $cleanPhone,
                'template' => $template
            ]);

            if ($response->successful() && ($responseData['sent'] ?? false) === true) {
                $messageRecord?->update([
                    'status'     => 'sent',
                    'message_id' => $responseData['id'] ?? null,
                ]);
                return true;
            }

            $errorMsg = $responseData['error'] ?? $response->body() ?? 'فشل غير معروف';
            $messageRecord?->update(['status' => 'failed', 'error_message' => $errorMsg]);

            return false;
        } catch (\Exception $e) {
            Log::error("[WHATSAPP] استثناء", [
                'message'  => $e->getMessage(),
                'phone'    => $cleanPhone,
                'template' => $template
            ]);
            $messageRecord?->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return false;
        }
    }

    // ── Helpers ───────────────────────────────────────
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

    private function generateMessagePreview($params, $template): string
    {
        if ($template === 'trabar') {
            $bodyParams = $params[0]['parameters'] ?? [];
            $name = $bodyParams[0]['text'] ?? 'غير معروف';
            $text = $bodyParams[1]['text'] ?? 'غير معروف';
            return "trabar: {$name} — {$text}";
        }

        if (in_array($template, ['general_notices_ar', 'general_notices_en'])) {
            $text = $params[0]['parameters'][0]['text'] ?? 'غير معروف';
            // لو الـ parameter رقم → تذكرة دعم فني
            if (is_numeric($text)) {
                return "إشعار تذكرة دعم فني رقم: {$text}";
            }
            return $text;
        }

        return "رسالة قالب: {$template}";
    }
}

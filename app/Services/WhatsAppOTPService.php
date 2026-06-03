<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WhatsAppOTPService
{
    const MANAGER_PHONE = '971501774477';
    const ADMIN_PHONE   = '201016934863';
    const MANAGER_EMAIL = 'info@evorq.com';
    const ADMIN_EMAIL   = 'admin@evorq.com';

    private $appId     = "oFafUriVLBEhLkZoydSQL9vsbKbQM68G5zejBBab";
    private $appSecret = "1wIyZ8dwiSXDzwZ3sAavjyuD0XtoKTzs3E1MtZgy8yJkTtcAfXS5CbUCkv4K7oxAG5oWgDSqCpnet8Fj2Z1EoY3dzoioLT4Pfim5";
    private $projectId = 669;
    private $baseUrl   = "https://api-users.4jawaly.com/api/v1/whatsapp/";
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


    // ── إشعار الأدمن بتسجيل شريك جديد ──────────────────
    public function sendNewPartnerNotification(string $adminPhone, string $partnerName, string $partnerEmail, string $partnerPhone): bool
    {
        $bodyText = "تم تسجيل شريك مستقل جديد:\nالاسم: {$partnerName}\nالإيميل: {$partnerEmail}\nالهاتف: {$partnerPhone}\nبانتظار المراجعة والموافقة.";

        $params = [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => \App\Support\SystemManager::displayName()],
                    ["type" => "text", "text" => $bodyText],
                ]
            ],
            [
                "type" => "header",
                "parameters" => [
                    ["type" => "image", "image" => ["link" => 'https://evorq.online/assets/images/salaray.jpeg']]
                ]
            ]
        ];

        return $this->executeRequest($adminPhone, 'trabar', 'ar', $params);
    }

    // ── إشعار عام للمشروع ────────────────────────────────
    public function sendProjectNotification(string $phone, string $memberName, string $eventText, string $projectTitle, ?string $email = null): bool
    {
        $bodyText = $this->sanitizeTrabarText("{$eventText} في المشروع: {$projectTitle}");

        $result = $this->sendTrabar($phone, $memberName, $bodyText);

        if ($email) {
            $this->sendEmailNotification($email, $memberName, "إشعار مشروع: {$projectTitle}", $bodyText);
        }

        return $result;
    }

    // ── إرسال إشعار عبر الإيميل ──────────────────────────
    public function sendEmailNotification(string $email, string $name, string $subject, string $body): bool
    {
        try {
            Mail::raw(
                "السلام عليكم {$name},\n\n{$body}\n\n---\nفريق Evorq Technologies",
                function ($message) use ($email, $name, $subject) {
                    $message->to($email, $name)
                            ->subject($subject)
                            ->from(config('mail.from.address', 'noreply@evorq.com'), config('mail.from.name', 'Evorq'));
                }
            );

            return true;
        } catch (\Exception $e) {
            Log::error("[EMAIL] فشل إرسال الإيميل", [
                'to'    => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ── إشعار مهمة جديدة في المشروع ─────────────────────
    public function sendNewTaskNotification(string $phone, string $memberName, string $taskTitle, string $projectTitle): bool
    {
        $bodyText = "تم إضافة مهمة جديدة: ({$taskTitle}) في المشروع: {$projectTitle}";

        $params = [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $memberName],
                    ["type" => "text", "text" => $bodyText],
                ]
            ],
            [
                "type" => "header",
                "parameters" => [
                    ["type" => "image", "image" => ["link" => 'https://evorq.online/assets/images/salaray.jpeg']]
                ]
            ]
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }

    // ── إشعار مرحلة جديدة في المشروع ────────────────────
    public function sendNewStageNotification(string $phone, string $memberName, string $stageName, string $projectTitle): bool
    {
        $bodyText = "تم إضافة مرحلة عمل جديدة: ({$stageName}) في المشروع: {$projectTitle}";

        $params = [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $memberName],
                    ["type" => "text", "text" => $bodyText],
                ]
            ],
            [
                "type" => "header",
                "parameters" => [
                    ["type" => "image", "image" => ["link" => 'https://evorq.online/assets/images/salaray.jpeg']]
                ]
            ]
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }

    /**
     * إشعار الموظف عند تسجيل خصم أو مكافأة — template: trabar
     */
    public function sendAdjustmentNotification(
        string $phone,
        string $employeeName,
        string $typeLabel,
        float $amount,
        string $currency,
        string $date,
        ?string $notes = null,
        ?string $email = null,
        ?string $emailSubject = null,
        bool $isUpdate = false,
    ): bool {
        $amountFormatted = number_format($amount, 2);
        $actionWord = $isUpdate ? 'تم تعديل' : 'تم تسجيل';
        $bodyText = "{$actionWord} {$typeLabel} بمبلغ {$amountFormatted} {$currency} بتاريخ {$date}.";
        if ($notes && trim($notes) !== '') {
            $bodyText .= " ملاحظات: " . trim($notes);
        }

        $result = $this->sendTrabar($phone, $employeeName, $bodyText);

        if ($email) {
            $this->sendEmailNotification(
                $email,
                $employeeName,
                $emailSubject ?? "إشعار {$typeLabel}",
                $bodyText,
            );
        }

        return $result;
    }

    /**
     * إشعار فوري بالخصم/المكافأة للموظف والإدارة (واتساب + بريد مباشرة).
     *
     * @return array{employee_whatsapp: bool, employee_email: bool, manager: bool}
     */
    public function notifyAdjustmentImmediate(
        User $user,
        string $typeLabel,
        float $amount,
        string $currency,
        string $date,
        ?string $notes,
        bool $isUpdate = false,
    ): array {
        $actionWord = $isUpdate ? 'تم تعديل' : 'تم تسجيل';
        $employeeTitle = $isUpdate ? "تعديل {$typeLabel}" : "إشعار {$typeLabel}";
        $employeeBody = "{$actionWord} {$typeLabel} بمبلغ " . number_format($amount, 2) . " {$currency} بتاريخ {$date}.";
        if ($notes && trim($notes) !== '') {
            $employeeBody .= "\nملاحظات: " . trim($notes);
        }

        $adminBody = "{$actionWord} {$typeLabel} للموظف {$user->name} بمبلغ "
            . number_format($amount, 2) . " {$currency} بتاريخ {$date}.";
        if ($notes && trim($notes) !== '') {
            $adminBody .= " ملاحظات: " . trim($notes);
        }

        $results = [
            'employee_whatsapp' => false,
            'employee_email'    => false,
            'manager'           => false,
        ];

        if ($user->phone) {
            $results['employee_whatsapp'] = $this->sendAdjustmentNotification(
                phone: $user->phone,
                employeeName: $user->name,
                typeLabel: $typeLabel,
                amount: $amount,
                currency: $currency,
                date: $date,
                notes: $notes,
                email: null,
                emailSubject: $employeeTitle,
                isUpdate: $isUpdate,
            );
        }

        if ($user->email) {
            $results['employee_email'] = $this->sendEmailNotification(
                $user->email,
                $user->name,
                $employeeTitle,
                $employeeBody,
            );
        }

        $results['manager'] = $this->notifyManager($adminBody, 'الخصومات والمكافآت');

        Log::info('[ADJUSTMENT] إرسال فوري', [
            'user_id' => $user->id,
            'results' => $results,
        ]);

        return $results;
    }

    /**
     * إشعار الموظف عند تسجيل عطلة — template: trabar
     */
    public function sendHolidayNotification(
        string $phone,
        string $employeeName,
        string $holidayName,
        string $typeLabel,
        string $dateRange,
        string $salaryNote,
        ?string $details = null
    ): bool {
        $bodyText = "عطلة: {$holidayName} ({$typeLabel}) للفترة {$dateRange}. {$salaryNote}";
        if ($details && trim($details) !== '') {
            $bodyText .= ' تفاصيل: ' . trim($details);
        }

        $params = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $employeeName],
                    ['type' => 'text', 'text' => $bodyText],
                ],
            ],
            [
                'type' => 'header',
                'parameters' => [
                    ['type' => 'image', 'image' => ['link' => 'https://evorq.online/assets/images/salaray.jpeg']],
                ],
            ],
        ];

        return $this->executeRequest($phone, 'trabar', 'ar', $params);
    }

    // ── إشعار المدير والأدمن بأي حدث في المشروع ────────
    public function notifyManager(string $eventText, string $projectTitle): bool
    {
        $r1 = $this->sendProjectNotification(self::MANAGER_PHONE, 'المدير', $eventText, $projectTitle, self::MANAGER_EMAIL);
        $r2 = $this->sendProjectNotification(self::ADMIN_PHONE,   'الأدمن', $eventText, $projectTitle, self::ADMIN_EMAIL);
        return $r1 || $r2;
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
    private function sanitizeTrabarText(string $text, int $maxLength = 900): string
    {
        $text = str_replace(['«', '»', '"', '“', '”'], '', $text);
        $text = preg_replace('/[\r\n]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);

        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength - 3) . '...';
        }

        return $text;
    }

    /**
     * إرسال قالب trabar — body فقط أولاً (أكثر نجاحاً في التسليم)، ثم مع صورة header.
     */
    private function sendTrabar(string $phone, string $recipientName, string $bodyText): bool
    {
        $recipientName = $this->sanitizeTrabarText($recipientName, 120);
        $bodyText = $this->sanitizeTrabarText($bodyText);

        $bodyComponent = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $recipientName],
                ['type' => 'text', 'text' => $bodyText],
            ],
        ];

        $sent = $this->executeRequest($phone, 'trabar', 'ar', [$bodyComponent]);
        if ($sent) {
            return true;
        }

        return $this->executeRequest($phone, 'trabar', 'ar', [
            [
                'type' => 'header',
                'parameters' => [
                    ['type' => 'image', 'image' => ['link' => 'https://evorq.online/assets/images/salaray.jpeg']],
                ],
            ],
            $bodyComponent,
        ]);
    }

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
            $bodyParams = [];
            foreach ($params as $component) {
                if (($component['type'] ?? null) === 'body') {
                    $bodyParams = $component['parameters'] ?? [];
                    break;
                }
            }
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

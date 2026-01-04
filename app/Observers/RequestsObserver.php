<?php

namespace App\Observers;

use App\Models\Requests; // تأكد من استبدال هذا بالاسم الصحيح لموديل الطلب لديك
use App\Models\System;
use App\Models\TechnicalSupport;

class RequestsObserver
{
    /**
     * Handle the RequestModel "updated" event.
     */
    public function updated(Requests $request): void
    {
        // 1. التحقق من تحول الحالة إلى 'منتهية' فقط
        // يجب أن نستخدم isDirty للتأكد من أن status هو ما تم تغييره
        if ($request->isDirty('status') && $request->status === 'منتهية') {

            // 2. جلب معلومات النظام المرتبط
            // افترض أنك وضعت علاقة System في موديل RequestModel
            $system = $request->system;

            // 3. التحقق من أيام الدعم الفني (support_days)
            // إذا كانت support_days تحتوي على قيمة (رقم) وليست NULL
            if ($system && is_numeric($system->support_days) && $system->support_days > 0) {

                // 4. بناء موضوع ووصف التذكرة
                $subject = 'مشكلة محتملة بعد إنهاء الطلب رقم: ' . $request->order_number;
                $description = "نظام الدعم الفني يفتح تذكرة تلقائية بسبب إنهاء الطلب. يرجى من العميل إدخال تفاصيل الشكوى (مثل مشكلة في الصور).";

                // 5. إنشاء تذكرة دعم فني جديدة
                TechnicalSupport::create([
                    'request_id'  => $request->id,
                    'system_id'   => $request->system_id,
                    'client_id'   => $request->client_id, // افترض أن الطلب مرتبط بالعميل
                    'subject'     => $subject,
                    'description' => $description,
                    'status'      => 'open', // الحالة الافتراضية
                ]);
            }
        }
    }
    // ... باقي التوابع (created, deleted, etc.)
}
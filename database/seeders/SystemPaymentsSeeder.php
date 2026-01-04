<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\System;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class SystemPaymentsSeeder extends Seeder
{
    public function run()
    {
        // 1. جلب الأنظمة الظاهرة في شاشتك
        $allSystems = System::all();

        // 2. الحصول على أول مستخدم أو إنشاء واحد للتجربة
        $user = User::first() ?? User::factory()->create();

        if ($allSystems->isEmpty()) {
            $this->command->error("جدول الأنظمة فارغ، أضف أنظمة أولاً.");
            return;
        }

        foreach ($allSystems as $system) {
            // إنشاء 5 عمليات شراء لكل نظام لتجربة الأرقام
            for ($i = 0; $i < 5; $i++) {
                Payment::create([
                    'user_id'         => $user->id,
                    'system_id'       => $system->id,
                    'amount'          => $system->price,
                    'original_price'  => $system->price, // الحقل الذي تسبب في الخطأ
                    'fees'            => 0,              // إضافة رسوم صفرية لتجنب خطأ مشابه
                    'status'          => 'success',
                    'currency'        => 'EGP',
                    'payment_id'      => 'PAY-' . rand(100000, 999999),
                    'payment_method'  => 'manual',
                    'created_at'      => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info("تم ربط المبيعات بالأنظمة بنجاح! حدث الصفحة الآن.");
    }
}

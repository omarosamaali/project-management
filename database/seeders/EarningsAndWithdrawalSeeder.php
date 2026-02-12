<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Earning;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Seeder;

class EarningsAndWithdrawalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. تحديد المستخدمين الضروريين
        $adminUser = User::where('role', 'admin')->first();
        $partners = User::where('role', 'partner')->get();

        if (!$adminUser || $partners->isEmpty()) {
            // قد يحدث هذا إذا لم يتم تشغيل UserSeeder أولاً
            echo "Admin or Partner users not found. Skipping Earnings and Withdrawal Seeding.\n";
            return;
        }

        // 2. إنشاء أرباح (Earnings) لكل شريك
        foreach ($partners as $partner) {
            // إنشاء أرباح متاحة (Available)
            Earning::factory(rand(10, 20))->create([
                'user_id' => $partner->id,
                'status' => 'available',
                'amount' => $this->faker()->randomFloat(2, 50, 400),
            ]);

            // إنشاء أرباح مسحوبة (Withdrawn) للماضي
            Earning::factory(rand(5, 10))->create([
                'user_id' => $partner->id,
                'status' => 'withdrawn',
                'amount' => $this->faker()->randomFloat(2, 20, 150),
            ]);
        }

        // 3. إنشاء طلبات سحب (Withdrawal Requests)
        foreach ($partners as $partner) {
            $availableBalance = $partner->available_balance;

            // أ. طلبات سحب مكتملة (Completed) في الماضي
            if (rand(0, 1) === 1) {
                // المبلغ المسحوب يجب أن يكون أقل من (أو يساوي) الرصيد المتاح وقت الطلب
                $amountToWithdraw = $this->faker()->randomFloat(2, 200, 1000);

                if ($amountToWithdraw > 150) { // نفترض الحد الأدنى للسحب
                    $completedRequest = WithdrawalRequest::factory()->completed()->create([
                        'user_id' => $partner->id,
                        'amount' => $amountToWithdraw,
                        'available_balance' => $amountToWithdraw + $this->faker()->randomFloat(2, 50, 200), // رصيد كان متاح وقت الطلب
                        'processed_by' => $adminUser->id,
                    ]);

                    // ربط بعض الأرباح المسحوبة بهذا الطلب
                    $partner->earnings()->where('status', 'withdrawn')->inRandomOrder()->take(rand(1, 3))->update([
                        'withdrawal_request_id' => $completedRequest->id,
                    ]);
                }
            }

            // ب. طلب سحب حالي جديد (Pending)
            $currentAvailableBalance = $partner->fresh()->available_balance; // احصل على الرصيد بعد عمليات السحب الماضية
            if ($currentAvailableBalance >= 250 && rand(0, 1) === 1) {
                $pendingAmount = $this->faker()->randomFloat(2, 200, $currentAvailableBalance * 0.7);

                $pendingRequest = WithdrawalRequest::factory()->pending()->create([
                    'user_id' => $partner->id,
                    'amount' => $pendingAmount,
                    'available_balance' => $currentAvailableBalance,
                    'notes' => 'برجاء معالجة الطلب في أقرب وقت.',
                ]);

                // تحديث حالة الأرباح المرتبطة لتصبح 'pending_withdrawal'
                $earningsToLock = $partner->earnings()
                    ->available()
                    ->inRandomOrder()
                    ->take(rand(1, 4))
                    ->get();

                $earningsToLock->each(function ($earning) use ($pendingRequest) {
                    $earning->update([
                        'status' => 'pending_withdrawal',
                        'withdrawal_request_id' => $pendingRequest->id,
                    ]);
                });
            }

            // ج. طلب سحب مرفوض (Rejected)
            if (rand(0, 3) === 0) {
                WithdrawalRequest::factory()->create([
                    'user_id' => $partner->id,
                    'status' => 'rejected',
                    'completed_at' => now(),
                    'processed_by' => $adminUser->id,
                    'amount' => $this->faker()->randomFloat(2, 100, 500),
                    'available_balance' => $this->faker()->randomFloat(2, 600, 1500),
                    'admin_notes' => 'المبلغ أقل من الحد الأدنى للسحب أو تفاصيل الدفع غير كاملة.',
                ]);
            }
        }
    }

    /**
     * لاستخدام Faker داخل الـ Seeder
     */
    protected function faker()
    {
        return \Faker\Factory::create('ar_SA');
    }
}

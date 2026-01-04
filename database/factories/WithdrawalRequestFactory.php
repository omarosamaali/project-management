<?php

namespace Database\Factories;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalRequestFactory extends Factory
{
    protected $model = WithdrawalRequest::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'completed', 'rejected']);
        $completedAt = ($status === 'completed') ? $this->faker->dateTimeBetween('-6 months', 'now') : null;

        // المبلغ المتاح سيكون عشوائياً، وسيتم تحديثه في الـ Seeder ليكون واقعياً
        $amount = $this->faker->randomFloat(2, 200, 3000);

        return [
            'user_id' => User::factory(), // سيتم استبداله في الـ Seeder
            'amount' => $amount,
            'available_balance' => $amount + $this->faker->randomFloat(2, 50, 500),
            'status' => $status,
            'completed_at' => $completedAt,
            'notes' => $this->faker->optional(0.6)->sentence(),
            'admin_notes' => ($status === 'rejected') ? 'تم الرفض لعدم توافر شروط السحب' : null,
            'processed_by' => null, // سيتم تحديده في الـ Seeder
        ];
    }

    public function pending(): Factory
    {
        return $this->state(fn(array $attributes) => ['status' => 'pending', 'completed_at' => null, 'processed_by' => null]);
    }

    public function completed(): Factory
    {
        return $this->state(fn(array $attributes) => ['status' => 'completed', 'completed_at' => now(), 'admin_notes' => 'تم التحويل بنجاح']);
    }
}

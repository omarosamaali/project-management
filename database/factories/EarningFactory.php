<?php

namespace Database\Factories;

use App\Models\Earning;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EarningFactory extends Factory
{
    protected $model = Earning::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 5, 300),
            'source' => $this->faker->randomElement(['Sale-A', 'Sale-B', 'Bonus']),
            'status' => 'available',
            'description' => $this->faker->sentence(3),
            'withdrawal_request_id' => null,
        ];
    }
}

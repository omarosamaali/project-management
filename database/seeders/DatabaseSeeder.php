<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'phone' => '+971 50 177 4477',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);


        $this->call([
            SystemSeeder::class,
            UserSeeder::class,
            PerformanceSeeder::class,
            EarningsAndWithdrawalSeeder::class,
            AdminPartnerSeeder::class,
            SpecialRequestsSeeder::class,
            ServiceSeeder::class,
            PartnerAssignmentSeeder::class,
            SystemPaymentsSeeder::class,
            RequestsTableSeeder::class,
            WorkTimeSeeder::class
        ]);
    }
}

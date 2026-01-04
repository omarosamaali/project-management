<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'عمر أسامة',
                'email' => 'omar@gmail.com',
                'phone' => '01012345678',
                'percentage' => 50,
                'orders' => 10,
                'role' => 'partner',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'عبدالله منصور',
                'email' => 'abdullah@gmail.com',
                'phone' => '01098765432',
                'percentage' => 35,
                'orders' => 15,
                'role' => 'partner',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'مؤمن حامد',
                'email' => 'moamen@gmail.com',
                'phone' => '01123456789',
                'percentage' => 40,
                'orders' => 8,
                'role' => 'partner',
                'password' => Hash::make('password'),
            ],
        ];

        $clients = [
            [
                'name' => 'أبي بكر',
                'email' => 'abobakr@gmail.com',
                'phone' => '9901900',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'سيد مصطفي',
                'email' => 'sayed@gmail.com',
                'phone' => '01098765432',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'طلحة حامد',
                'email' => 'talha@gmail.com',
                'phone' => '01123456789',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($partners as $partner) {
            User::create($partner);
        }

        foreach ($clients as $client) {
            User::create($client);
        }
    }
}

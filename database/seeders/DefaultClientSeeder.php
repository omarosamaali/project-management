<?php
// database/seeders/DefaultClientSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultClientSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'contact@evorq.com'], // البحث بالإيميل
            [
                'name' => 'ايفورك للتكنولوجيا',
                'email' => 'contact@evorq.com',
                'phone' => '+971 50 177 4477',
                'password' => Hash::make('password'), // كلمة مرور افتراضية
                'is_default' => true, // علامة للعميل الافتراضي
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

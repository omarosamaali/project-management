<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\System;

class AdminPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systems = System::all();

        foreach ($systems as $system) {
            $exists = DB::table('partner_system')
                ->where('partner_id', 1)
                ->where('system_id', $system->id)
                ->exists();

            if (!$exists) {
                DB::table('partner_system')->insert([
                    'partner_id' => 1,
                    'system_id' => $system->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('تم إضافة الأدمن كشريك في جميع الأنظمة بنجاح!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\System;

class PartnerAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $partnerId = 2; // المعرف الخاص بالشريك

        // 1. ربط الشريك بكل الخدمات في جدول partner_service
        $services = DB::table('services')->pluck('id');

        foreach ($services as $serviceId) {
            DB::table('partner_service')->updateOrInsert(
                ['partner_id' => $partnerId, 'service_id' => $serviceId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2. ربط الشريك بكل الأنظمة في جدول partner_system
        $systems = DB::table('systems')->pluck('id');

        foreach ($systems as $systemId) {
            DB::table('partner_system')->updateOrInsert(
                ['partner_id' => $partnerId, 'system_id' => $systemId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

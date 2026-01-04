<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requests; // تأكد من اسم الموديل الخاص بك
use Illuminate\Support\Str;

class RequestsTableSeeder extends Seeder
{
    public function run(): void
    {
        $requests = [
            [
                'order_number' => 'REQ-2026-001',
                'system_id' => 1,
                'client_id' => 3, // افترضنا أن ID العميل 3
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => 'REQ-2026-002',
                'system_id' => 2,
                'client_id' => 4,
                'status' => 'in_progress',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDay(),
            ],
            [
                'order_number' => 'REQ-2026-003',
                'system_id' => 1,
                'client_id' => 3,
                'status' => 'waiting_client',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'order_number' => 'REQ-2026-004',
                'system_id' => 3,
                'client_id' => 5,
                'status' => 'closed',
                'created_at' => now()->subWeeks(1),
                'updated_at' => now()->subWeeks(1),
            ],
            [
                'order_number' => 'REQ-2026-005',
                'system_id' => 2,
                'client_id' => 4,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($requests as $requestData) {
            Requests::create($requestData);
        }
    }
}

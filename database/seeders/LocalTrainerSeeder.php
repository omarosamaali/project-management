<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalTrainerSeeder extends Seeder
{
    public function run(): void
    {
        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($categories->isEmpty()) {
            $this->command?->warn('LocalTrainerSeeder skipped: no active course categories found.');
            return;
        }

        $fallbackAvatar = User::query()
            ->whereNotNull('avatar')
            ->value('avatar');

        $rows = [
            ['name' => 'Local Trainer One', 'email' => 'local.trainer.1@evorq.test', 'phone' => '+971500000101', 'country' => 'AE', 'lang' => 'ar'],
            ['name' => 'Local Trainer Two', 'email' => 'local.trainer.2@evorq.test', 'phone' => '+971500000102', 'country' => 'SA', 'lang' => 'ar'],
            ['name' => 'Local Trainer Three', 'email' => 'local.trainer.3@evorq.test', 'phone' => '+971500000103', 'country' => 'EG', 'lang' => 'en'],
            ['name' => 'Local Trainer Four', 'email' => 'local.trainer.4@evorq.test', 'phone' => '+971500000104', 'country' => 'JO', 'lang' => 'ar'],
            ['name' => 'Local Trainer Five', 'email' => 'local.trainer.5@evorq.test', 'phone' => '+971500000105', 'country' => 'KW', 'lang' => 'en'],
            ['name' => 'Local Trainer Six', 'email' => 'local.trainer.6@evorq.test', 'phone' => '+971500000106', 'country' => 'AE', 'lang' => 'ar'],
        ];

        foreach ($rows as $idx => $row) {
            $category = $categories[$idx % $categories->count()];

            User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'account_type' => 'personal',
                    'password' => Hash::make('password'),
                    'phone' => $row['phone'],
                    'country' => $row['country'],
                    'role' => 'trainer',
                    'status' => 'active',
                    'course_category_id' => $category->id,
                    'teaching_language' => $row['lang'],
                    'linkedin_url' => 'https://www.linkedin.com/in/'.strtolower(str_replace(' ', '-', $row['name'])),
                    'trainer_bio' => 'Trainer profile seeded for local testing. This account is safe to edit and delete.',
                    'avatar' => $fallbackAvatar,
                    'email_verified_at' => now(),
                    'terms_accepted_at' => now(),
                    'whatsapp_verified' => true,
                ]
            );
        }

        $this->command?->info('LocalTrainerSeeder: trainers created/updated successfully.');
    }
}

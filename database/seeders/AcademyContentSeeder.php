<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseRating;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AcademyContentSeeder extends Seeder
{
    /**
     * Import categories + academy testimonials exported via:
     * php artisan academy:export-seed --copy-icons
     */
    public function run(): void
    {
        $path = base_path('database/seeders/data/academy_content.json');
        if (!File::exists($path)) {
            $this->command?->error("Missing export file: {$path}");
            $this->command?->line('Generate it locally with: php artisan academy:export-seed --copy-icons');
            return;
        }

        $payload = json_decode(File::get($path), true);
        if (!is_array($payload)) {
            $this->command?->error('Invalid academy_content.json');
            return;
        }

        $categories = $payload['categories'] ?? [];
        $testimonials = $payload['testimonials'] ?? [];

        $categoryCount = $this->seedCategories(is_array($categories) ? $categories : []);
        $reviewCount = $this->seedTestimonials(is_array($testimonials) ? $testimonials : []);

        $this->command?->info("Academy content seeded: {$categoryCount} categories, {$reviewCount} testimonials.");
    }

    protected function seedCategories(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $titleEn = trim((string) ($row['title_en'] ?? ''));
            $titleAr = trim((string) ($row['title_ar'] ?? ''));
            if ($titleEn === '' && $titleAr === '') {
                continue;
            }

            $match = $titleEn !== ''
                ? ['title_en' => $titleEn]
                : ['title_ar' => $titleAr];

            $iconPath = $this->resolveCategoryIcon($row);

            $attributes = [
                'title_ar' => $titleAr !== '' ? $titleAr : $titleEn,
                'title_en' => $titleEn !== '' ? $titleEn : $titleAr,
                'description_ar' => $row['description_ar'] ?? null,
                'description_en' => $row['description_en'] ?? null,
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'is_active' => array_key_exists('is_active', $row) ? (bool) $row['is_active'] : true,
            ];

            if ($iconPath) {
                $attributes['icon'] = $iconPath;
            }

            CourseCategory::updateOrCreate($match, $attributes);

            $count++;
        }

        return $count;
    }

    protected function resolveCategoryIcon(array $row): ?string
    {
        $seedFile = trim((string) ($row['icon_seed_file'] ?? ''));
        if ($seedFile !== '') {
            $source = base_path('database/seeders/data/' . ltrim($seedFile, '/'));
            if (File::exists($source)) {
                $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
                $safe = Str::slug(($row['title_en'] ?? '') ?: ($row['title_ar'] ?? 'category')) ?: 'category';
                $dest = 'course-categories/' . $safe . '-' . substr(sha1($source), 0, 8) . '.' . $ext;
                Storage::disk('public')->put($dest, File::get($source));
                return $dest;
            }
        }

        $icon = trim((string) ($row['icon'] ?? ''));
        if ($icon !== '' && Storage::disk('public')->exists($icon)) {
            return ltrim($icon, '/');
        }

        return null;
    }

    protected function seedTestimonials(array $rows): int
    {
        $count = 0;

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }

            $course = $this->findCourse($row);
            if (!$course) {
                $this->command?->warn(sprintf(
                    'Skip testimonial #%d — course not found: %s / %s',
                    $index + 1,
                    $row['course_name_ar'] ?? '?',
                    $row['course_name_en'] ?? '?'
                ));
                continue;
            }

            $user = $this->ensureReviewer($row, $index);
            $payment = $this->ensurePayment($course, $user);

            CourseRating::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
                [
                    'payment_id' => $payment?->id,
                    'answers' => is_array($row['answers'] ?? null) ? $row['answers'] : [],
                    'is_featured' => (bool) ($row['is_featured'] ?? true),
                    'completed_at' => !empty($row['completed_at'])
                        ? $row['completed_at']
                        : now()->subDays($index + 1),
                ]
            );

            $count++;
        }

        return $count;
    }

    protected function findCourse(array $row): ?Course
    {
        $nameAr = trim((string) ($row['course_name_ar'] ?? ''));
        $nameEn = trim((string) ($row['course_name_en'] ?? ''));

        if ($nameAr !== '') {
            $course = Course::where('name_ar', $nameAr)->first();
            if ($course) {
                return $course;
            }
        }

        if ($nameEn !== '') {
            $course = Course::where('name_en', $nameEn)->first();
            if ($course) {
                return $course;
            }
        }

        return null;
    }

    protected function ensureReviewer(array $row, int $index): User
    {
        $email = trim((string) ($row['user_email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'seed.review.' . ($index + 1) . '@academy.import';
        }

        $name = trim((string) ($row['user_name'] ?? '')) ?: ('متدرب ' . ($index + 1));

        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'account_type' => 'personal',
                'password' => Hash::make(Str::random(24)),
                'role' => 'trainee',
                'status' => 'active',
                'email_verified_at' => now(),
                'whatsapp_verified' => true,
            ]
        );
    }

    protected function ensurePayment(Course $course, User $user): ?Payment
    {
        $existing = Payment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Minimal enrollment row so ratings stay consistent with app rules
        return Payment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'amount' => 0,
            'original_price' => 0,
            'fees' => 0,
            'currency' => 'AED',
            'status' => 'completed',
            'payment_method' => 'seed',
            'is_attended' => true,
        ]);
    }
}

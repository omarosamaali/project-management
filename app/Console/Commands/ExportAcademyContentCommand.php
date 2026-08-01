<?php

namespace App\Console\Commands;

use App\Models\CourseCategory;
use App\Models\CourseRating;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportAcademyContentCommand extends Command
{
    protected $signature = 'academy:export-seed
        {--path=database/seeders/data/academy_content.json : Output JSON path relative to base_path}
        {--all-reviews : Include all completed ratings, not only featured}
        {--copy-icons : Copy category icon files into database/seeders/data/category-icons}';

    protected $description = 'Export current course categories and academy testimonials (ratings) to a JSON file for live seeding';

    public function handle(): int
    {
        $relative = ltrim(str_replace('\\', '/', (string) $this->option('path')), '/');
        $outputPath = base_path($relative);
        $dir = dirname($outputPath);
        File::ensureDirectoryExists($dir);

        $categories = CourseCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (CourseCategory $category) {
                $icon = $category->icon ? ltrim((string) $category->icon, '/') : null;

                return [
                    'title_ar' => $category->title_ar,
                    'title_en' => $category->title_en,
                    'description_ar' => $category->description_ar,
                    'description_en' => $category->description_en,
                    'icon' => $icon,
                    'sort_order' => (int) $category->sort_order,
                    'is_active' => (bool) $category->is_active,
                ];
            })
            ->values()
            ->all();

        if ($this->option('copy-icons')) {
            $iconsDir = base_path('database/seeders/data/category-icons');
            File::ensureDirectoryExists($iconsDir);
            foreach ($categories as &$row) {
                if (empty($row['icon'])) {
                    continue;
                }
                $diskPath = $row['icon'];
                if (!Storage::disk('public')->exists($diskPath)) {
                    $this->warn("Icon missing on disk: {$diskPath}");
                    continue;
                }
                $ext = pathinfo($diskPath, PATHINFO_EXTENSION) ?: 'png';
                $safe = Str::slug($row['title_en'] ?: $row['title_ar'] ?: 'category') ?: 'category';
                $targetName = $safe . '.' . $ext;
                File::put(
                    $iconsDir . DIRECTORY_SEPARATOR . $targetName,
                    Storage::disk('public')->get($diskPath)
                );
                $row['icon_seed_file'] = 'category-icons/' . $targetName;
            }
            unset($row);
        }

        $ratingsQuery = CourseRating::query()
            ->whereNotNull('completed_at')
            ->with(['user:id,name,email,avatar', 'course:id,name_ar,name_en'])
            ->latest('completed_at');

        if (!$this->option('all-reviews')) {
            $ratingsQuery->where('is_featured', true);
        }

        $testimonials = $ratingsQuery->get()
            ->filter(fn (CourseRating $r) => $r->course && $r->user)
            ->map(function (CourseRating $rating) {
                return [
                    'course_name_ar' => $rating->course->name_ar,
                    'course_name_en' => $rating->course->name_en,
                    'user_name' => $rating->user->name,
                    'user_email' => $rating->user->email,
                    'user_avatar' => $rating->user->avatar ? ltrim((string) $rating->user->avatar, '/') : null,
                    'answers' => $rating->answers ?? [],
                    'is_featured' => (bool) $rating->is_featured,
                    'completed_at' => optional($rating->completed_at)->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'app_url' => config('app.url'),
            'categories' => $categories,
            'testimonials' => $testimonials,
        ];

        File::put(
            $outputPath,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('Exported academy content:');
        $this->line('  Categories:    ' . count($categories));
        $this->line('  Testimonials:  ' . count($testimonials));
        $this->line('  File:          ' . $outputPath);
        $this->newLine();
        $this->comment('On live, deploy this JSON (and category-icons if copied), then run:');
        $this->line('  php artisan db:seed --class=AcademyContentSeeder');

        return self::SUCCESS;
    }
}

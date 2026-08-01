<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Opacity & size
    |--------------------------------------------------------------------------
    */
    'opacity' => (float) env('WATERMARK_OPACITY', 0.38),
    'width_ratio' => (float) env('WATERMARK_WIDTH_RATIO', 0.18),
    'min_logo_width' => 48,
    'max_logo_width' => 220,
    'margin' => 16,

    /*
    |--------------------------------------------------------------------------
    | Bake into files
    |--------------------------------------------------------------------------
    | Off by default: logos are shown via CSS overlays so AR can place left
    | and EN can place right. Set WATERMARK_BAKE=true to burn into uploads.
    */
    'bake' => filter_var(env('WATERMARK_BAKE', false), FILTER_VALIDATE_BOOL),

    /*
    |--------------------------------------------------------------------------
    | Logo assets (absolute paths resolved at runtime)
    |--------------------------------------------------------------------------
    | academy_watermark.png = white mark + soft shadow (readable on light/dark).
    */
    'logos' => [
        'academy' => public_path('assets/images/academy_watermark.png'),
        'app' => public_path('assets/images/logo.webp'),
    ],

    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),

    /*
    |--------------------------------------------------------------------------
    | Brand by storage prefix (relative to disk root or public/uploads)
    |--------------------------------------------------------------------------
    */
    'academy_prefixes' => [
        'courses/',
        'course-categories/',
        'trainers/avatars/',
        'academy/settings/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Skip watermark (logos, IDs, documents, brand assets)
    |--------------------------------------------------------------------------
    */
    'skip_prefixes' => [
        'logos/',
        'landing/brands/',
        'trainers/id-cards/',
        'partners/documents/',
        'clients/company-logos/',
        'payment_proofs/',
        'attachments/',
        'project_files/',
        'project_approvals/',
        'request_files/',
        'salaries/',
        'uploads/kb/',
    ],

    'skip_filename_contains' => [
        'logo',
        'favicon',
        'fav-icon',
        'invoice_logo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories scanned by media:apply-watermarks
    |--------------------------------------------------------------------------
    */
    'scan' => [
        'disk' => [
            'courses',
            'course-categories',
            'trainers/avatars',
            'academy/settings',
            'services',
            'expenses',
            'issues',
            'issue_comments',
            'remarks',
            'partners/avatars',
            'partners/videos',
            'landing',
            'service',
            'systems',
        ],
        'public_paths' => [
            'uploads/systems',
        ],
    ],

];

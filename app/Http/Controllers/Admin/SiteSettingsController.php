<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Setting;
use App\Support\PublicStoragePublisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $s = [
            // هوية الموقع
            'site_name'         => Setting::get('site_name', 'Pet Clinic'),
            'company_name'      => Setting::get('company_name', ''),
            'primary_color'     => Setting::get('primary_color', '#336cfa'),
            'secondary_color'   => Setting::get('secondary_color', '#104776'),
            'logo_path'         => Setting::get('logo_path'),
            'favicon_path'      => Setting::get('favicon_path'),
            'login_banner_path' => Setting::get('login_banner'),
            // الفاتورة
            'invoice_name_ar'   => Setting::get('invoice_name_ar', ''),
            'invoice_name_en'   => Setting::get('invoice_name_en', ''),
            'invoice_address_ar'=> Setting::get('invoice_address_ar', ''),
            'invoice_address_en'=> Setting::get('invoice_address_en', ''),
            'invoice_license_ar'=> Setting::get('invoice_license_ar', ''),
            'invoice_license_en'=> Setting::get('invoice_license_en', ''),
            'tax_percentage'    => Setting::get('tax_percentage', '0'),
            'invoice_footer'    => Setting::get('invoice_footer', ''),
            'invoice_footer_en' => Setting::get('invoice_footer_en', ''),
            'invoice_logo_path' => Setting::get('invoice_logo_path'),
            'default_lang'      => Setting::get('default_lang', 'ar'),
            'currency'          => Setting::get('currency', 'AED'),
            // بوابة الدفع
            'ziina_api_key'     => Setting::get('ziina_api_key', env('ZIINA_API_KEY', '')),
            'ziina_test_mode'   => Setting::get('ziina_test_mode', env('ZIINA_TEST_MODE', 'true')),
            // أرشيف الميدان
            'admin_animals_api_url'    => Setting::get('admin_animals_api_url', env('ADMIN_ANIMALS_API_URL', 'http://127.0.0.1:8001/api/v1')),
            'admin_animals_api_key'    => Setting::get('admin_animals_api_key', env('ADMIN_ANIMALS_API_KEY', '')),
            'platform_webhook_secret'  => Setting::get('platform_webhook_secret', env('PLATFORM_WEBHOOK_SECRET', '')),
        ];

        // إعدادات الموقع الخارجي (Landing)
        $landing = [
            'default_lang'          => Setting::get('landing_default_lang', 'ar'),
            'primary_color'         => Setting::get('landing_primary_color', Setting::get('primary_color', '#894B8D')),
            'secondary_color'       => Setting::get('landing_secondary_color', Setting::get('secondary_color', '#002169')),
            // معلومات عامة
            'site_name_ar'          => Setting::get('landing_site_name_ar', ''),
            'site_name_en'          => Setting::get('landing_site_name_en', ''),
            'address_ar'            => Setting::get('landing_address_ar', ''),
            'address_en'            => Setting::get('landing_address_en', ''),
            'email'                 => Setting::get('landing_email', ''),
            'phone'                 => Setting::get('landing_phone', ''),
            'opening_hours_ar'      => Setting::get('landing_opening_hours_ar', ''),
            'opening_hours_en'      => Setting::get('landing_opening_hours_en', ''),
            // واتساب للمحادثة
            'whatsapp_number'       => Setting::get('landing_whatsapp_number', ''),
            // سوشيال
            'facebook'              => Setting::get('landing_facebook', ''),
            'twitter'               => Setting::get('landing_twitter', ''),
            'whatsapp'              => Setting::get('landing_whatsapp', ''),
            'instagram'             => Setting::get('landing_instagram', ''),
            'youtube'               => Setting::get('landing_youtube', ''),
            // الشعارات
            'logo_colored_path'     => Setting::get('landing_logo_colored'),
            'logo_white_path'       => Setting::get('landing_logo_white'),
            // البانر
            'banner_title_ar'       => Setting::get('landing_banner_title_ar', 'مركز رعاية الحيوانات الأليفة'),
            'banner_title_en'       => Setting::get('landing_banner_title_en', 'Trusted Pet Care & Veterinary Center'),
            'banner_desc_ar'        => Setting::get('landing_banner_desc_ar', ''),
            'banner_desc_en'        => Setting::get('landing_banner_desc_en', ''),
            'banner_btn_ar'         => Setting::get('landing_banner_btn_ar', 'اقرأ المزيد'),
            'banner_btn_en'         => Setting::get('landing_banner_btn_en', 'Read More'),
            'banner_bg_path'        => Setting::get('landing_banner_bg'),
            'banner_img_path'       => Setting::get('landing_banner_img'),
            // قسم "عنا"
            'about_subtitle_ar'     => Setting::get('landing_about_subtitle_ar', 'اعرف المزيد عنا'),
            'about_subtitle_en'     => Setting::get('landing_about_subtitle_en', 'Know More About Us'),
            'about_title_ar'        => Setting::get('landing_about_title_ar', 'شغفنا هو تقديم رعاية متميزة للحيوانات'),
            'about_title_en'        => Setting::get('landing_about_title_en', 'Our Passion Is Providing Superior Pet Care'),
            'about_desc1_ar'        => Setting::get('landing_about_desc1_ar', ''),
            'about_desc1_en'        => Setting::get('landing_about_desc1_en', ''),
            'about_desc2_ar'        => Setting::get('landing_about_desc2_ar', ''),
            'about_desc2_en'        => Setting::get('landing_about_desc2_en', ''),
            'about_years'           => Setting::get('landing_about_years', '15'),
            'about_video_url'       => Setting::get('landing_about_video_url', 'https://www.youtube.com/watch?v=XdFfCPK5ycw'),
            'about_rating'          => Setting::get('landing_about_rating', '4.7'),
            'about_reviews'         => Setting::get('landing_about_reviews', '1,567'),
            'about_sign_img_path'   => Setting::get('landing_about_sign_img'),
            'about_img_path'        => Setting::get('landing_about_img'),
            // الشريط المتحرك
            'marquee_ar'            => Setting::get('landing_marquee_ar', 'احجز موعدك الآن'),
            'marquee_en'            => Setting::get('landing_marquee_en', 'Book For Online Appointment'),
            // الخدمات
            'services_subtitle_ar'  => Setting::get('landing_services_subtitle_ar', 'نقدم رعاية عالمية المستوى'),
            'services_subtitle_en'  => Setting::get('landing_services_subtitle_en', 'Delivering world class home care'),
            'services_title_ar'     => Setting::get('landing_services_title_ar', 'أفضل خدماتنا لرعاية حيوانك الأليف'),
            'services_title_en'     => Setting::get('landing_services_title_en', 'Providing Our Best Pet Care & Veterinary Services'),
            'services_selected_ids' => json_decode(Setting::get('landing_services_selected_ids', '[]'), true) ?: [],
            // لماذا نحن
            'why_subtitle_ar'       => Setting::get('landing_why_subtitle_ar', 'لماذا نحن الأفضل'),
            'why_subtitle_en'       => Setting::get('landing_why_subtitle_en', 'Why We are The Best'),
            'why_title_ar'          => Setting::get('landing_why_title_ar', 'حالات طوارئ الحيوانات — ما تحتاج معرفته'),
            'why_title_en'          => Setting::get('landing_why_title_en', 'Pet emergencies what you need to know.'),
            'why_desc_ar'           => Setting::get('landing_why_desc_ar', ''),
            'why_desc_en'           => Setting::get('landing_why_desc_en', ''),
            'why_items'             => Setting::get('landing_why_items', json_encode([
                ['title_ar'=>'خبرة واسعة',       'title_en'=>'More Experience',      'desc_ar'=>'ثقة كاملة في خطة العلاج وقدرات طاقمنا',  'desc_en'=>'Be confident in the treatment plan and doctor abilities.'],
                ['title_ar'=>'أسعار معقولة',      'title_en'=>'Affordable Pricing',   'desc_ar'=>'ثقة كاملة في خطة العلاج وقدرات طاقمنا',  'desc_en'=>'Be confident in the treatment plan and doctor abilities.'],
                ['title_ar'=>'تدريب حديث',        'title_en'=>'Modern Pet Training',  'desc_ar'=>'ثقة كاملة في خطة العلاج وقدرات طاقمنا',  'desc_en'=>'Be confident in the treatment plan and doctor abilities.'],
                ['title_ar'=>'روتين يومي منتظم',  'title_en'=>'Daily Routine',        'desc_ar'=>'ثقة كاملة في خطة العلاج وقدرات طاقمنا',  'desc_en'=>'Be confident in the treatment plan and doctor abilities.'],
            ])),
            'why_img_path'          => Setting::get('landing_why_img'),
            // شعارات الشركاء
            'brand_logos'           => array_combine(range(1,8), array_map(fn($n) => Setting::get("landing_brand_logo_{$n}"), range(1,8))),
            // العداد
            'counter_subtitle_ar'   => Setting::get('landing_counter_subtitle_ar', 'ثقتك أولويتنا'),
            'counter_subtitle_en'   => Setting::get('landing_counter_subtitle_en', 'Your Trust Our Priority'),
            'counter_title_ar'      => Setting::get('landing_counter_title_ar', 'رعاية احترافية وجودة مضمونة'),
            'counter_title_en'      => Setting::get('landing_counter_title_en', 'Professional care and guaranteed quality'),
            'counter_desc_ar'       => Setting::get('landing_counter_desc_ar', ''),
            'counter_desc_en'       => Setting::get('landing_counter_desc_en', ''),
            'counter_img_path'      => Setting::get('landing_counter_img'),
            'counter_items'         => Setting::get('landing_counter_items', json_encode([
                ['num'=>'15', 'suffix'=>'+',  'label_ar'=>'سنة خبرة',         'label_en'=>'Years of experience'],
                ['num'=>'23', 'suffix'=>'K',  'label_ar'=>'عميل موثوق',       'label_en'=>'Beloved Clients'],
                ['num'=>'15', 'suffix'=>'K+', 'label_ar'=>'آراء حقيقية',      'label_en'=>'Real Customer Reviews'],
            ])),
            // التقييمات
            'testimonial_img_path'  => Setting::get('landing_testimonial_img'),
            'testimonial_reviews'   => Setting::get('landing_testimonial_reviews', '1500+'),
            'testimonials'          => Setting::get('landing_testimonials', json_encode([
                ['title_ar'=>'صحة الحيوانات مهمة', 'title_en'=>'Pet Health Important', 'text_ar'=>'نقدم لحيواناتكم الأليفة أفضل رعاية طبية بأيدي متخصصين موثوقين.', 'text_en'=>'We provide your beloved pets with the best medical care by trusted specialists.', 'author_name'=>'Ahmed Salem', 'role_ar'=>'عميل موثوق', 'role_en'=>'Loyal Client'],
                ['title_ar'=>'خدمة رائعة', 'title_en'=>'Amazing Service', 'text_ar'=>'فريق طبي محترف ومتعاطف، أنصح الجميع بزيارة العيادة.', 'text_en'=>'Professional and compassionate medical team, I recommend everyone visit the clinic.', 'author_name'=>'Sara Johnson', 'role_ar'=>'عميل دائم', 'role_en'=>'Regular Client'],
            ])),
            // إنستجرام
            'instagram_btn_ar'      => Setting::get('landing_instagram_btn_ar', 'تابعنا على إنستجرام'),
            'instagram_btn_en'      => Setting::get('landing_instagram_btn_en', 'Follow Us On Instagram'),
            'instagram_images'      => array_combine(range(1, 5), array_map(fn($n) => Setting::get("landing_instagram_img_{$n}"), range(1, 5))),
            'instagram_links'       => array_combine(range(1, 5), array_map(fn($n) => Setting::get("landing_instagram_link_{$n}", ''), range(1, 5))),
            // المدونة
            'blog_subtitle_ar'      => Setting::get('landing_blog_subtitle_ar', 'أخبار ومقالات'),
            'blog_subtitle_en'      => Setting::get('landing_blog_subtitle_en', 'News & Blogs'),
            'blog_title_ar'         => Setting::get('landing_blog_title_ar', 'أحدث مقالاتنا'),
            'blog_title_en'         => Setting::get('landing_blog_title_en', 'Our Recent Articles'),
            'blog_btn_ar'           => Setting::get('landing_blog_btn_ar', 'عرض كل المقالات'),
            'blog_btn_en'           => Setting::get('landing_blog_btn_en', 'See All Posts'),
            'blog_cards'            => array_combine(range(1, 3), array_map(fn($n) => [
                'title_ar' => Setting::get("landing_blog_card_{$n}_title_ar", 'Clean indoor air as important in controlling asthma'),
                'title_en' => Setting::get("landing_blog_card_{$n}_title_en", 'Clean indoor air as important in controlling asthma'),
                'tags_ar'  => Setting::get("landing_blog_card_{$n}_tags_ar", ['Pet,Medical','Care','Pet Care'][$n-1]),
                'tags_en'  => Setting::get("landing_blog_card_{$n}_tags_en", ['Pet,Medical','Care','Pet Care'][$n-1]),
                'author'   => Setting::get("landing_blog_card_{$n}_author", 'admin'),
                'date'     => Setting::get("landing_blog_card_{$n}_date", '25th Aug, 2024'),
                'link'     => Setting::get("landing_blog_card_{$n}_link", ''),
                'image'    => Setting::get("landing_blog_card_{$n}_image"),
            ], range(1, 3))),
            // ساعات العمل
            'working_hours'         => json_decode(Setting::get('landing_working_hours', '{}'), true) ?: [
                'monday'    => [['open'=>'09:00','close'=>'17:00']],
                'tuesday'   => [['open'=>'09:00','close'=>'17:00']],
                'wednesday' => [['open'=>'09:00','close'=>'17:00']],
                'thursday'  => [['open'=>'09:00','close'=>'17:00']],
                'friday'    => [['open'=>'09:00','close'=>'17:00']],
                'saturday'  => [],
                'sunday'    => [],
            ],
            // الفوتر
            'footer_address_ar'     => Setting::get('landing_footer_address_ar', ''),
            'footer_address_en'     => Setting::get('landing_footer_address_en', ''),
            'footer_phone'          => Setting::get('landing_footer_phone', ''),
            'footer_email'          => Setting::get('landing_footer_email', ''),
            'footer_copyright_ar'   => Setting::get('landing_footer_copyright_ar', 'جميع الحقوق محفوظة'),
            'footer_copyright_en'   => Setting::get('landing_footer_copyright_en', 'All Rights Reserved'),
        ];

        $allOurServices  = \App\Models\OurService::orderBy('name')->get();
        $announcements   = \App\Models\Announcement::latest()->get();

        $integrations = [
            // واتساب
            'wa_token'       => Setting::get('wa_token',       env('WHATSAPP_TOKEN', '')),
            'wa_phone_id'    => Setting::get('wa_phone_id',    env('WHATSAPP_PHONE_ID', '')),
            'wa_business_id' => Setting::get('wa_business_id', env('WHATSAPP_BUSINESS_ID', '')),
            // بريد إلكتروني
            'mail_host'       => Setting::get('mail_host',       env('MAIL_HOST', 'smtp.gmail.com')),
            'mail_port'       => Setting::get('mail_port',       env('MAIL_PORT', '587')),
            'mail_encryption' => Setting::get('mail_encryption', env('MAIL_ENCRYPTION', 'tls')),
            'mail_username'   => Setting::get('mail_username',   env('MAIL_USERNAME', '')),
            'mail_password'   => Setting::get('mail_password',   env('MAIL_PASSWORD', '')),
            'mail_from_address' => Setting::get('mail_from_address', env('MAIL_FROM_ADDRESS', '')),
            'mail_from_name'    => Setting::get('mail_from_name',    env('MAIL_FROM_NAME', 'Pet Clinic')),
            'zoom_account_id'   => Setting::get('zoom_account_id', ''),
            'zoom_client_id'    => Setting::get('zoom_client_id', ''),
            'zoom_client_secret'=> Setting::get('zoom_client_secret', ''),
        ];

        $appDomain = rtrim(config('app.url'), '/');

        return view('dashboard.site_settings.index', compact('s', 'landing', 'allOurServices', 'integrations', 'appDomain', 'announcements'));
    }

    public function save(Request $request)
    {
        $tab = $request->input('tab', 'identity');

        if ($tab === 'identity') {
            $request->validate([
                'site_name'       => 'required|string|max:100',
                'company_name'    => 'nullable|string|max:100',
                'primary_color'   => 'nullable|string|max:20',
                'secondary_color' => 'nullable|string|max:20',
                'logo'            => 'nullable|image|max:2048',
                'favicon'         => 'nullable|image|max:512',
                'logo_colored'    => 'nullable|image|max:1024',
                'logo_white'      => 'nullable|image|max:1024',
                'login_banner'    => 'nullable|image|max:4096',
            ]);

            Setting::setMany([
                'site_name'       => $request->site_name,
                'company_name'    => $request->company_name,
                'primary_color'   => $request->primary_color ?? '#336cfa',
                'secondary_color' => $request->secondary_color ?? '#104776',
            ]);

            if ($request->hasFile('logo')) {
                $old = Setting::get('logo_path');
                if ($old) Storage::disk('public')->delete($old);
                $path = PublicStoragePublisher::storeAndPublish($request->file('logo'), 'site');
                Setting::set('logo_path', $path);

                $companyId = app()->bound('company_id')
                    ? (int) app('company_id')
                    : (int) session('company_id');
                if ($companyId > 0) {
                    Company::query()->whereKey($companyId)->update(['logo_path' => $path]);
                }
            }

            if ($request->hasFile('favicon')) {
                $old = Setting::get('favicon_path');
                if ($old) Storage::disk('public')->delete($old);
                $path = PublicStoragePublisher::storeAndPublish($request->file('favicon'), 'site');
                Setting::set('favicon_path', $path);
            }

            foreach (['logo_colored' => 'landing_logo_colored', 'logo_white' => 'landing_logo_white'] as $field => $key) {
                if ($request->hasFile($field)) {
                    $old = Setting::get($key);
                    if ($old) Storage::disk('public')->delete($old);
                    $path = PublicStoragePublisher::storeAndPublish($request->file($field), 'landing');
                    Setting::set($key, $path);
                }
            }

            if ($request->hasFile('login_banner')) {
                $old = Setting::get('login_banner');
                if ($old) Storage::disk('public')->delete($old);
                Setting::set('login_banner', PublicStoragePublisher::storeAndPublish($request->file('login_banner'), 'site'));
            }

            if ($request->input('remove_login_banner')) {
                $old = Setting::get('login_banner');
                if ($old) Storage::disk('public')->delete($old);
                Setting::set('login_banner', null);
            }

            return back()->with('success', 'تم حفظ إعدادات هوية الموقع');
        }

        if ($tab === 'invoice') {
            $request->validate([
                'invoice_name_ar'    => 'nullable|string|max:100',
                'invoice_name_en'    => 'nullable|string|max:100',
                'invoice_address_ar' => 'nullable|string|max:500',
                'invoice_address_en' => 'nullable|string|max:500',
                'invoice_license_ar' => 'nullable|string|max:255',
                'invoice_license_en' => 'nullable|string|max:255',
                'tax_percentage'     => 'nullable|numeric|min:0|max:100',
                'invoice_footer'     => 'nullable|string|max:500',
                'invoice_footer_en'  => 'nullable|string|max:500',
                'default_lang'       => 'nullable|in:ar,en',
                'currency'           => 'nullable|string|max:10',
                'invoice_logo'       => 'nullable|image|max:2048',
            ]);

            Setting::setMany([
                'invoice_name_ar'    => $request->invoice_name_ar,
                'invoice_name_en'    => $request->invoice_name_en,
                'invoice_address_ar' => $request->invoice_address_ar,
                'invoice_address_en' => $request->invoice_address_en,
                'invoice_license_ar' => $request->invoice_license_ar,
                'invoice_license_en' => $request->invoice_license_en,
                'tax_percentage'     => $request->tax_percentage ?? '0',
                'invoice_footer'     => $request->invoice_footer,
                'invoice_footer_en'  => $request->invoice_footer_en,
                'default_lang'       => $request->default_lang ?? 'ar',
                'currency'           => $request->currency ?? 'AED',
            ]);

            if ($request->hasFile('invoice_logo')) {
                $old = Setting::get('invoice_logo_path');
                if ($old) Storage::disk('public')->delete($old);
                $path = PublicStoragePublisher::storeAndPublish($request->file('invoice_logo'), 'site');
                Setting::set('invoice_logo_path', $path);
            }

            return back()->with('success', 'تم حفظ إعدادات الفاتورة');
        }

        if ($tab === 'payment') {
            $request->validate([
                'ziina_api_key'   => 'nullable|string|max:500',
                'ziina_test_mode' => 'nullable|in:true,false',
            ]);

            Setting::setMany([
                'ziina_api_key'   => $request->ziina_api_key ?? '',
                'ziina_test_mode' => $request->ziina_test_mode ?? 'false',
            ]);

            return back()->with('success', 'تم حفظ إعدادات بوابة الدفع');
        }

        if ($tab === 'platform') {
            $request->validate([
                'admin_animals_api_url'   => 'nullable|url|max:500',
                'admin_animals_api_key'   => 'nullable|string|max:500',
                'platform_webhook_secret' => 'nullable|string|max:255',
            ]);

            Setting::setMany([
                'admin_animals_api_url'   => rtrim($request->admin_animals_api_url ?? '', '/'),
                'admin_animals_api_key'   => $request->admin_animals_api_key ?? '',
                'platform_webhook_secret' => $request->platform_webhook_secret ?? '',
            ]);

            return back()->with('success', 'تم حفظ إعدادات ربط أرشيف الميدان');
        }

        if ($tab === 'landing') {
            $section = $request->input('section', 'general');

            if ($section === 'general') {
                Setting::setMany([
                    'landing_default_lang'      => $request->default_lang ?? 'ar',
                    'landing_site_name_ar'       => $request->site_name_ar ?? '',
                    'landing_site_name_en'       => $request->site_name_en ?? '',
                    'landing_address_ar'         => $request->address_ar ?? '',
                    'landing_address_en'         => $request->address_en ?? '',
                    'landing_email'              => $request->email ?? '',
                    'landing_phone'              => $request->phone ?? '',
                    'landing_opening_hours_ar'   => $request->opening_hours_ar ?? '',
                    'landing_opening_hours_en'   => $request->opening_hours_en ?? '',
                    'landing_whatsapp_number'    => $request->whatsapp_number ?? '',
                    'landing_facebook'           => $request->facebook ?? '',
                    'landing_twitter'            => $request->twitter ?? '',
                    'landing_whatsapp'           => $request->whatsapp ?? '',
                    'landing_instagram'          => $request->instagram ?? '',
                    'landing_youtube'            => $request->youtube ?? '',
                ]);
                foreach (['logo_colored'=>'landing_logo_colored','logo_white'=>'landing_logo_white'] as $field=>$key) {
                    if ($request->hasFile($field)) {
                        $old = Setting::get($key);
                        if ($old) Storage::disk('public')->delete($old);
                        Setting::set($key, PublicStoragePublisher::storeAndPublish($request->file($field), 'landing'));
                    }
                }
            }

            if ($section === 'banner') {
                Setting::setMany([
                    'landing_banner_title_ar' => $request->banner_title_ar ?? '',
                    'landing_banner_title_en' => $request->banner_title_en ?? '',
                    'landing_banner_desc_ar'  => $request->banner_desc_ar ?? '',
                    'landing_banner_desc_en'  => $request->banner_desc_en ?? '',
                    'landing_banner_btn_ar'   => $request->banner_btn_ar ?? '',
                    'landing_banner_btn_en'   => $request->banner_btn_en ?? '',
                ]);
                foreach (['banner_bg'=>'landing_banner_bg','banner_img'=>'landing_banner_img'] as $field=>$key) {
                    if ($request->hasFile($field)) {
                        $old = Setting::get($key);
                        if ($old) Storage::disk('public')->delete($old);
                        Setting::set($key, PublicStoragePublisher::storeAndPublish($request->file($field), 'landing'));
                    }
                }
            }

            if ($section === 'about') {
                Setting::setMany([
                    'landing_about_subtitle_ar' => $request->about_subtitle_ar ?? '',
                    'landing_about_subtitle_en' => $request->about_subtitle_en ?? '',
                    'landing_about_title_ar'    => $request->about_title_ar ?? '',
                    'landing_about_title_en'    => $request->about_title_en ?? '',
                    'landing_about_desc1_ar'    => $request->about_desc1_ar ?? '',
                    'landing_about_desc1_en'    => $request->about_desc1_en ?? '',
                    'landing_about_desc2_ar'    => $request->about_desc2_ar ?? '',
                    'landing_about_desc2_en'    => $request->about_desc2_en ?? '',
                    'landing_about_years'       => $request->about_years ?? '15',
                    'landing_about_video_url'   => $request->about_video_url ?? '',
                    'landing_about_rating'      => $request->about_rating ?? '4.7',
                    'landing_about_reviews'     => $request->about_reviews ?? '1,567',
                    'landing_marquee_ar'        => $request->marquee_ar ?? '',
                    'landing_marquee_en'        => $request->marquee_en ?? '',
                ]);
                if ($request->hasFile('about_sign_img')) {
                    $old = Setting::get('landing_about_sign_img');
                    if ($old) Storage::disk('public')->delete($old);
                    Setting::set('landing_about_sign_img', PublicStoragePublisher::storeAndPublish($request->file('about_sign_img'), 'landing'));
                }
                if ($request->hasFile('about_img')) {
                    $old = Setting::get('landing_about_img');
                    if ($old) Storage::disk('public')->delete($old);
                    Setting::set('landing_about_img', PublicStoragePublisher::storeAndPublish($request->file('about_img'), 'landing'));
                }
            }

            if ($section === 'services') {
                Setting::setMany([
                    'landing_services_subtitle_ar' => $request->services_subtitle_ar ?? '',
                    'landing_services_subtitle_en' => $request->services_subtitle_en ?? '',
                    'landing_services_title_ar'    => $request->services_title_ar ?? '',
                    'landing_services_title_en'    => $request->services_title_en ?? '',
                ]);
                $ids = array_values(array_filter(array_map('intval', (array)$request->input('services_selected_ids', []))));
                Setting::set('landing_services_selected_ids', json_encode(array_slice($ids, 0, 4)));
                return back()->with(['success' => 'تم حفظ إعدادات الخدمات', '_landing_sec' => 'services'])->withInput(['tab' => 'landing']);
            }

            if ($section === 'why') {
                Setting::setMany([
                    'landing_why_subtitle_ar' => $request->why_subtitle_ar ?? '',
                    'landing_why_subtitle_en' => $request->why_subtitle_en ?? '',
                    'landing_why_title_ar'    => $request->why_title_ar ?? '',
                    'landing_why_title_en'    => $request->why_title_en ?? '',
                    'landing_why_desc_ar'     => $request->why_desc_ar ?? '',
                    'landing_why_desc_en'     => $request->why_desc_en ?? '',
                ]);
                $why = [];
                foreach (range(0, 3) as $i) {
                    $why[] = [
                        'title_ar' => $request->input("why_items.{$i}.title_ar", ''),
                        'title_en' => $request->input("why_items.{$i}.title_en", ''),
                        'desc_ar'  => $request->input("why_items.{$i}.desc_ar", ''),
                        'desc_en'  => $request->input("why_items.{$i}.desc_en", ''),
                    ];
                }
                Setting::set('landing_why_items', json_encode($why, JSON_UNESCAPED_UNICODE));

                if ($request->hasFile('why_img')) {
                    $old = Setting::get('landing_why_img');
                    if ($old) Storage::disk('public')->delete($old);
                    Setting::set('landing_why_img', PublicStoragePublisher::storeAndPublish($request->file('why_img'), 'landing'));
                }
            }

            if ($section === 'counter') {
                Setting::setMany([
                    'landing_counter_subtitle_ar' => $request->counter_subtitle_ar ?? '',
                    'landing_counter_subtitle_en' => $request->counter_subtitle_en ?? '',
                    'landing_counter_title_ar'    => $request->counter_title_ar ?? '',
                    'landing_counter_title_en'    => $request->counter_title_en ?? '',
                    'landing_counter_desc_ar'     => $request->counter_desc_ar ?? '',
                    'landing_counter_desc_en'     => $request->counter_desc_en ?? '',
                ]);
                $items = [];
                foreach (range(0, 2) as $i) {
                    $items[] = [
                        'num'      => $request->input("counter_items.{$i}.num", '0'),
                        'suffix'   => $request->input("counter_items.{$i}.suffix", '+'),
                        'label_ar' => $request->input("counter_items.{$i}.label_ar", ''),
                        'label_en' => $request->input("counter_items.{$i}.label_en", ''),
                    ];
                }
                Setting::set('landing_counter_items', json_encode($items, JSON_UNESCAPED_UNICODE));
                if ($request->hasFile('counter_img')) {
                    $old = Setting::get('landing_counter_img');
                    if ($old) Storage::disk('public')->delete($old);
                    Setting::set('landing_counter_img', PublicStoragePublisher::storeAndPublish($request->file('counter_img'), 'landing'));
                }
            }

            if ($section === 'brands') {
                foreach (range(1, 8) as $n) {
                    if ($request->hasFile("brand_logo_{$n}")) {
                        $old = Setting::get("landing_brand_logo_{$n}");
                        if ($old) Storage::disk('public')->delete($old);
                        Setting::set("landing_brand_logo_{$n}", PublicStoragePublisher::storeAndPublish($request->file("brand_logo_{$n}"), 'landing/brands'));
                    }
                }
            }

            if ($section === 'testimonials') {
                Setting::set('landing_testimonial_reviews', $request->testimonial_reviews ?? '1500+');

                // استرجاع التقييمات الموجودة للحفاظ على صور العملاء القديمة
                $existing = json_decode(Setting::get('landing_testimonials', '[]'), true) ?: [];

                $testis = [];
                foreach (range(0, 4) as $i) {
                    $name = $request->input("testimonials.{$i}.author_name", '');
                    if (!$name) continue;

                    $authorPhoto = $existing[$i]['author_photo'] ?? null;
                    if ($request->hasFile("testimonials.{$i}.author_photo")) {
                        if ($authorPhoto) Storage::disk('public')->delete($authorPhoto);
                        $authorPhoto = PublicStoragePublisher::storeAndPublish($request->file("testimonials.{$i}.author_photo"), 'landing/testimonials');
                    }

                    $testis[] = [
                        'title_ar'     => $request->input("testimonials.{$i}.title_ar", ''),
                        'title_en'     => $request->input("testimonials.{$i}.title_en", ''),
                        'text_ar'      => $request->input("testimonials.{$i}.text_ar", ''),
                        'text_en'      => $request->input("testimonials.{$i}.text_en", ''),
                        'author_name'  => $name,
                        'role_ar'      => $request->input("testimonials.{$i}.role_ar", ''),
                        'role_en'      => $request->input("testimonials.{$i}.role_en", ''),
                        'author_photo' => $authorPhoto,
                    ];
                }
                if (!empty($testis)) {
                    Setting::set('landing_testimonials', json_encode($testis, JSON_UNESCAPED_UNICODE));
                }
                if ($request->hasFile('testimonial_img')) {
                    $old = Setting::get('landing_testimonial_img');
                    if ($old) Storage::disk('public')->delete($old);
                    Setting::set('landing_testimonial_img', PublicStoragePublisher::storeAndPublish($request->file('testimonial_img'), 'landing'));
                }
            }

            if ($section === 'hours') {
                $raw = $request->input('working_hours', '{}');
                $decoded = json_decode($raw, true);
                Setting::set('landing_working_hours', json_encode($decoded ?: new \stdClass()));
                return back()->with('success', 'تم حفظ ساعات العمل')->with('_landing_sec', 'hours');
            }

            if ($section === 'footer') {
                Setting::setMany([
                    'landing_footer_address_ar'   => $request->footer_address_ar ?? '',
                    'landing_footer_address_en'   => $request->footer_address_en ?? '',
                    'landing_footer_phone'        => $request->footer_phone ?? '',
                    'landing_footer_email'        => $request->footer_email ?? '',
                    'landing_footer_copyright_ar' => $request->footer_copyright_ar ?? '',
                    'landing_footer_copyright_en' => $request->footer_copyright_en ?? '',
                ]);
            }

            if ($section === 'instagram') {
                Setting::setMany([
                    'landing_instagram_btn_ar' => $request->instagram_btn_ar ?? 'تابعنا على إنستجرام',
                    'landing_instagram_btn_en' => $request->instagram_btn_en ?? 'Follow Us On Instagram',
                ]);
                foreach (range(1, 5) as $n) {
                    Setting::set("landing_instagram_link_{$n}", $request->input("instagram_link_{$n}", ''));
                    if ($request->hasFile("instagram_img_{$n}")) {
                        $old = Setting::get("landing_instagram_img_{$n}");
                        if ($old) Storage::disk('public')->delete($old);
                        Setting::set("landing_instagram_img_{$n}", PublicStoragePublisher::storeAndPublish($request->file("instagram_img_{$n}"), 'landing/instagram'));
                    }
                }
            }

            if ($section === 'blog') {
                Setting::setMany([
                    'landing_blog_subtitle_ar' => $request->blog_subtitle_ar ?? 'أخبار ومقالات',
                    'landing_blog_subtitle_en' => $request->blog_subtitle_en ?? 'News & Blogs',
                    'landing_blog_title_ar'    => $request->blog_title_ar ?? 'أحدث مقالاتنا',
                    'landing_blog_title_en'    => $request->blog_title_en ?? 'Our Recent Articles',
                    'landing_blog_btn_ar'      => $request->blog_btn_ar ?? 'عرض كل المقالات',
                    'landing_blog_btn_en'      => $request->blog_btn_en ?? 'See All Posts',
                ]);
                foreach (range(1, 3) as $n) {
                    Setting::set("landing_blog_card_{$n}_title_ar", $request->input("blog_card_{$n}_title_ar", ''));
                    Setting::set("landing_blog_card_{$n}_title_en", $request->input("blog_card_{$n}_title_en", ''));
                    Setting::set("landing_blog_card_{$n}_tags_ar",  $request->input("blog_card_{$n}_tags_ar", ''));
                    Setting::set("landing_blog_card_{$n}_tags_en",  $request->input("blog_card_{$n}_tags_en", ''));
                    Setting::set("landing_blog_card_{$n}_author",   $request->input("blog_card_{$n}_author", 'admin'));
                    Setting::set("landing_blog_card_{$n}_date",     $request->input("blog_card_{$n}_date", ''));
                    Setting::set("landing_blog_card_{$n}_link",     $request->input("blog_card_{$n}_link", ''));
                    if ($request->hasFile("blog_card_{$n}_image")) {
                        $old = Setting::get("landing_blog_card_{$n}_image");
                        if ($old) Storage::disk('public')->delete($old);
                        Setting::set("landing_blog_card_{$n}_image", PublicStoragePublisher::storeAndPublish($request->file("blog_card_{$n}_image"), 'landing/blog'));
                    }
                }
            }

            return back()
                ->with('success', 'تم حفظ إعدادات الموقع الخارجي')
                ->with('_site_tab', 'landing')
                ->with('_landing_sec', $section);
        }

        if ($tab === 'integrations') {
            $section = $request->input('section', 'whatsapp');

            if ($section === 'whatsapp') {
                $request->validate([
                    'wa_token'       => 'nullable|string|max:500',
                    'wa_phone_id'    => 'nullable|string|max:100',
                    'wa_business_id' => 'nullable|string|max:100',
                ]);
                $wa_token       = $request->input('wa_token', '');
                $wa_phone_id    = $request->input('wa_phone_id', '');
                $wa_business_id = $request->input('wa_business_id', '');
                Setting::setMany([
                    'wa_token'       => $wa_token,
                    'wa_phone_id'    => $wa_phone_id,
                    'wa_business_id' => $wa_business_id,
                ]);
                $this->updateEnv([
                    'WHATSAPP_TOKEN'       => $wa_token,
                    'WHATSAPP_PHONE_ID'    => $wa_phone_id,
                    'WHATSAPP_BUSINESS_ID' => $wa_business_id,
                ]);
                return back()->with('success', 'تم حفظ إعدادات واتساب بنجاح')->with('_site_tab', 'integrations');
            }

            if ($section === 'mail') {
                $request->validate([
                    'mail_host'         => 'nullable|string|max:100',
                    'mail_port'         => 'nullable|integer',
                    'mail_encryption'   => 'nullable|in:tls,ssl,none',
                    'mail_username'     => 'nullable|string|max:200',
                    'mail_password'     => 'nullable|string|max:500',
                    'mail_from_address' => 'nullable|email|max:200',
                    'mail_from_name'    => 'nullable|string|max:100',
                ]);
                $mail_host = $request->input('mail_host', 'smtp.gmail.com');
                $mail_port = $request->input('mail_port', '587');
                $mail_enc  = $request->input('mail_encryption', 'tls');
                $mail_user = $request->input('mail_username', '');
                $mail_pass = $request->input('mail_password', '');
                $mail_from = $request->input('mail_from_address', '');
                $mail_name = $request->input('mail_from_name', 'Pet Clinic');
                Setting::setMany([
                    'mail_host'         => $mail_host,
                    'mail_port'         => $mail_port,
                    'mail_encryption'   => $mail_enc,
                    'mail_username'     => $mail_user,
                    'mail_password'     => $mail_pass,
                    'mail_from_address' => $mail_from,
                    'mail_from_name'    => $mail_name,
                ]);
                $this->updateEnv([
                    'MAIL_MAILER'       => 'smtp',
                    'MAIL_HOST'         => $mail_host,
                    'MAIL_PORT'         => $mail_port,
                    'MAIL_ENCRYPTION'   => $mail_enc,
                    'MAIL_USERNAME'     => $mail_user,
                    'MAIL_PASSWORD'     => $mail_pass,
                    'MAIL_FROM_ADDRESS' => $mail_from,
                    'MAIL_FROM_NAME'    => $mail_name,
                ]);
                return back()->with('success', 'تم حفظ إعدادات البريد الإلكتروني بنجاح')->with('_site_tab', 'integrations');
            }

            if ($section === 'zoom') {
                $request->validate([
                    'zoom_account_id'    => 'nullable|string|max:255',
                    'zoom_client_id'     => 'nullable|string|max:255',
                    'zoom_client_secret' => 'nullable|string|max:500',
                ]);
                Setting::setMany([
                    'zoom_account_id'    => $request->input('zoom_account_id', ''),
                    'zoom_client_id'     => $request->input('zoom_client_id', ''),
                    'zoom_client_secret' => $request->input('zoom_client_secret', ''),
                ]);

                return back()->with('success', 'تم حفظ إعدادات زوم بنجاح')->with('_site_tab', 'integrations');
            }

            return back()->with('success', 'تم الحفظ')->with('_site_tab', 'integrations');
        }

        if ($tab === 'domain') {
            $request->validate([
                'custom_domain' => [
                    'required', 'string', 'max:255',
                    'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
                ],
            ], [
                'custom_domain.required' => 'يرجى إدخال اسم النطاق',
                'custom_domain.regex'    => 'صيغة النطاق غير صحيحة (مثال: clinic.example.com)',
            ]);

            $domain  = strtolower(trim($request->custom_domain));
            $company = app()->bound('current_company') ? app('current_company') : null;

            if ($company) {
                // شركة — حفظ في جدول companies
                $company->update([
                    'custom_domain'              => $domain,
                    'custom_domain_status'       => 'pending',
                    'custom_domain_requested_at' => now(),
                ]);
                $senderName = $company->display_name;

                app(\App\Services\FaunaArchiveSyncService::class)->syncDomain($company->fresh());
            } else {
                // أدمن — حفظ في جدول settings
                Setting::set('custom_domain', $domain);
                Setting::set('custom_domain_status', 'pending');
                $senderName = 'الأدمن';
            }

            // إشعار للأدمن (فقط لو طلبت من شركة)
            if ($company) {
                $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
                foreach ($admins as $admin) {
                    \App\Models\AppNotification::notify(
                        $admin->id,
                        'طلب ربط نطاق جديد',
                        'الشركة "' . $senderName . '" تطلب ربط النطاق: ' . $domain,
                        rtrim(config('services.fauna_archive.url', env('FAUNA_ARCHIVE_URL', 'http://127.0.0.1:8001')), '/') . '/domain-requests',
                        'fa-globe',
                        'info'
                    );
                }
            }

            return back()
                ->with('success', 'تم إرسال طلب ربط النطاق، سيتواصل معك الفريق قريباً')
                ->with('_site_tab', 'landing')
                ->with('_landing_sec', 'domain');
        }

        return back()->with('success', 'تم الحفظ');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        $host     = Setting::get('mail_host',         env('MAIL_HOST', 'smtp.gmail.com'));
        $port     = Setting::get('mail_port',         env('MAIL_PORT', '587'));
        $enc      = Setting::get('mail_encryption',   env('MAIL_ENCRYPTION', 'tls'));
        $user     = Setting::get('mail_username',     env('MAIL_USERNAME', ''));
        $pass     = Setting::get('mail_password',     env('MAIL_PASSWORD', ''));
        $fromAddr = Setting::get('mail_from_address', env('MAIL_FROM_ADDRESS', $user));
        $fromName = Setting::get('mail_from_name',    env('MAIL_FROM_NAME', 'Pet Clinic'));

        if (!$host || !$user || !$pass) {
            return back()->with('error', 'يرجى حفظ بيانات البريد الإلكتروني أولاً قبل الإرسال التجريبي');
        }

        config([
            'mail.mailers.smtp.host'       => $host,
            'mail.mailers.smtp.port'       => $port,
            'mail.mailers.smtp.encryption' => $enc === 'none' ? null : $enc,
            'mail.mailers.smtp.username'   => $user,
            'mail.mailers.smtp.password'   => $pass,
            'mail.from.address'            => $fromAddr ?: $user,
            'mail.from.name'               => $fromName,
        ]);

        try {
            Mail::raw('هذه رسالة تجريبية من نظام Pet Clinic للتأكد من صحة إعدادات البريد الإلكتروني.', function ($msg) use ($request, $fromAddr, $fromName, $user) {
                $msg->to($request->test_email)
                    ->from($fromAddr ?: $user, $fromName)
                    ->subject('رسالة تجريبية — Pet Clinic');
            });

            return back()->with('success', 'تم إرسال الرسالة التجريبية بنجاح إلى ' . $request->test_email)->with('_site_tab', 'integrations');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل الإرسال: ' . $e->getMessage())->with('_site_tab', 'integrations');
        }
    }

    private function updateEnv(array $data): void
    {
        $envPath    = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($data as $key => $value) {
            $value   = (string) $value;
            $escaped = str_contains($value, ' ') || $value === '' ? '"' . addslashes($value) . '"' : $value;

            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $envContent);
            } else {
                $envContent .= "\n{$key}={$escaped}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    public function deleteLandingImage(Request $request)
    {
        $allowed = [
            'landing_logo_colored'  => 'general',
            'landing_logo_white'    => 'general',
            'landing_banner_bg'     => 'banner',
            'landing_banner_img'    => 'banner',
            'landing_about_img'     => 'about',
            'landing_why_img'        => 'why',
            'landing_counter_img'    => 'counter',
            'landing_testimonial_img'=> 'testimonials',
        ];
        foreach (range(1, 8) as $n) {
            $allowed["landing_brand_logo_{$n}"] = 'brands';
        }
        foreach (range(1, 5) as $n) {
            $allowed["landing_instagram_img_{$n}"] = 'instagram';
        }
        foreach (range(1, 3) as $n) {
            $allowed["landing_blog_card_{$n}_image"] = 'blog';
        }

        $key = $request->input('key');

        if (!array_key_exists($key, $allowed)) {
            return back()->with('error', 'مفتاح غير صالح');
        }

        $path = Setting::get($key);
        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::set($key, null);
        }

        return back()
            ->with('success', 'تم حذف الصورة')
            ->with('_site_tab', 'landing')
            ->with('_landing_sec', $allowed[$key]);
    }
}

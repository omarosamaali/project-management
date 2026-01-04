<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name_ar' => 'موقع ويب',
                'name_en' => 'Web Application',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'تطبيق موبايل',
                'name_en' => 'Mobile App',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'كلاهما',
                'name_en' => 'Web & Mobile',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'تصميم شعار',
                'name_en' => 'Logo',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'تصميم هوية مؤسسة',
                'name_en' => 'Business Identity',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'تسويق إلكتروني',
                'name_en' => 'Digital Marketing',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'إدارة موقع او تطبيق',
                'name_en' => 'Website Management',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'إدارة حساب التواصل الاجتماعي',
                'name_en' => 'Social Media Management',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'دورة تدريبية',
                'name_en' => 'Training',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'استشارات تقنية',
                'name_en' => 'Technical Consulting',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'تطبيقات سطح المكتب',
                'name_en' => 'Desktop App',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
            [
                'name_ar' => 'طلب اخر',
                'name_en' => 'Other',
                'status' => 'active',
                'image' => 'services/fmOyPPYGGU0UK1elDBOLbx6a7JyIMML0wJAI3BZs.png',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SpecialRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SpecialRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. تعريف بيانات العملاء
        $clientsData = [
            [
                'name' => 'أبي بكر',
                'email' => 'abobakr@gmail.com',
                'phone' => '9901900',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'سيد مصطفي',
                'email' => 'sayed@gmail.com',
                'phone' => '01098765432',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'طلحة حامد',
                'email' => 'talha@gmail.com',
                'phone' => '01123456789',
                'role' => 'client',
                'password' => Hash::make('password'),
            ],
        ];

        $clientIds = [];

        // 2. إنشاء العملاء وحفظ IDs الخاصة بهم
        foreach ($clientsData as $client) {
            $user = User::firstOrCreate(['email' => $client['email']], $client);
            $clientIds[] = $user->id;
        }

        // 3. إنشاء بيانات المشاريع الخاصة
        $requestsData = [
            // ===================================
            // طلبات العميل الأول (أبي بكر) - 3 طلبات
            // ===================================
            [
                'order_number' => 'REQ' . time() . rand(1, 9),
                'user_id' => $clientIds[0],
                'title' => 'منصة متكاملة للتجارة الإلكترونية',
                'project_type' => 'موقع ويب',
                'description' => 'موقع ويب لبيع منتجات يدوية، يتطلب نظام دفع، لوحة تحكم للمنتجات، ونظام شحن.',
                'core_features' => 'إدارة المخزون، بوابات دفع (Stripe/PayPal)، نظام تقييم العملاء.',
                'examples' => 'https://example.com/ecommerce-inspiration',
                'budget' => '15000',
                'deadline' => Carbon::now()->addMonths(3)->toDateString(),
                'status' => 'in_progress',
            ],
            [                'order_number' => 'REQ' . time() . rand(1, 9),

                'user_id' => $clientIds[0],
                'title' => 'تصميم شعار جديد للشركة',
                'project_type' => 'تطبيق موبايل',
                'description' => 'شعار عصري وبسيط لشركة ناشئة تعمل في مجال الذكاء الاصطناعي.',
                'core_features' => 'ثلاثة نماذج أولية، تسليم ملفات المصدر (Vector).',
                'examples' => 'لا يوجد',
                'budget' => '15000',
                'deadline' => Carbon::now()->addWeeks(2)->toDateString(),
                'status' => 'pending',
            ],
            [                'order_number' => 'REQ' . time() . rand(1, 9),

                'user_id' => $clientIds[0],
                'title' => 'استشارات تقنية لتحسين الأداء',
                'project_type' => 'كلاهما',
                'description' => 'جلسة استشارية لمدة 4 ساعات حول تحسين أداء قواعد البيانات في نظامنا الحالي.',
                'core_features' => 'تحليل الاستعلامات البطيئة، تقديم خطة تحسين مفصلة.',
                'examples' => null,
                'budget' => '15000',
                'deadline' => Carbon::now()->addDays(10)->toDateString(),
                'status' => 'completed',
            ],

            // ===================================
            // طلبات العميل الثاني (سيد مصطفي) - طلبين
            // ===================================
            [                'order_number' => 'REQ' . time() . rand(1, 9),

                'user_id' => $clientIds[1],
                'title' => 'تطبيق موبايل لتتبع اللياقة البدنية',
                'project_type' => 'كلاهما',
                'description' => 'تطبيق Android و iOS لتسجيل التمارين اليومية واحتساب السعرات الحرارية المحروقة.',
                'core_features' => 'التكامل مع Google Fit و Apple Health، إشعارات يومية، نظام مكافآت.',
                'examples' => 'https://example.com/fitness-app',
                'budget' => '15000',
                'deadline' => Carbon::now()->addMonths(5)->toDateString(),
                'status' => 'in_review',
            ],
            [                'order_number' => 'REQ' . time() . rand(1, 9),

                'user_id' => $clientIds[1],
                'title' => 'إدارة حسابات التواصل الاجتماعي (تويتر وفيسبوك)',
                'project_type' => 'كلاهما',
                'description' => 'إدارة شاملة لمدة شهرين، تشمل تصميم المحتوى وجدولة المنشورات والرد على التعليقات.',
                'core_features' => '30 منشور شهري، تقارير أسبوعية، حملة إعلانية واحدة.',
                'examples' => null,
                'budget' => '15000',
                'deadline' => null, // مستمر
                'status' => 'in_progress',
            ],

            // ===================================
            // طلبات العميل الثالث (طلحة حامد) - طلب واحد
            // ===================================
            [                'order_number' => 'REQ' . time() . rand(1, 9),

                'user_id' => $clientIds[2],
                'title' => 'نظام داخلي لإدارة الموارد البشرية (HRMS)',
                'project_type' => 'تصميم شعار',
                'description' => 'نظام داخلي لإدارة إجازات الموظفين وسجلات الحضور والانصراف.',
                'core_features' => 'تكامل بصمة الأصبع، إرسال تقارير تلقائية للمدير.',
                'examples' => null,
                'budget' => '15000',
                'deadline' => Carbon::now()->addMonths(2)->toDateString(),
                'status' => 'pending',
            ],
        ];

        // 4. إدخال المشاريع إلى قاعدة البيانات
        foreach ($requestsData as $request) {
            SpecialRequest::create($request);
        }
    }
}

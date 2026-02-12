<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\System;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            [
                'name_ar' => 'نظام إدارة المبيعات',
                'name_en' => 'Sales Management System',
                'price' => 5000.00,
                'execution_days_from' => 15,
                'support_days' => 365,
                'execution_days_to' => 30,
                'description_ar' => 'نظام متكامل لإدارة المبيعات والفواتير مع تقارير تفصيلية وإدارة العملاء',
                'description_en' => 'Comprehensive sales and invoice management system with detailed reports and customer management',
                'requirements' => [
                    ['ar' => 'قاعدة بيانات MySQL', 'en' => 'MySQL Database'],
                    ['ar' => 'خادم PHP 8.1 أو أحدث', 'en' => 'PHP 8.1 or higher'],
                    ['ar' => 'مساحة تخزين 500 ميجا', 'en' => '500 MB storage space'],
                ],
                'features' => [
                    ['ar' => 'إدارة الفواتير والعروض', 'en' => 'Invoice and quotation management'],
                    ['ar' => 'تقارير مبيعات تفصيلية', 'en' => 'Detailed sales reports'],
                    ['ar' => 'إدارة العملاء والموردين', 'en' => 'Customer and supplier management'],
                    ['ar' => 'نظام صلاحيات متقدم', 'en' => 'Advanced permissions system'],
                ],
                // الأزرار الأصلية
                'buttons' => [
                    [
                        'text_ar' => 'طلب عرض تجريبي',
                        'text_en' => 'Request Demo',
                        'link' => 'https://example.com/sales-demo',
                        'color' => '#10B981' // زمردي
                    ],
                    [
                        'text_ar' => 'تفاصيل الأسعار',
                        'text_en' => 'Pricing Details',
                        'link' => 'https://example.com/sales-pricing',
                        'color' => '#F97316' // برتقالي
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500',
                    'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة المخزون',
                'name_en' => 'Inventory Management System',
                'price' => 4500.00,
                'support_days' => 365,
                'execution_days_from' => 10,
                'execution_days_to' => 20,
                'description_ar' => 'نظام ذكي لإدارة المخزون والمستودعات مع تنبيهات الحد الأدنى وتتبع الحركة',
                'description_en' => 'Smart inventory and warehouse management system with minimum alerts and movement tracking',
                'requirements' => [
                    ['ar' => 'نظام التشغيل Linux/Windows', 'en' => 'Linux/Windows OS'],
                    ['ar' => 'Laravel 10 أو أحدث', 'en' => 'Laravel 10 or higher'],
                    ['ar' => 'ذاكرة RAM 2GB كحد أدنى', 'en' => 'Minimum 2GB RAM'],
                ],
                'features' => [
                    ['ar' => 'إدارة المستودعات المتعددة', 'en' => 'Multiple warehouse management'],
                    ['ar' => 'تتبع حركة المخزون', 'en' => 'Inventory movement tracking'],
                    ['ar' => 'تنبيهات الحد الأدنى', 'en' => 'Minimum stock alerts'],
                    ['ar' => 'تقارير الجرد', 'en' => 'Inventory reports'],
                ],
                // أزرار جديدة
                'buttons' => [
                    [
                        'text_ar' => 'تفعيل النظام',
                        'text_en' => 'Activate System',
                        'link' => 'https://example.com/activate-inventory',
                        'color' => '#06B6D4' // سماوي
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1553413077-190dd305871c?w=500',
                    'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام الموارد البشرية',
                'name_en' => 'HR Management System',
                'price' => 6000.00,
                'execution_days_from' => 20,
                'execution_days_to' => 40,
                'support_days' => 365,
                'description_ar' => 'نظام شامل لإدارة الموارد البشرية والرواتب والحضور والانصراف',
                'description_en' => 'Comprehensive HR, payroll, attendance and leave management system',
                'requirements' => [
                    ['ar' => 'قاعدة بيانات PostgreSQL', 'en' => 'PostgreSQL Database'],
                    ['ar' => 'خادم ويب Apache/Nginx', 'en' => 'Apache/Nginx web server'],
                    ['ar' => 'شهادة SSL', 'en' => 'SSL Certificate'],
                ],
                'features' => [
                    ['ar' => 'إدارة بيانات الموظفين', 'en' => 'Employee data management'],
                    ['ar' => 'نظام الحضور والانصراف', 'en' => 'Attendance system'],
                    ['ar' => 'حساب الرواتب تلقائياً', 'en' => 'Automatic payroll calculation'],
                    ['ar' => 'إدارة الإجازات', 'en' => 'Leave management'],
                    ['ar' => 'تقييم الأداء', 'en' => 'Performance evaluation'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'طلب عرض سعر خاص',
                        'text_en' => 'Request Special Quote',
                        'link' => 'https://example.com/hr-quote',
                        'color' => '#7C3AED' // بنفسجي
                    ],
                    [
                        'text_ar' => 'مشاهدة فيديو تعريفي',
                        'text_en' => 'Watch Introduction Video',
                        'link' => 'https://youtube.com/hr-system-intro',
                        'color' => '#EF4444' // أحمر
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=500',
                    'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام نقاط البيع',
                'name_en' => 'Point of Sale System',
                'price' => 3500.00,
                'execution_days_from' => 7,
                'support_days' => 365,
                'execution_days_to' => 14,
                'description_ar' => 'نظام كاشير سريع وسهل الاستخدام مع دعم الطابعات والباركود',
                'description_en' => 'Fast and easy-to-use POS system with printer and barcode support',
                'requirements' => [
                    ['ar' => 'متصفح حديث Chrome/Firefox', 'en' => 'Modern browser Chrome/Firefox'],
                    ['ar' => 'طابعة حرارية', 'en' => 'Thermal printer'],
                    ['ar' => 'قارئ باركود', 'en' => 'Barcode scanner'],
                ],
                'features' => [
                    ['ar' => 'واجهة سريعة وسهلة', 'en' => 'Fast and easy interface'],
                    ['ar' => 'دعم الباركود', 'en' => 'Barcode support'],
                    ['ar' => 'طباعة الفواتير', 'en' => 'Invoice printing'],
                    ['ar' => 'تقارير المبيعات اليومية', 'en' => 'Daily sales reports'],
                ],
                // أزرار جديدة ومختلفة (زر واحد فقط)
                'buttons' => [
                    [
                        'text_ar' => 'تثبيت تطبيق الكاشير',
                        'text_en' => 'Install POS App',
                        'link' => 'https://example.com/download-pos',
                        'color' => '#3B82F6' // أزرق
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1487058792275-0ad4aaf24ca7?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800',
                    'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة المشاريع',
                'name_en' => 'Project Management System',
                'price' => 7000.00,
                'execution_days_from' => 25,
                'execution_days_to' => 45,
                'support_days' => 365,
                'description_ar' => 'نظام احترافي لإدارة المشاريع والمهام والفرق مع تقارير الإنجاز',
                'description_en' => 'Professional project, task and team management system with progress reports',
                'requirements' => [
                    ['ar' => 'خادم VPS مخصص', 'en' => 'Dedicated VPS server'],
                    ['ar' => 'قاعدة بيانات MySQL 8', 'en' => 'MySQL 8 Database'],
                    ['ar' => 'Redis للتخزين المؤقت', 'en' => 'Redis for caching'],
                ],
                'features' => [
                    ['ar' => 'إدارة المشاريع والمهام', 'en' => 'Project and task management'],
                    ['ar' => 'جدولة المهام Gantt Chart', 'en' => 'Task scheduling with Gantt Chart'],
                    ['ar' => 'تعاون الفريق', 'en' => 'Team collaboration'],
                    ['ar' => 'تتبع الوقت', 'en' => 'Time tracking'],
                    ['ar' => 'لوحة تحكم تفاعلية', 'en' => 'Interactive dashboard'],
                ],
                // أزرار جديدة ومختلفة (ثلاثة أزرار)
                'buttons' => [
                    [
                        'text_ar' => 'جرّب مجاناً لمدة 14 يوم',
                        'text_en' => 'Try Free for 14 Days',
                        'link' => 'https://example.com/project-trial',
                        'color' => '#EC4899' // وردي
                    ],
                    [
                        'text_ar' => 'قارن بين الباقات',
                        'text_en' => 'Compare Plans',
                        'link' => 'https://example.com/project-compare',
                        'color' => '#8B5CF6' // نيلي
                    ],
                    [
                        'text_ar' => 'تواصل مع الدعم',
                        'text_en' => 'Contact Support',
                        'link' => 'mailto:support@example.com',
                        'color' => '#64748B' // حجري
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1507925921958-8a62f3d1a50d?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=500',
                    'https://images.unsplash.com/photo-1542626991-cbc4e32524cc?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة المحتوى',
                'name_en' => 'Content Management System',
                'price' => 4000.00,
                'execution_days_from' => 12,
                'execution_days_to' => 25,
                'support_days' => 265,
                'description_ar' => 'نظام CMS مرن لإدارة المحتوى والمقالات والصفحات',
                'description_en' => 'Flexible CMS for content, articles and page management',
                'requirements' => [
                    ['ar' => 'PHP 8.2', 'en' => 'PHP 8.2'],
                    ['ar' => 'Composer', 'en' => 'Composer'],
                    ['ar' => 'Node.js للأصول', 'en' => 'Node.js for assets'],
                ],
                'features' => [
                    ['ar' => 'محرر نصوص متقدم', 'en' => 'Advanced text editor'],
                    ['ar' => 'إدارة الوسائط', 'en' => 'Media management'],
                    ['ar' => 'تحسين محركات البحث SEO', 'en' => 'SEO optimization'],
                    ['ar' => 'متعدد اللغات', 'en' => 'Multi-language support'],
                ],
                // أزرار جديدة ومختلفة (زر واحد فقط للنظام غير النشط)
                'buttons' => [
                    [
                        'text_ar' => 'قريباً: اشترك لتصلك التحديثات',
                        'text_en' => 'Coming Soon: Subscribe for Updates',
                        'link' => 'https://example.com/cms-updates',
                        'color' => '#FBBF24' // كهرماني
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=500',
                    'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=500',
                ],
                'status' => 'inactive',
            ],
            [
                'name_ar' => 'نظام إدارة المطاعم',
                'name_en' => 'Restaurant Management System',
                'price' => 5500.00,
                'execution_days_from' => 18,
                'support_days' => 365,
                'execution_days_to' => 35,
                'description_ar' => 'نظام شامل لإدارة المطاعم والمشاريع والمخزون والتوصيل',
                'description_en' => 'Comprehensive restaurant, order, inventory and delivery management system',
                'requirements' => [
                    ['ar' => 'قاعدة بيانات MySQL', 'en' => 'MySQL Database'],
                    ['ar' => 'خادم PHP 8.0 أو أحدث', 'en' => 'PHP 8.0 or higher'],
                    ['ar' => 'طابعة حرارية للمطبخ', 'en' => 'Thermal printer for kitchen'],
                ],
                'features' => [
                    ['ar' => 'إدارة المشاريع والطاولات', 'en' => 'Order and table management'],
                    ['ar' => 'قائمة طعام رقمية', 'en' => 'Digital menu'],
                    ['ar' => 'نظام التوصيل', 'en' => 'Delivery system'],
                    ['ar' => 'تقارير المبيعات والأرباح', 'en' => 'Sales and profit reports'],
                    ['ar' => 'إدارة المخزون والمكونات', 'en' => 'Inventory and ingredient management'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'شاهد واجهة المستخدم',
                        'text_en' => 'View User Interface',
                        'link' => 'https://example.com/restaurant-ui',
                        'color' => '#14B8A6' // تيل
                    ],
                    [
                        'text_ar' => 'ابدأ مشروعك الآن',
                        'text_en' => 'Start Your Project Now',
                        'link' => 'https://example.com/start-restaurant-system',
                        'color' => '#F43F5E' // أحمر وردي
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=500',
                    'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة العيادات',
                'name_en' => 'Clinic Management System',
                'price' => 6500.00,
                'execution_days_from' => 22,
                'execution_days_to' => 40,
                'support_days' => 365,
                'description_ar' => 'نظام طبي متكامل لإدارة العيادات والمواعيد والملفات الطبية',
                'description_en' => 'Integrated medical system for clinic, appointment and medical record management',
                'requirements' => [
                    ['ar' => 'قاعدة بيانات آمنة MySQL', 'en' => 'Secure MySQL Database'],
                    ['ar' => 'شهادة SSL إلزامية', 'en' => 'Mandatory SSL Certificate'],
                    ['ar' => 'نظام نسخ احتياطي يومي', 'en' => 'Daily backup system'],
                ],
                'features' => [
                    ['ar' => 'إدارة المرضى والملفات الطبية', 'en' => 'Patient and medical record management'],
                    ['ar' => 'نظام حجز المواعيد', 'en' => 'Appointment booking system'],
                    ['ar' => 'الوصفات الطبية الإلكترونية', 'en' => 'Electronic prescriptions'],
                    ['ar' => 'إدارة الفواتير والتأمين', 'en' => 'Billing and insurance management'],
                    ['ar' => 'تقارير طبية وإحصائية', 'en' => 'Medical and statistical reports'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'اطلب نسخة تجريبية مجانية',
                        'text_en' => 'Request Free Trial',
                        'link' => 'https://example.com/clinic-trial',
                        'color' => '#059669' // أخضر داكن
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500',
                    'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة المدارس',
                'name_en' => 'School Management System',
                'price' => 7500.00,
                'execution_days_from' => 30,
                'execution_days_to' => 50,
                'support_days' => 365,
                'description_ar' => 'نظام تعليمي شامل لإدارة المدارس والطلاب والمعلمين والدرجات',
                'description_en' => 'Comprehensive educational system for school, student, teacher and grade management',
                'requirements' => [
                    ['ar' => 'خادم مخصص VPS', 'en' => 'Dedicated VPS server'],
                    ['ar' => 'قاعدة بيانات MySQL 8', 'en' => 'MySQL 8 Database'],
                    ['ar' => 'مساحة تخزين 5GB', 'en' => '5GB storage space'],
                ],
                'features' => [
                    ['ar' => 'إدارة الطلاب والمعلمين', 'en' => 'Student and teacher management'],
                    ['ar' => 'نظام الحضور والغياب', 'en' => 'Attendance tracking system'],
                    ['ar' => 'إدارة الدرجات والشهادات', 'en' => 'Grade and certificate management'],
                    ['ar' => 'جدول الحصص الإلكتروني', 'en' => 'Electronic timetable'],
                    ['ar' => 'بوابة أولياء الأمور', 'en' => 'Parent portal'],
                    ['ar' => 'إدارة المكتبة', 'en' => 'Library management'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'طلب اجتماع استشاري',
                        'text_en' => 'Request Consultation Meeting',
                        'link' => 'https://example.com/school-consultation',
                        'color' => '#DB2777' // فوشيا
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=500',
                    'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام التجارة الإلكترونية',
                'name_en' => 'E-Commerce System',
                'price' => 8000.00,
                'execution_days_from' => 28,
                'support_days' => 365,
                'execution_days_to' => 45,
                'description_ar' => 'متجر إلكتروني احترافي مع بوابات دفع متعددة وإدارة شاملة',
                'description_en' => 'Professional online store with multiple payment gateways and comprehensive management',
                'requirements' => [
                    ['ar' => 'خادم VPS قوي', 'en' => 'Powerful VPS server'],
                    ['ar' => 'شهادة SSL إلزامية', 'en' => 'Mandatory SSL Certificate'],
                    ['ar' => 'Laravel 10 أو أحدث', 'en' => 'Laravel 10 or higher'],
                    ['ar' => 'Redis للأداء', 'en' => 'Redis for performance'],
                ],
                'features' => [
                    ['ar' => 'واجهة متجر احترافية', 'en' => 'Professional store interface'],
                    ['ar' => 'إدارة المنتجات والفئات', 'en' => 'Product and category management'],
                    ['ar' => 'بوابات دفع متعددة', 'en' => 'Multiple payment gateways'],
                    ['ar' => 'نظام الشحن والتوصيل', 'en' => 'Shipping and delivery system'],
                    ['ar' => 'إدارة المشاريع والمرتجعات', 'en' => 'Order and return management'],
                    ['ar' => 'كوبونات وعروض خاصة', 'en' => 'Coupons and special offers'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'ابدأ بإنشاء متجرك',
                        'text_en' => 'Start Building Your Store',
                        'link' => 'https://example.com/start-ecommerce',
                        'color' => '#EC4899' // وردي
                    ],
                    [
                        'text_ar' => 'شاهد المعرض (Portfolio)',
                        'text_en' => 'View Portfolio',
                        'link' => 'https://example.com/ecommerce-portfolio',
                        'color' => '#475569' // أردوازي
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1557821552-17105176677c?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=500',
                    'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة الفنادق',
                'name_en' => 'Hotel Management System',
                'price' => 9000.00,
                'execution_days_from' => 35,
                'support_days' => 365,
                'execution_days_to' => 55,
                'description_ar' => 'نظام متكامل لإدارة الفنادق والحجوزات والغرف والخدمات',
                'description_en' => 'Integrated system for hotel, reservation, room and service management',
                'requirements' => [
                    ['ar' => 'خادم Cloud مخصص', 'en' => 'Dedicated Cloud server'],
                    ['ar' => 'قاعدة بيانات PostgreSQL', 'en' => 'PostgreSQL Database'],
                    ['ar' => 'نظام نسخ احتياطي تلقائي', 'en' => 'Automatic backup system'],
                ],
                'features' => [
                    ['ar' => 'نظام الحجز الإلكتروني', 'en' => 'Online booking system'],
                    ['ar' => 'إدارة الغرف والأجنحة', 'en' => 'Room and suite management'],
                    ['ar' => 'نظام تسجيل الدخول والخروج', 'en' => 'Check-in and check-out system'],
                    ['ar' => 'إدارة خدمات الفندق', 'en' => 'Hotel service management'],
                    ['ar' => 'نظام الفواتير والمدفوعات', 'en' => 'Billing and payment system'],
                    ['ar' => 'تقارير الإشغال والإيرادات', 'en' => 'Occupancy and revenue reports'],
                ],
                // أزرار جديدة ومختلفة (ثلاثة أزرار)
                'buttons' => [
                    [
                        'text_ar' => 'شاهد صفحة الحجز',
                        'text_en' => 'View Booking Page',
                        'link' => 'https://example.com/hotel-booking-page',
                        'color' => '#1D4ED8' // أزرق داكن
                    ],
                    [
                        'text_ar' => 'طلب اجتماع توضيحي',
                        'text_en' => 'Request Explanation Meeting',
                        'link' => 'https://example.com/hotel-meeting',
                        'color' => '#0F766E' // تركواز داكن
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=500',
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=500',
                ],
                'status' => 'active',
            ],
            [
                'name_ar' => 'نظام إدارة الصيانة',
                'name_en' => 'Maintenance Management System',
                'price' => 4800.00,
                'execution_days_from' => 14,
                'support_days' => 365,
                'execution_days_to' => 28,
                'description_ar' => 'نظام ذكي لإدارة طلبات الصيانة والفنيين والمعدات',
                'description_en' => 'Smart system for maintenance request, technician and equipment management',
                'requirements' => [
                    ['ar' => 'قاعدة بيانات MySQL', 'en' => 'MySQL Database'],
                    ['ar' => 'PHP 8.1 أو أحدث', 'en' => 'PHP 8.1 or higher'],
                    ['ar' => 'تطبيق موبايل للفنيين', 'en' => 'Mobile app for technicians'],
                ],
                'features' => [
                    ['ar' => 'إدارة طلبات الصيانة', 'en' => 'Maintenance request management'],
                    ['ar' => 'تتبع الفنيين وتوزيع المهام', 'en' => 'Technician tracking and task distribution'],
                    ['ar' => 'إدارة قطع الغيار', 'en' => 'Spare parts management'],
                    ['ar' => 'الصيانة الوقائية المجدولة', 'en' => 'Scheduled preventive maintenance'],
                    ['ar' => 'تقارير الأداء والتكاليف', 'en' => 'Performance and cost reports'],
                ],
                // أزرار جديدة ومختلفة
                'buttons' => [
                    [
                        'text_ar' => 'تنزيل تطبيق الفنيين',
                        'text_en' => 'Download Technician App',
                        'link' => 'https://example.com/download-maintenance-app',
                        'color' => '#0F928C' // زمردي داكن
                    ],
                    [
                        'text_ar' => 'تواصل مع الدعم الفني',
                        'text_en' => 'Contact Technical Support',
                        'link' => 'tel:+966555123456',
                        'color' => '#CA8A04' // زيتوني
                    ],
                ],
                'main_image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=500',
                'images' => [
                    'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=500',
                    'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?w=500',
                ],
                'status' => 'active',
            ],
        ];

        foreach ($systems as $system) {
            System::create($system);
        }
    }
}

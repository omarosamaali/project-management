<?php

/**
 * Static course-rating surveys, keyed by location_type.
 *
 * target  = where the score feeds: trainer | course | academy
 * type    = scale (1-5) | boolean (yes/no) | text
 * section = visual grouping header (Arabic)
 */

return [

    /*
    |--------------------------------------------------------------------------
    | حضوري  (on_site)
    |--------------------------------------------------------------------------
    */
    'on_site' => [
        // أولاً: تقييم المحاضر
        ['id' => 'trainer_knowledge',        'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى تمكن المحاضر من المادة العلمية',
            'label_en' => 'Instructor\'s command of the subject matter'],
        ['id' => 'trainer_clarity',          'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'وضوح الشرح وتوصيل الأفكار بسلاسة',
            'label_en' => 'Clarity of explanation and smooth delivery of ideas'],
        ['id' => 'trainer_engagement',       'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'القدرة على تحفيز التفاعل والإجابة على الاستفسارات',
            'label_en' => 'Ability to encourage interaction and answer questions'],

        // ثانياً: تقييم المادة العلمية والعرض التقديمي
        ['id' => 'content_quality',          'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة المعلومات ومدى حداثتها',
            'label_en' => 'Quality and currency of information'],
        ['id' => 'presentation_clarity',     'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'وضوح وجاذبية العرض التقديمي',
            'label_en' => 'Clarity and appeal of the presentation'],
        ['id' => 'practical_benefit',        'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى الاستفادة من التطبيقات العملية',
            'label_en' => 'Benefit gained from practical exercises'],

        // ثالثاً: تقييم الوقت والمكان
        ['id' => 'duration_fit',             'section' => 'تقييم الوقت والمكان', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'ملاءمة مدة الورشة',
            'label_en' => 'Suitability of the workshop duration'],
        ['id' => 'venue_quality',            'section' => 'تقييم الوقت والمكان', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة وتجهيزات مكان إقامة الورشة',
            'label_en' => 'Quality and equipment of the workshop venue'],

        // أسئلة عامة
        ['id' => 'recommend',               'section' => 'أسئلة عامة', 'type' => 'boolean', 'target' => 'course', 'required' => true,
            'label_ar' => 'هل تنصح زملاءك أو أشخاصاً آخرين بحضور هذه الدورة؟',
            'label_en' => 'Would you recommend this course to colleagues or others?'],
        ['id' => 'best_part',               'section' => 'أسئلة عامة', 'type' => 'text', 'target' => 'course', 'required' => true,
            'label_ar' => 'ما هي أكثر نقطة أو محور نال إعجابك في الورشة؟',
            'label_en' => 'What was the most impressive aspect of the workshop?'],
        ['id' => 'suggestions',             'section' => 'أسئلة عامة', 'type' => 'text', 'target' => 'academy', 'required' => true,
            'label_ar' => 'هل لديك أي مقترحات إضافية لتطوير الورش والدورات القادمة؟',
            'label_en' => 'Do you have any suggestions for improving future workshops and courses?'],
    ],

    /*
    |--------------------------------------------------------------------------
    | أونلاين  (online)
    |--------------------------------------------------------------------------
    */
    'online' => [
        // أولاً: تقييم المحاضر
        ['id' => 'trainer_knowledge',        'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى تمكن المحاضر من المادة العلمية',
            'label_en' => 'Instructor\'s command of the subject matter'],
        ['id' => 'trainer_clarity',          'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'وضوح الشرح وتوصيل الأفكار بسلاسة عبر البث',
            'label_en' => 'Clarity of explanation and smooth delivery over broadcast'],
        ['id' => 'trainer_engagement',       'section' => 'تقييم المحاضر', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'القدرة على إدارة الحوار وتحفيز المشاركة الرقمية',
            'label_en' => 'Ability to manage discussion and encourage digital participation'],

        // ثانياً: تقييم المادة العلمية والعرض التقديمي
        ['id' => 'content_quality',          'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة المحتوى العلمي ومدى حداثته',
            'label_en' => 'Quality and currency of the scientific content'],
        ['id' => 'presentation_clarity',     'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'وضوح وجاذبية العرض التقديمي والوسائط المستخدمة',
            'label_en' => 'Clarity and appeal of the presentation and media used'],
        ['id' => 'practical_benefit',        'section' => 'تقييم المادة العلمية والعرض التقديمي', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى الاستفادة من التطبيقات أو التدريبات العملية',
            'label_en' => 'Benefit gained from practical exercises or drills'],

        // ثالثاً: تقييم البيئة الرقمية والتنظيم
        ['id' => 'duration_fit',             'section' => 'تقييم البيئة الرقمية والتنظيم', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'ملاءمة مدة الورشة والتوقيت',
            'label_en' => 'Suitability of the workshop duration and timing'],
        ['id' => 'stream_quality',           'section' => 'تقييم البيئة الرقمية والتنظيم', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة الصوت والصورة ووضوح البث',
            'label_en' => 'Audio/video quality and broadcast clarity'],
        ['id' => 'platform_ease',            'section' => 'تقييم البيئة الرقمية والتنظيم', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'سهولة استخدام المنصة والدخول للقاعة الافتراضية',
            'label_en' => 'Ease of platform use and accessing the virtual room'],

        // أسئلة عامة
        ['id' => 'recommend',               'section' => 'أسئلة عامة', 'type' => 'boolean', 'target' => 'course', 'required' => true,
            'label_ar' => 'هل تنصح زملاءك أو أشخاصاً آخرين بحضور هذه الدورة؟',
            'label_en' => 'Would you recommend this course to colleagues or others?'],
        ['id' => 'best_part',               'section' => 'أسئلة عامة', 'type' => 'text', 'target' => 'course', 'required' => true,
            'label_ar' => 'ما هي أكثر نقطة أو محور نال إعجابك في الورشة؟',
            'label_en' => 'What was the most impressive aspect of the workshop?'],
        ['id' => 'suggestions',             'section' => 'أسئلة عامة', 'type' => 'text', 'target' => 'academy', 'required' => true,
            'label_ar' => 'هل لديك أي مقترحات إضافية لتطوير الورش والدورات القادمة؟',
            'label_en' => 'Do you have any suggestions for improving future workshops and courses?'],
    ],

    /*
    |--------------------------------------------------------------------------
    | مسجلة  (recorded)
    |--------------------------------------------------------------------------
    */
    'recorded' => [
        // أولاً: تقييم المحاضر وأسلوب الشرح
        ['id' => 'trainer_knowledge',        'section' => 'تقييم المحاضر وأسلوب الشرح', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى تمكن المحاضر وسلاسة أسلوبه في التقديم',
            'label_en' => 'Instructor\'s command and smoothness of presentation'],
        ['id' => 'trainer_clarity',          'section' => 'تقييم المحاضر وأسلوب الشرح', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'وضوح الشرح وتسلسل الأفكار بدون حاجة للتفاعل المباشر',
            'label_en' => 'Clarity of explanation and flow of ideas without direct interaction'],
        ['id' => 'trainer_voice',            'section' => 'تقييم المحاضر وأسلوب الشرح', 'type' => 'scale', 'target' => 'trainer', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'نبرة الصوت وسرعة الإلقاء ومناسبتها للتعلم الذاتي',
            'label_en' => 'Tone of voice, pace, and suitability for self-paced learning'],

        // ثانياً: تقييم المحتوى وتنسيق الدورة
        ['id' => 'content_quality',          'section' => 'تقييم المحتوى وتنسيق الدورة', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة المحتوى العلمي ومدى شموليته وشرحه للهدف',
            'label_en' => 'Quality, comprehensiveness, and goal-relevance of scientific content'],
        ['id' => 'video_segmentation',       'section' => 'تقييم المحتوى وتنسيق الدورة', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'تقسيم المقاطع وتناسب مدة كل فيديو مع الشرح',
            'label_en' => 'Video segmentation and suitability of each video\'s duration'],
        ['id' => 'attachments_benefit',      'section' => 'تقييم المحتوى وتنسيق الدورة', 'type' => 'scale', 'target' => 'course', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مدى الاستفادة من المرفقات والملفات الإضافية (إن وجدت)',
            'label_en' => 'Benefit from attachments and supplementary files (if any)'],

        // ثالثاً: تقييم المنصة والتجربة التقنية
        ['id' => 'production_quality',       'section' => 'تقييم المنصة والتجربة التقنية', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'جودة إنتاج الفيديو والصوت',
            'label_en' => 'Video and audio production quality'],
        ['id' => 'navigation_ease',          'section' => 'تقييم المنصة والتجربة التقنية', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'سهولة التصفح والوصول للدروس والانتقال بين الوحدات',
            'label_en' => 'Ease of browsing, accessing lessons, and navigating between units'],
        ['id' => 'platform_flexibility',     'section' => 'تقييم المنصة والتجربة التقنية', 'type' => 'scale', 'target' => 'academy', 'required' => true, 'min' => 1, 'max' => 5,
            'label_ar' => 'مرونة المنصة ومناسبة أوقات مشاهدة المحتوى حسب وقتك',
            'label_en' => 'Platform flexibility and convenience of viewing content at your own pace'],

        // أسئلة عامة
        ['id' => 'recommend',               'section' => 'أسئلة عامة ومقترحات', 'type' => 'boolean', 'target' => 'course', 'required' => true,
            'label_ar' => 'هل تنصح زملاءك أو أشخاصاً آخرين بالالتحاق بهذه الدورة المسجلة؟',
            'label_en' => 'Would you recommend this recorded course to colleagues or others?'],
        ['id' => 'best_part',               'section' => 'أسئلة عامة ومقترحات', 'type' => 'text', 'target' => 'course', 'required' => true,
            'label_ar' => 'ما هي أكثر مقطع أو محتوى نال إعجابك وأفادك في الدورة؟',
            'label_en' => 'What content or segment impressed you the most and was most useful?'],
        ['id' => 'suggestions',             'section' => 'أسئلة عامة ومقترحات', 'type' => 'text', 'target' => 'academy', 'required' => true,
            'label_ar' => 'هل لديك أي مقترحات لتطوير التجربة أو تحسين المنصة والدورات المسجلة القادمة؟',
            'label_en' => 'Do you have any suggestions for improving the experience, platform, or future recorded courses?'],
    ],

];

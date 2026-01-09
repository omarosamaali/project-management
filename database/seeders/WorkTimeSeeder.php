<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkTime;
use App\Models\User;
use Carbon\Carbon;

class WorkTimeSeeder extends Seeder
{
    public function run()
    {
        // معرفات الموظفين
        $userIds = [2, 3, 4];

        foreach ($userIds as $id) {
            $user = User::find($id);
            if (!$user) continue;

            for ($i = 30; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // تخطي أيام الجمعة
                if ($date->isFriday()) continue;

                $country = $user->country_name ?? 'EG';

                // --- 1. منطق الحضور ---
                if ($id == 2) {
                    // الموظف 2: دائماً حضور مبكر (بين 8:45 و 8:59)
                    $presenceTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 08:45')
                        ->addMinutes(rand(0, 14));
                    $note = 'حضور مبكر / منتظم';
                } else {
                    // بقية الموظفين: حضور عشوائي (قد يتأخرون)
                    $presenceTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 09:00')
                        ->addMinutes(rand(-10, 30));

                    $nineAM = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 09:00');
                    $nineTenAM = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 09:10');

                    if ($presenceTime->lte($nineAM)) {
                        $note = 'حضور مبكر / منتظم';
                    } elseif ($presenceTime->lte($nineTenAM)) {
                        $note = 'متأخر (ضمن فترة السماح)';
                    } else {
                        $note = 'متأخر - يتم خصم 90 دقيقة';
                    }
                }

                WorkTime::create([
                    'user_id'    => $id,
                    'country'    => $country,
                    'type'       => 'حضور',
                    'date'       => $date->format('Y-m-d'),
                    'start_time' => $presenceTime->format('H:i'),
                    'notes'      => $note,
                ]);

                // --- 2. الاستراحة (ثابتة للكل) ---
                $breakOutTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 13:00')->addMinutes(rand(0, 10));
                WorkTime::create([
                    'user_id'    => $id,
                    'country'    => $country,
                    'type'       => 'خروج للاستراحة',
                    'date'       => $date->format('Y-m-d'),
                    'start_time' => $breakOutTime->format('H:i')
                ]);

                $breakInTime = (clone $breakOutTime)->addMinutes(rand(55, 65));
                WorkTime::create([
                    'user_id'    => $id,
                    'country'    => $country,
                    'type'       => 'دخول من الاستراحة',
                    'date'       => $date->format('Y-m-d'),
                    'start_time' => $breakInTime->format('H:i')
                ]);

                // --- 3. الانصراف ---
                if ($id == 2) {
                    // الموظف 2: دائماً لديه إضافي (ينصرف بين الساعة 7:00 و 8:30 مساءً)
                    // الساعة 18:00 هي نهاية العمل، أي وقت بعدها هو إضافي
                    $leaveTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 19:00')
                        ->addMinutes(rand(0, 90));
                } else {
                    // بقية الموظفين: انصراف عادي حول الساعة 5:00 مساءً
                    $leaveTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' 17:00')
                        ->addMinutes(rand(-5, 20));
                }

                WorkTime::create([
                    'user_id'    => $id,
                    'country'    => $country,
                    'type'       => 'انصراف',
                    'date'       => $date->format('Y-m-d'),
                    'start_time' => $leaveTime->format('H:i')
                ]);
            }
        }
    }
}

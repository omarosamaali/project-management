<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\EmployeeAdjustment;
use App\Models\User;
use App\Models\WorkTime;
use App\Services\WhatsAppOTPService;
use App\Support\HolidayCalendar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkAttendanceAbsences extends Command
{
    protected $signature = 'attendance:mark-absences {--date= : تاريخ اليوم Y-m-d (افتراضي: اليوم)}';

    protected $description = 'تسجيل غياب وخصم للموظفين الذين لم يسجّلوا حضوراً في يوم عمل';

    public function handle(WhatsAppOTPService $whatsapp): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::today();

        if ($date->isFriday()) {
            $this->info('يوم الجمعة — لا يُسجَّل غياب.');
            return self::SUCCESS;
        }

        $employees = User::query()
            ->where('role', 'partner')
            ->where('is_employee', true)
            ->notBlocked()
            ->where(fn ($q) => $q->where('status', 'active')->orWhereNull('status'))
            ->get();

        $marked = 0;

        foreach ($employees as $employee) {
            if (HolidayCalendar::isHolidayForUser($employee, $date)) {
                continue;
            }

            $hasAttendance = WorkTime::where('user_id', $employee->id)
                ->where('date', $date->toDateString())
                ->whereIn('type', ['حضور', 'دخول من الاستراحة'])
                ->exists();

            if ($hasAttendance) {
                continue;
            }

            $noteKey = 'غياب تلقائي — ' . $date->format('Y-m-d');
            $exists = EmployeeAdjustment::where('user_id', $employee->id)
                ->where('type', 'deduction')
                ->whereDate('date', $date->toDateString())
                ->where('notes', 'like', $noteKey . '%')
                ->exists();

            if ($exists) {
                continue;
            }

            $amount = HolidayCalendar::dailySalaryRate($employee);
            if ($amount <= 0) {
                $this->warn("تخطي #{$employee->id}: لا يوجد راتب أساسي.");
                continue;
            }

            $adjustment = EmployeeAdjustment::create([
                'user_id' => $employee->id,
                'type' => 'deduction',
                'amount' => $amount,
                'date' => $date->toDateString(),
                'notes' => $noteKey . ' — عدم تسجيل حضور',
            ]);

            $this->notifyAbsence($whatsapp, $employee, $adjustment, $date);
            $marked++;
        }

        $this->info("تم تسجيل {$marked} غياباً لتاريخ {$date->format('Y-m-d')}.");
        Log::info('[ATTENDANCE_ABSENCE] انتهى التشغيل', ['date' => $date->toDateString(), 'marked' => $marked]);

        return self::SUCCESS;
    }

    private function notifyAbsence(WhatsAppOTPService $whatsapp, User $user, EmployeeAdjustment $adjustment, Carbon $date): void
    {
        $currency = $user->salary_currency ?? $user->salary_currency_scale ?? 'USD';
        $amount = number_format((float) $adjustment->amount, 2);
        $title = 'تسجيل غياب';
        $message = "لم يُسجَّل حضورك يوم {$date->format('Y-m-d')}. تم خصم {$amount} {$currency}.";

        AppNotification::notify(
            $user->id,
            $title,
            $message,
            route('dashboard.adjustments.index'),
            'fa-user-times',
            'warning'
        );

        if ($user->email) {
            $whatsapp->sendEmailNotification(
                $user->email,
                $user->name,
                $title,
                $message
            );
        }

        if (!$user->phone) {
            return;
        }

        try {
            $whatsapp->sendAdjustmentNotification(
                phone: $user->phone,
                employeeName: $user->name,
                typeLabel: 'غياب',
                amount: (float) $adjustment->amount,
                currency: $currency,
                date: $date->format('Y-m-d'),
                notes: 'عدم تسجيل حضور في يوم عمل',
            );
        } catch (\Throwable $e) {
            Log::error('[ATTENDANCE_ABSENCE] فشل إرسال إشعار', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\EmployeeAdjustment;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use App\Support\WorkAttendanceState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdjustmentController extends Controller
{
    private function isEmployeeView(): bool
    {
        return WorkAttendanceState::isEmployeePartner(Auth::user());
    }

    private function denyUnlessAdmin(): void
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك');
        }
    }

    private function authorizeAdjustmentAccess(EmployeeAdjustment $adjustment): void
    {
        if ($this->isEmployeeView() && (int) $adjustment->user_id !== (int) Auth::id()) {
            abort(403, 'غير مصرح لك');
        }

        if ($adjustment->user && $adjustment->user->isBlocked()) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $isEmployeeView = $this->isEmployeeView();

        $adjustments = EmployeeAdjustment::with('user')
            ->whereHas('user', fn ($u) => $u->notBlocked())
            ->when($isEmployeeView, fn ($q) => $q->where('user_id', Auth::id()))
            ->when(!$isEmployeeView && $request->search, function ($q) use ($request) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.adjustments.index', compact('adjustments', 'isEmployeeView'));
    }

    public function create()
    {
        $this->denyUnlessAdmin();
        $employees = User::where('role', 'partner')->notBlocked()->get();
        return view('dashboard.adjustments.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->denyUnlessAdmin();
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:bonus,deduction',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $employee = User::notBlocked()->findOrFail($data['user_id']);

        $adjustment = EmployeeAdjustment::create([
            ...$data,
            'user_id' => $employee->id,
        ]);

        $adjustment->load('user');
        $this->notifyAdjustment($adjustment);

        return redirect()->route('dashboard.adjustments.index')
            ->with('success', 'تم حفظ السجل وإرسال إشعار للموظف بنجاح');
    }

    public function edit(EmployeeAdjustment $adjustment)
    {
        $this->denyUnlessAdmin();
        $this->authorizeAdjustmentAccess($adjustment);
        $employees = User::where('role', 'partner')->notBlocked()->get();
        return view('dashboard.adjustments.edit', compact('adjustment', 'employees'));
    }

    public function update(Request $request, EmployeeAdjustment $adjustment)
    {
        $this->denyUnlessAdmin();
        $this->authorizeAdjustmentAccess($adjustment);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:bonus,deduction',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        User::notBlocked()->findOrFail($data['user_id']);
        $adjustment->update($data);
        $adjustment->load('user');
        $this->notifyAdjustment($adjustment, true);

        return redirect()->route('dashboard.adjustments.index')
            ->with('success', 'تم تعديل السجل وإرسال الإشعارات بنجاح');
    }

    public function destroy(EmployeeAdjustment $adjustment)
    {
        $this->denyUnlessAdmin();
        $this->authorizeAdjustmentAccess($adjustment);
        $adjustment->delete();

        return back()->with('success', 'تم الحذف بنجاح');
    }

    private function notifyAdjustment(EmployeeAdjustment $adjustment, bool $isUpdate = false): void
    {
        try {
            $user = $adjustment->user ?? User::find($adjustment->user_id);

            if (!$user || $user->isBlocked()) {
                return;
            }

            $typeLabel = $adjustment->type === 'bonus' ? 'مكافأة' : 'خصm';
            $currency = $user->salary_currency ?? $user->salary_currency_scale ?? 'USD';
            $date = $adjustment->date instanceof \Carbon\Carbon
                ? $adjustment->date->format('Y-m-d')
                : (string) $adjustment->date;

            $employeeBody = $this->adjustmentMessageBody($typeLabel, (float) $adjustment->amount, $currency, $date, $adjustment->notes);
            $actionWord = $isUpdate ? 'تم تعديل' : 'تم تسجيل';
            $employeeTitle = $isUpdate ? "تعديل {$typeLabel}" : "إشعار {$typeLabel}";
            $adminBody = "{$actionWord} {$typeLabel} للموظف «{$user->name}» بمبلغ "
                . number_format((float) $adjustment->amount, 2) . " {$currency} بتاريخ {$date}.";
            if ($adjustment->notes) {
                $adminBody .= " ملاحظات: {$adjustment->notes}";
            }

            $url = route('dashboard.adjustments.index');
            $uiType = $adjustment->type === 'bonus' ? 'success' : 'warning';
            $icon = $adjustment->type === 'bonus' ? 'fa-gift' : 'fa-minus-circle';

            AppNotification::notify(
                $user->id,
                $employeeTitle,
                $employeeBody,
                $url,
                $icon,
                $uiType,
            );

            $whatsapp = app(WhatsAppOTPService::class);

            if ($user->email) {
                $whatsapp->sendEmailNotification(
                    $user->email,
                    $user->name,
                    $employeeTitle,
                    $employeeBody,
                );
            } else {
                Log::info("[ADJUSTMENT] لا يوجد بريد للموظف #{$user->id}");
            }

            if ($user->phone) {
                $sent = $whatsapp->sendAdjustmentNotification(
                    phone: $user->phone,
                    employeeName: $user->name,
                    typeLabel: $typeLabel,
                    amount: (float) $adjustment->amount,
                    currency: $currency,
                    date: $date,
                    notes: $adjustment->notes,
                );
                Log::info('[ADJUSTMENT] واتساب الموظف: ' . ($sent ? 'نجح' : 'فشل'), [
                    'adjustment_id' => $adjustment->id,
                    'user_id' => $user->id,
                ]);
            } else {
                Log::info("[ADJUSTMENT] لا يوجد رقم هاتف للموظف #{$user->id}");
            }

            $whatsapp->notifyManager($adminBody, 'الخصومات والمكافآت');

            User::where('role', 'admin')->get()->each(function (User $admin) use (
                $adminBody,
                $url,
                $icon,
                $uiType,
                $user,
            ) {
                if ((int) $admin->id === (int) $user->id) {
                    return;
                }

                AppNotification::notify(
                    $admin->id,
                    'خصومات ومكافآت',
                    $adminBody,
                    $url,
                    $icon,
                    $uiType,
                );

                if ($admin->email) {
                    app(WhatsAppOTPService::class)->sendEmailNotification(
                        $admin->email,
                        $admin->name,
                        'إشعار خصومات ومكافآت',
                        $adminBody,
                    );
                }
            });
        } catch (\Exception $e) {
            Log::error('[ADJUSTMENT] فشل إرسال إشعار الخصm/المكافأة', [
                'adjustment_id' => $adjustment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function adjustmentMessageBody(
        string $typeLabel,
        float $amount,
        string $currency,
        string $date,
        ?string $notes,
    ): string {
        $body = "تم تسجيل {$typeLabel} بمبلغ " . number_format($amount, 2) . " {$currency} بتاريخ {$date}.";
        if ($notes && trim($notes) !== '') {
            $body .= "\nملاحظات: " . trim($notes);
        }

        return $body;
    }
}

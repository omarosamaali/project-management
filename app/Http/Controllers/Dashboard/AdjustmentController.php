<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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

        $this->notifyEmployee($adjustment);

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

        return redirect()->route('dashboard.adjustments.index')
            ->with('success', 'تم تعديل السجل بنجاح');
    }

    public function destroy(EmployeeAdjustment $adjustment)
    {
        $this->denyUnlessAdmin();
        $this->authorizeAdjustmentAccess($adjustment);
        $adjustment->delete();

        return back()->with('success', 'تم الحذف بنجاح');
    }

    private function notifyEmployee(EmployeeAdjustment $adjustment): void
    {
        try {
            $user = $adjustment->user ?? User::find($adjustment->user_id);

            if (!$user || $user->isBlocked()) {
                return;
            }

            if (!$user->phone) {
                Log::info("[ADJUSTMENT] لا يوجد رقم هاتف للموظف #{$adjustment->user_id}");
                return;
            }

            $typeLabel = $adjustment->type === 'bonus' ? 'مكافأة' : 'خصم';
            $currency = $user->salary_currency ?? $user->salary_currency_scale ?? 'USD';
            $date = $adjustment->date instanceof \Carbon\Carbon
                ? $adjustment->date->format('Y-m-d')
                : (string) $adjustment->date;

            $whatsapp = app(WhatsAppOTPService::class);
            $sent = $whatsapp->sendAdjustmentNotification(
                phone: $user->phone,
                employeeName: $user->name,
                typeLabel: $typeLabel,
                amount: (float) $adjustment->amount,
                currency: $currency,
                date: $date,
                notes: $adjustment->notes,
            );

            if ($user->email) {
                $body = "تم تسجيل {$typeLabel} بمبلغ " . number_format((float) $adjustment->amount, 2)
                    . " {$currency} بتاريخ {$date}.";
                if ($adjustment->notes) {
                    $body .= "\nملاحظات: {$adjustment->notes}";
                }
                $whatsapp->sendEmailNotification(
                    $user->email,
                    $user->name,
                    "إشعار {$typeLabel}",
                    $body
                );
            }

            Log::info('[ADJUSTMENT] نتيجة إرسال الإشعار: ' . ($sent ? 'نجح' : 'فشل'), [
                'adjustment_id' => $adjustment->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('[ADJUSTMENT] فشل إرسال إشعار الخصم/المكافأة', [
                'adjustment_id' => $adjustment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

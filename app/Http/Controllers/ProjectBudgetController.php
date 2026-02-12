<?php

namespace App\Http\Controllers;

use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectBudgetController extends Controller
{
    public function updateBudget(Request $request, $id)
    {
        $specialRequest = Requests::findOrFail($id);

        DB::transaction(function () use ($request, $specialRequest) {
            // 1. تحديث السعر في الطلب الأساسي
            $specialRequest->update(['price' => $request->price]);

            // 2. إدارة الدفعات
            if ($request->has('installments')) {
                // حذف الدفعات القديمة التي لم تُدفع بعد (اختياري حسب منطق عملك)
                $specialRequest->installments()->delete();

                foreach ($request->installments as $ins) {
                    if (!empty($ins['name']) && !empty($ins['amount'])) {
                        $specialRequest->installments()->create([
                            'payment_name' => $ins['name'],
                            'amount'       => $ins['amount'],
                            'due_date'     => $ins['due_date'],
                            'status'       => 'unpaid'
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'تم تحديث الميزانية والدفعات بنجاح');
    }
}
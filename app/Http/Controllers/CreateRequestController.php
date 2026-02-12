<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequest;
use App\Models\System;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class CreateRequestController extends Controller
{
    public function createRequest()
    {
        $systems = System::all();
        $clients = User::all();
        $services = Service::all();

        return view('dashboard.requests.create-request', compact('systems', 'clients', 'services'));
    }

    public function postRequest(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:clients,id',
            'title' => 'required|string|max:255',
            'project_type' => 'required|string',
            'system_id' => 'nullable|exists:systems,id',
            'description' => 'required|string',
            'core_features' => 'nullable|string',
            'examples' => 'nullable|string',
            'budget' => 'nullable|numeric',
            'deadline' => 'nullable|date',
            'bidding_deadline' => 'nullable|date',
            'status' => 'required|string|in:active,pending,in_review,in_progress,completed,canceled,بإنتظار طلب,بإنتظار عروض الأسعار',
            'is_project' => 'required|boolean',
            'price' => 'nullable|numeric',
            'payment_type' => 'nullable|string|in:full,installments',
            'order_number' => 'required|string|unique:special_requests,order_number',
        ]);

        // ✅ إذا لم يتم اختيار عميل، استخدم العميل الافتراضي
        if (empty($validated['user_id'])) {
            $defaultClient = User::where('email', 'contact@evorq.com')->first();

            if (!$defaultClient) {
                return back()->withErrors(['user_id' => 'العميل الافتراضي غير موجود. يرجى تشغيل الـ Seeder.']);
            }

            $validated['user_id'] = $defaultClient->id;
        }

        // إنشاء الطلب
        SpecialRequest::create($validated);

        return redirect()
            ->route('dashboard.requests.index')
            ->with('success', 'تم إضافة الطلب بنجاح');
    }
}

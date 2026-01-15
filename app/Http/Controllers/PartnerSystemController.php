<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyService;

class PartnerSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // جلب قيم البحث من الطلب
        $search = $request->input('search');
        $userId = $request->input('user_id');

        // بناء الاستعلام
        $query = MyService::where('status', 'active')->with('user', 'service');

        // فلتر البحث النصي (اسم الخدمة أو اسم الشريك)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name_en', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // فلتر اختيار الشريك من القائمة المنسدلة
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $myServices = $query->paginate(8)->withQueryString();

        // جلب قائمة الشركاء فقط لعرضهم في الفلتر (الذين لديهم خدمات مفعلة)
        $partners = \App\Models\User::whereHas('myServices', function ($q) {
            $q->where('status', 'active');
        })->get();

        return view('dashboard.partner_systems.index', compact('myServices', 'partners'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $myService = MyService::findOrFail($id);
        return view('dashboard.partner_systems.show', compact('myService'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $myService = MyService::findOrFail($id);
        $myService->delete();
        return redirect()
            ->route('dashboard.partner_systems.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}

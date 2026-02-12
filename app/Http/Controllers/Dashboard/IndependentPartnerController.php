<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class IndependentPartnerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $partners = User::where('role', 'independent_partner')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.independent-partners.index', compact('partners'));
    }

    // صفحة عرض تفاصيل التحقق (فيديو وبطاقة)
    public function show($id)
    {
        $partner = User::where('role', 'independent_partner')->findOrFail($id);
        return view('dashboard.independent-partners.show', compact('partner'));
    }

    // صفحة تعديل البيانات الأساسية
    public function edit($id)
    {
        $partner = User::where('role', 'independent_partner')->findOrFail($id);
        return view('dashboard.independent-partners.edit', compact('partner'));
    }

    // تحديث البيانات
    public function update(Request $request, $id)
    {
        $partner = User::where('role', 'independent_partner')->findOrFail($id);

        // التحقق من حالة الـ status فقط إذا كنت أرسلت الباقي كـ Readonly
        $request->validate([
            'status' => 'required|in:active,pending,blocked,inactive',
        ]);

        // التحديث المباشر للحالة
        $updated = $partner->update([
            'status' => $request->status
        ]);

        // اختبار بسيط للتأكد من التحديث
        if ($updated) {
            return redirect()->route('dashboard.independent-partners.index')
                ->with('success', 'تم تحديث حالة الشريك بنجاح');
        }

        return back()->with('error', 'حدث خطأ أثناء محاولة التحديث');
    }
    public function destroy($id)
    {
        // التأكد من أن المستخدم موجود وهو شريك مستقل
        $partner = User::where('role', 'independent_partner')->findOrFail($id);

        // (اختياري) حذف الملفات المرتبطة به من التخزين لتوفير المساحة
        if ($partner->avatar) {
            Storage::disk('public')->delete($partner->avatar);
        }
        if ($partner->id_card_path) {
            Storage::disk('public')->delete($partner->id_card_path);
        }
        if ($partner->verification_video) {
            Storage::disk('public')->delete($partner->verification_video);
        }

        // حذف السجل من قاعدة البيانات
        $partner->delete();

        return redirect()->route('dashboard.independent-partners.index')
            ->with('success', 'تم حذف حساب الشريك وجميع ملفاته بنجاح');
    }
}
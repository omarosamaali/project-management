<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Requests $userRequest)
    {
        $validated = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'stars.required' => 'يرجى اختيار تقييم',
            'stars.min' => 'التقييم يجب أن يكون من 1 إلى 5',
            'stars.max' => 'التقييم يجب أن يكون من 1 إلى 5',
            'comment.max' => 'التعليق يجب ألا يتجاوز 1000 حرف',
        ]);

        // التحقق من أن الطلب منتهي
        if ($userRequest->status !== 'منتهية') {
            return back()->with('error', 'لا يمكن تقييم طلب غير منتهي');
        }

        // التحقق من عدم وجود تقييم سابق
        if ($userRequest->rating) {
            return back()->with('error', 'تم تقييم هذا الطلب مسبقاً');
        }

        // إنشاء التقييم
        Rating::create([
            'request_id' => $userRequest->id,
            'user_id' => Auth::id(),
            'stars' => $validated['stars'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('dashboard.requests.index')
            ->with('success', 'شكراً لتقييمك! تم إرسال التقييم بنجاح');
    }
}
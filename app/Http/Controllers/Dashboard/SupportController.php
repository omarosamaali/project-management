<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Support;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $supports = Support::with(['user', 'request', 'unreadMessages'])
            ->when($search, function ($query, $search) {
                return $query->where('subject', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('last_message_at', 'desc')
            ->whereHas('request', function ($query) {
                $query->where('status', '!=', 'منتهية');
            })->paginate(10);

        return view('dashboard.support.index', compact('supports'));
    }

    // Show Method
    public function show($id)
    {
        $support = Support::with(['user', 'request', 'messages.user'])->findOrFail($id);
        $support->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return view('dashboard.support.show', compact('support'));
    }

    // SendMessage Method
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $support = Support::findOrFail($id);

        SupportMessage::create([
            'support_id' => $support->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false
        ]);

        $support->update([
            'last_message_at' => now(),
            'status' => 'in_progress'
        ]);

        return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح');
    }

    // UpdateStatus Method
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,closed'
        ]);

        $support = Support::findOrFail($id);
        $support->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'تم تحديث حالة التذكرة');
    }
}

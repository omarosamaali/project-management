<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SessionRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userEmail = $user->email;
        $userId = $user->id;
        $query = SessionRequest::with('user');
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($userEmail, $userId) {
                $q->where('user_id', $userId)
                    ->orWhereJsonContains('invitees', ['email' => $userEmail]);
            });
        }
        $sessions = $query->latest()->paginate(10);
        return view('dashboard.sessions.index', compact('sessions'));
    }

    public function show(SessionRequest $session)
    {
        $user = auth()->user();
        $isOwner = $session->user_id === $user->id;
        $isInvited = $session->getParticipantStatus($user->email) !== null;
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && !$isOwner && !$isInvited) {
            abort(403, 'غير مسموح لك بالدخول');
        }
        $users = User::where('role', '!=', 'admin')->get();
        return view('dashboard.sessions.show', compact('session', 'users'));
    }

    public function update(Request $request, SessionRequest $session)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $request->validate([
            'session_time' => 'required',
            'session_link' => 'required|url',
            'user_ids'     => 'required|array',
        ]);
        $invitees = [];
        $selectedUsers = User::whereIn('id', $request->user_ids)->get();
        foreach ($selectedUsers as $user) {
            $invitees[] = [
                'email'  => $user->email,
                'name'   => $user->name,
                'status' => 'pending'
            ];
        }
        $session->update([
            'session_time' => $request->session_time,
            'session_link' => $request->session_link,
            'invitees'     => $invitees,
            'status'       => 'confirmed',
        ]);
        return back()->with('success', 'تم تحديد موعد الاجتماع ودعوة المستخدمين');
    }

    public function create()
    {
        return view('dashboard.sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
        ]);

        SessionRequest::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        return redirect()->route('dashboard.sessions.index')->with('success', 'تم إرسال طلب الاجتماع بنجاح');
    }

    public function getParticipantStatus($email)
    {
        if (!$this->invitees || !is_array($this->invitees)) {
            return null;
        }

        foreach ($this->invitees as $invitee) {
            if (is_array($invitee)) {
                if (isset($invitee['email']) && $invitee['email'] === $email) {
                    return $invitee['status'] ?? 'pending';
                }
            }
            elseif (is_string($invitee)) {
                if ($invitee === $email) {
                    return 'pending'; // الحالة الافتراضية للبيانات القديمة
                }
            }
        }

        return null;
    }
    // تعديل دالة الـ update الأصلية (عندما يضيف الأدمن مدعوين لأول مرة)

    public function updateParticipantStatus(Request $request, SessionRequest $session)
    {
        $request->validate(['status' => 'required|in:accepted,rejected,attended,absent']);

        $userEmail = auth()->user()->email;
        $invitees = $session->invitees;
        $updatedInvitees = [];
        $found = false;

        foreach ($invitees as $invitee) {
            $item = is_array($invitee) ? $invitee : ['email' => $invitee, 'status' => 'pending'];
            if ($item['email'] === $userEmail) {
                $item['status'] = $request->status;
                $found = true;
            }
            $updatedInvitees[] = $item;
        }

        if (!$found) {
            return back()->with('error', 'أنت غير مدعو لهذا الاجتماع');
        }

        $session->update(['invitees' => $updatedInvitees]);

        return back()->with('success', 'تم تحديث حالتك بنجاح');
    }
}

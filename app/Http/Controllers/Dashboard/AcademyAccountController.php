<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\TrainerApprovedMail;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AcademyAccountController extends Controller
{
    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403, 'غير مصرح لك بإدارة حسابات الأكاديمية.');
    }

    /**
     * @return array{role:string,title:string,singular:string,route:string,create_label:string,empty:string}
     */
    protected function meta(string $role): array
    {
        return match ($role) {
            'trainer' => [
                'role' => 'trainer',
                'title' => __('messages.trainers_accounts'),
                'singular' => __('messages.trainer'),
                'route' => 'dashboard.trainers',
                'create_label' => __('messages.add_trainer'),
                'empty' => __('messages.no_trainers'),
            ],
            'trainee' => [
                'role' => 'trainee',
                'title' => __('messages.trainees_accounts'),
                'singular' => __('messages.trainee'),
                'route' => 'dashboard.trainees',
                'create_label' => __('messages.add_trainee'),
                'empty' => __('messages.no_trainees'),
            ],
            default => abort(404),
        };
    }

    protected function findAccount(string $role, User $user): User
    {
        abort_unless($user->role === $role, 404);

        return $user;
    }

    protected function routeRole(): string
    {
        return (string) request()->route('role');
    }

    protected function notifyTrainerApproved(User $trainer): array
    {
        $loginUrl = Course::publicBaseUrl() . '/login';
        $result = ['email' => false, 'whatsapp' => false];

        try {
            if (filled($trainer->email)) {
                Mail::to($trainer->email, $trainer->name)->send(new TrainerApprovedMail($trainer));
                $result['email'] = true;
            }
        } catch (\Throwable $e) {
            Log::error('[TRAINER APPROVE] فشل إرسال الإيميل', [
                'user_id' => $trainer->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            if (filled($trainer->phone)) {
                $result['whatsapp'] = app(WhatsAppOTPService::class)->sendTrainerApprovedNotification(
                    (string) $trainer->phone,
                    (string) $trainer->name,
                    $loginUrl,
                );
            }
        } catch (\Throwable $e) {
            Log::error('[TRAINER APPROVE] فشل إرسال الواتساب', [
                'user_id' => $trainer->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);
        $search = trim((string) $request->input('search', ''));

        $users = User::where('role', $role)
            ->when($role === 'trainer', fn ($q) => $q->with('courseCategory'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'active' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.academy.accounts.index', compact('users', 'meta', 'search'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $meta = $this->meta($this->routeRole());

        return view('dashboard.academy.accounts.create', compact('meta'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'country' => $validated['country'] ?? null,
            'role' => $role,
            'account_type' => 'personal',
        ]);

        return redirect()
            ->route($meta['route'] . '.index')
            ->with('success', __('messages.account_created_successfully'));
    }

    public function show(User $user)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);
        $account = $this->findAccount($role, $user);
        if ($role === 'trainer') {
            $account->load('courseCategory');
        }

        return view('dashboard.academy.accounts.show', [
            'meta' => $meta,
            'account' => $account,
        ]);
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);
        $account = $this->findAccount($role, $user);
        $categories = $role === 'trainer'
            ? CourseCategory::query()->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('dashboard.academy.accounts.edit', [
            'meta' => $meta,
            'account' => $account,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);
        $account = $this->findAccount($role, $user);
        $wasPending = $account->status === 'pending';

        $statusOptions = $role === 'trainer'
            ? ['active', 'inactive', 'pending']
            : ['active', 'inactive'];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($account->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', Rule::in($statusOptions)],
            'country' => ['nullable', 'string', 'max:100'],
        ];

        if ($role === 'trainer') {
            $rules['course_category_id'] = ['nullable', 'exists:course_categories,id'];
        }

        $validated = $request->validate($rules);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'country' => $validated['country'] ?? null,
            'role' => $role,
        ];

        if ($role === 'trainer') {
            $payload['course_category_id'] = $validated['course_category_id'] ?? null;
        }

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $account->update($payload);

        $approvedNow = $role === 'trainer'
            && $wasPending
            && $validated['status'] === 'active';

        if ($approvedNow) {
            $account->loadMissing('courseCategory');
            $notificationResult = $this->notifyTrainerApproved($account->fresh(['courseCategory']));
            $message = $notificationResult['email'] && $notificationResult['whatsapp']
                ? __('messages.trainer_approved_notified')
                : __('messages.trainer_approved_partial_notify');

            return redirect()
                ->route($meta['route'] . '.show', $account)
                ->with('success', $message);
        }

        return redirect()
            ->route($meta['route'] . '.index')
            ->with('success', __('messages.account_updated_successfully'));
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();
        $role = $this->routeRole();
        $meta = $this->meta($role);
        $account = $this->findAccount($role, $user);
        $account->delete();

        return redirect()
            ->route($meta['route'] . '.index')
            ->with('success', __('messages.account_deleted_successfully'));
    }
}

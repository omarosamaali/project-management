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
use Illuminate\Support\Facades\DB;
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
        $loginUrl = \App\Support\AppDomains::academyUrl('/login?ui=academy');
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
        $normalizedSearch = mb_strtolower($search);

        $users = User::where('role', $role)
            ->when($role === 'trainer', fn ($q) => $q->with('courseCategory')->withCount('trainedCourses'))
            ->when($search !== '', function ($query) use ($search, $normalizedSearch) {
                $query->where(function ($q) use ($search, $normalizedSearch) {
                    $q->whereRaw('LOWER(name) like ?', ['%' . $normalizedSearch . '%'])
                        ->orWhereRaw('LOWER(email) like ?', ['%' . $normalizedSearch . '%'])
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
            $rules['linkedin_url'] = ['nullable', 'url', 'max:500'];
            $rules['teaching_language'] = ['nullable', 'in:ar,en'];
            $rules['trainer_bio'] = ['nullable', 'string', 'max:5000'];
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
            $payload['linkedin_url'] = $validated['linkedin_url'] ?? null;
            $payload['teaching_language'] = $validated['teaching_language'] ?? null;
            $payload['trainer_bio'] = $validated['trainer_bio'] ?? null;
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

        $deletedCoursesCount = 0;
        DB::transaction(function () use ($account, $role, &$deletedCoursesCount) {
            if ($role === 'trainer') {
                $deletedCoursesCount = Course::query()
                    ->where('trainer_id', $account->id)
                    ->count();

                Course::query()
                    ->where('trainer_id', $account->id)
                    ->delete();
            }

            $account->delete();
        });

        $successMessage = __('messages.account_deleted_successfully');
        if ($role === 'trainer' && $deletedCoursesCount > 0) {
            $successMessage .= ' — ' . __('messages.deleted_related_courses_count', ['count' => $deletedCoursesCount]);
        }

        return redirect()
            ->route($meta['route'] . '.index')
            ->with('success', $successMessage);
    }
}

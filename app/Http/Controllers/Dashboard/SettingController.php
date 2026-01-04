<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');

        $settingss = User::whereIn('role', ['partner', 'design_partner', 'advertising_partner'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', '%' . $search . '%');
            })
            ->with('systems')
            ->withCount([
                'systems as partner_requests_count' => function ($query) {
                    $query->join('requests', 'systems.id', '=', 'requests.system_id');
                }
            ])
            ->latest()
            ->paginate(8);

        return view('dashboard.settings.index', compact('settingss'));
    }

    // Create Method
    public function create()
    {
        $systems = System::orderBy('name_ar')->get();

        if ($systems->isEmpty()) {
            return redirect()->route('dashboard.settings.index')
                ->with('error', 'لا توجد أنظمة متاحة. يجب إضافة أنظمة أولاً');
        }

        return view('dashboard.settings.create', compact('systems'));
    }

    // Store Method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'systems_id' => 'required|array|min:1',
            'systems_id.*' => 'exists:systems,id',
            'password' => 'required|string|min:8',
            'percentage' => 'required|numeric|min:0|max:100',
            'orders' => 'nullable|numeric|min:0',
            'withdrawal_method' => 'nullable|in:wallet,paypal',
            'withdrawal_email' => 'nullable',
            'withdrawal_notes' => 'nullable|string|max:500',

        ], [
            'systems_id.required' => 'يجب اختيار نظام واحد على الأقل',
            'systems_id.min' => 'يجب اختيار نظام واحد على الأقل',
            'systems_id.*.exists' => 'أحد الأنظمة المحددة غير موجود في قاعدة البيانات',
        ]);

        try {
            // إنشاء الشريك
            $settings = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $request->input('role'),
                'percentage' => $validated['percentage'],
                'orders' => $validated['orders'] ?? 0,
                'withdrawal_method' => $validated['withdrawal_method'] ?? null,
                'withdrawal_email' => $validated['withdrawal_email'] ?? null,
                'withdrawal_notes' => $validated['withdrawal_notes'] ?? null,
            ]);

            $systemIds = array_values(array_unique(array_filter($validated['systems_id'])));
            // ربط الشريك بالأنظمة
            if (!empty($systemIds)) {
                $settings->systems()->attach($systemIds);
            }

            return redirect()->route('dashboard.settings.index')
                ->with('success', 'تم إضافة الشريك بنجاح');
        } catch (\Exception $e) {
            if (isset($settings) && $settings->exists) {
                $settings->delete();
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إضافة الشريك: ' . $e->getMessage()]);
        }
    }

    // Show Method
    public function show(User $setting)
    {
        $setting->load('systems');
        return view('dashboard.settings.show', compact('setting'));
    }

    // Edit Method
    public function edit(User $setting)
    {
        $systems = System::all();
        $setting->load('systems');

        return view('dashboard.settings.edit', compact('setting', 'systems'));
    }

    // Update Method
    public function update(Request $request, User $setting)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $setting->id,
            'systems_id' => 'required|array|min:1',
            'systems_id.*' => 'exists:systems,id',
            'password' => 'nullable|string|min:8',
            'percentage' => 'required|numeric|min:0|max:100',
            'orders' => 'nullable|numeric|min:0',
            'withdrawal_method' => 'nullable|in:wallet,paypal',
            'withdrawal_email' => 'nullable',
            'withdrawal_notes' => 'nullable|string|max:500',

        ], [
            'systems_id.required' => 'يجب اختيار نظام واحد على الأقل',
            'systems_id.min' => 'يجب اختيار نظام واحد على الأقل',
        ]);
        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'percentage' => $validated['percentage'],
            'orders' => $validated['orders'] ?? 0,
            'role' => $request->input('role'),
            'withdrawal_method' => $validated['withdrawal_method'] ?? null,
            'withdrawal_email' => $validated['withdrawal_email'] ?? null,
            'withdrawal_notes' => $validated['withdrawal_notes'] ?? null,

        ];
        if (!empty($request->password)) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }
        $setting->update($dataToUpdate);
        $systemIds = array_unique($validated['systems_id']);
        $setting->systems()->sync($systemIds);
        return redirect()->route('dashboard.settings.index')
            ->with('success', 'تم تحديث الشريك بنجاح');
    }

    // Destroy Method
    public function destroy(User $setting)
    {
        $setting->delete();
        return redirect()->route('dashboard.settings.index')
            ->with('success', 'تم حذف الشريك بنجاح');
    }
}
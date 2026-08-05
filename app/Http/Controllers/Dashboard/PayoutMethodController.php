<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PayoutMethodController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $methods = PayoutMethod::query()
            ->withCount('paymentProfiles')
            ->orderBy('id')
            ->get();

        return view('dashboard.academy.payout-methods.index', compact('methods'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('dashboard.academy.payout-methods.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validateMethod($request);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = $request->file('image')->store('payout-methods', 'public');
        }

        $method = PayoutMethod::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'image_path' => $data['image_path'] ?? null,
            'is_active' => $data['is_active'],
            'is_system' => false,
            'sort_order' => 0,
        ]);
        $method->update(['sort_order' => $method->id]);

        $this->syncFields($method, $request->input('fields', []));

        return redirect()
            ->route('dashboard.academy.payout-methods.index')
            ->with('success', __('messages.payout_method_created'));
    }

    public function edit(PayoutMethod $payoutMethod)
    {
        $this->authorizeAdmin();

        $payoutMethod->load('fields');

        return view('dashboard.academy.payout-methods.edit', [
            'method' => $payoutMethod,
        ]);
    }

    public function update(Request $request, PayoutMethod $payoutMethod)
    {
        $this->authorizeAdmin();

        $data = $this->validateMethod($request);

        if ($request->boolean('remove_image') && ! $request->hasFile('image')) {
            if ($payoutMethod->image_path) {
                Storage::disk('public')->delete($payoutMethod->image_path);
            }
            $data['image_path'] = null;
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($payoutMethod->image_path) {
                Storage::disk('public')->delete($payoutMethod->image_path);
            }
            $data['image_path'] = $request->file('image')->store('payout-methods', 'public');
        }

        $payoutMethod->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'image_path' => $data['image_path'] ?? $payoutMethod->image_path,
            'is_active' => $data['is_active'],
        ]);

        if (! $payoutMethod->is_system) {
            $this->syncFields($payoutMethod, $request->input('fields', []));
        }

        return redirect()
            ->route('dashboard.academy.payout-methods.edit', $payoutMethod)
            ->with('success', __('messages.payout_method_updated'));
    }

    public function destroy(PayoutMethod $payoutMethod)
    {
        $this->authorizeAdmin();

        if ($payoutMethod->is_system) {
            return back()->with('error', __('messages.payout_method_cannot_delete_system'));
        }

        if ($payoutMethod->paymentProfiles()->exists() || $payoutMethod->cashoutRequests()->exists()) {
            return back()->with('error', __('messages.payout_method_cannot_delete_in_use'));
        }

        if ($payoutMethod->image_path) {
            Storage::disk('public')->delete($payoutMethod->image_path);
        }

        $payoutMethod->delete();

        return redirect()
            ->route('dashboard.academy.payout-methods.index')
            ->with('success', __('messages.payout_method_deleted'));
    }

    protected function syncFields(PayoutMethod $method, array $fields): void
    {
        $method->fields()->delete();

        $order = 0;
        $usedKeys = [];

        foreach ($fields as $field) {
            if (! is_array($field) || ! filled($field['label_ar'] ?? null)) {
                continue;
            }

            $source = trim((string) ($field['label_en'] ?? ''));
            if ($source === '') {
                $source = trim((string) $field['label_ar']);
            }

            $baseKey = \Illuminate\Support\Str::slug($source, '_') ?: ('field_'.$order);
            $key = $baseKey;
            $suffix = 2;
            while (isset($usedKeys[$key])) {
                $key = $baseKey.'_'.$suffix;
                $suffix++;
            }
            $usedKeys[$key] = true;

            $method->fields()->create([
                'key' => $key,
                'label_ar' => $field['label_ar'],
                'label_en' => $field['label_en'] ?? $field['label_ar'],
                'input_type' => $field['input_type'] ?? 'text',
                'is_required' => ! empty($field['is_required']),
                'sort_order' => $order,
            ]);

            $order++;
        }
    }

    protected function validateMethod(Request $request): array
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif,svg|max:2048',
            'remove_image' => 'nullable',
            'fields' => 'nullable|array',
            'fields.*.label_ar' => 'nullable|string|max:255',
            'fields.*.label_en' => 'nullable|string|max:255',
            'fields.*.input_type' => 'nullable|in:text,textarea,email,number',
            'fields.*.is_required' => 'nullable',
        ], [
            'image.mimes' => 'صيغة الصورة يجب أن تكون JPG أو PNG أو WEBP أو GIF أو SVG.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ]);

        unset($data['remove_image'], $data['image']);

        $rawActive = $request->input('is_active');
        if (is_array($rawActive)) {
            $data['is_active'] = in_array('1', array_map('strval', $rawActive), true);
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        return $data;
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user() instanceof \App\Models\User && Auth::user()->isAdmin(), 403);
    }
}

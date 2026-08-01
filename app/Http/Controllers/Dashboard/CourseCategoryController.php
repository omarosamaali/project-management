<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Support\WatermarkedUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        $search = trim((string) $request->input('search'));
        $query = CourseCategory::withCount('courses')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(12)->withQueryString();

        return view('dashboard.course-categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        return view('dashboard.course-categories.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        $data = $this->validateCategory($request);

        if ($request->hasFile('icon')) {
            $data['icon'] = WatermarkedUpload::store($request->file('icon'), 'course-categories');
        }

        CourseCategory::create($data);

        return redirect()
            ->route('dashboard.course-categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح.');
    }

    public function edit(CourseCategory $courseCategory)
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        return view('dashboard.course-categories.edit', [
            'category' => $courseCategory,
        ]);
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        $data = $this->validateCategory($request);

        if ($request->boolean('remove_icon') && !$request->hasFile('icon')) {
            if ($courseCategory->icon) {
                Storage::disk('public')->delete($courseCategory->icon);
            }
            $data['icon'] = null;
        } elseif ($request->hasFile('icon')) {
            if ($courseCategory->icon) {
                Storage::disk('public')->delete($courseCategory->icon);
            }
            $data['icon'] = WatermarkedUpload::store($request->file('icon'), 'course-categories');
        }

        $courseCategory->update($data);

        return redirect()
            ->route('dashboard.course-categories.edit', $courseCategory)
            ->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy(CourseCategory $courseCategory)
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->canManageCourses(), 403);

        if ($courseCategory->courses()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لوجود دورات مرتبطة به.');
        }

        if ($courseCategory->icon) {
            Storage::disk('public')->delete($courseCategory->icon);
        }

        $courseCategory->delete();

        return redirect()
            ->route('dashboard.course-categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح.');
    }

    protected function validateCategory(Request $request): array
    {
        $data = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'icon' => [
                'nullable',
                'file',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value instanceof \Illuminate\Http\UploadedFile) {
                        return;
                    }
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (! in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'gif', 'svg'], true)) {
                        $fail('صيغة الأيقونة يجب أن تكون JPG أو PNG أو WEBP أو GIF أو SVG.');
                    }
                },
            ],
            'is_active' => 'nullable|boolean',
            'remove_icon' => 'nullable|boolean',
        ]);

        unset($data['remove_icon']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

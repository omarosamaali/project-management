<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MyService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyServiceController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $my_services = MyService::where('title', 'LIKE', '%' . $search . '%')->latest()->paginate(8);
        } else {
            $my_services = MyService::latest()->paginate(8);
        }
        return view('dashboard.my_services.index', compact('my_services'));
    }

    // Create Method
    public function create()
    {
        $services = Service::all();
        return view('dashboard.my_services.create', compact('services'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'service_id' => 'required|exists:services,id',
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required|string',
            'what_you_will_get' => 'required|string',
        ]);

        $request->merge(['user_id' => Auth::id()]);

        MyService::create($request->all());

        return redirect()->route('dashboard.my_services.index')
            ->with('success', 'تمت اضافة الخدمة بنجاح');
    }

    // Show Method
    public function show(MyService $myService)
    {
        return view('dashboard.my_services.show', compact('myService'));
    }

    // Edit Method
    public function edit(MyService $myService)
    {
        $services = Service::all();
        return view('dashboard.my_services.edit', compact('services', 'myService'));
    }

    // Update Method
    public function update(Request $request, MyService $myService)
    {
        $request->validate([
            'title' => 'required',
            'service_id' => 'required|exists:services,id',
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required|string',
            'what_you_will_get' => 'required|string',
        ]);

        $myService->update($request->all());

        return redirect()
            ->route('dashboard.my_services.index')
            ->with('success', 'تم تعديل الخدمة بنجاح');
    }


    // Destroy Method
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()
            ->route('dashboard.my_services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}

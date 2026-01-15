<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Support\Facades\Auth;
use App\Models\Requests;
use App\Models\Service;
use App\Models\System;

class SystemController extends Controller
{
    // Index Method
    public function index()
    {
        $systems = System::where('status', 'active')
            ->with(['payments', 'requests', 'service'])
            ->get();
        $logos = Logo::all();
        $services = Service::where('status', 'active')->get();

        return view('system.index', compact('systems', 'logos', 'services'));
    }

    // Show Method
    public function show(System $system)
    {
        $system->load('partners');
        $is_purchased = Requests::where('client_id' , Auth::id())->where('system_id', $system->id)->exists();
        return view('system.show', compact('system', 'is_purchased'));
    }

}

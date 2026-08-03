<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Support\Facades\Auth;
use App\Models\Requests;
use App\Models\MyStore;
use App\Models\System;
use App\Models\Service;

class SystemController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user && ($user->isTrainee() || $user->isTrainer())) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('system.index');
        }

        $systems = System::where('status', 'active')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        $stores = MyStore::where('status', 'نشط')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        $items = $systems->map(function ($system) {
            return (object) [
                'type' => 'system',
                'id' => $system->id,
                'service_id' => $system->service_id,
                'name_ar' => $system->name_ar,
                'name_en' => $system->name_en,
                'description_ar' => $system->description_ar,
                'description_en' => $system->description_en,
                'main_image' => $system->main_image,
                'price' => $system->price,
                'service_name_ar' => $system->service?->name_ar,
                'total_participants' => ($system->payments_count ?? 0) + ($system->counter ?? 0),
                'execution_days_to' => $system->execution_days_to ?? null,
                'counter' => $system->counter ?? 0,
                'route' => route('system.show', $system),
            ];
        })->concat($stores->map(function ($store) {
            return (object) [
                'type' => 'store',
                'id' => $store->id,
                'service_id' => $store->service_id,
                'name_ar' => $store->name_ar,
                'name_en' => $store->name_en,
                'description_ar' => $store->description_ar,
                'description_en' => $store->description_en,
                'main_image' => $store->main_image,
                'price' => $store->price,
                'original_price' => $store->original_price,
                'service_name_ar' => $store->service?->name_ar,
                'total_participants' => $store->payments_count ?? 0,
                'execution_days' => $store->execution_days,
                'support_days' => $store->support_days,
                'route' => route('stores.show', $store->id),
            ];
        }));

        $logos = Logo::all();
        $services = Service::where('status', 'active')
            ->where('name_ar', 'not like', '%دورة%')
            ->where('name_en', 'not like', '%training%')
            ->where('name_en', 'not like', '%course%')
            ->get();

        return view('system.index', compact('items', 'logos', 'services'));
    }
    // في SystemController.php
    public function show(System $system)
    {
        $system->load(['partners', 'service']);
        
        // حساب المقاعد المتبقية
        $capacity = $system->counter ?? 0;
        $enrolled = \App\Models\Payment::where('system_id', $system->id)->count();
        $remaining_seats = $capacity - $enrolled;
        $is_purchased = Requests::where('client_id', Auth::id())
        ->where('system_id', $system->id)
        ->exists();
        
        $related_systems = System::where('service_id', $system->service_id)
            ->where('id', '!=', $system->id)
            ->where('status', 'active')
            ->limit(6)
        ->get();
        return view('system.show', compact('system', 'is_purchased', 'remaining_seats', 'related_systems'));
    }

    
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawalRequests = WithdrawalRequest::where('user_id', Auth::user()->id)->paginate(8);
        return view('dashboard.withdrawal-requests.index', compact('withdrawalRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WithdrawalRequest $withdrawal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WithdrawalRequest $withdrawalRequest): void
    {
        //
    }
}

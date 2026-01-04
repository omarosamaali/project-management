<?php

namespace App\Http\Controllers;

use App\Models\Project_Manager;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'special_request_id' => 'required',
            'request_id'         => 'required', // حقل جديد
            'user_id'            => 'required|exists:users,id',
        ]);

        // استخدام updateOrCreate لضمان وجود مدير مشروع واحد فقط لكل طلب
        Project_Manager::updateOrCreate(
            [
                'special_request_id' => $validated['special_request_id'],
                'request_id'         => $validated['request_id'],
            ],
            [
                'user_id' => $validated['user_id'],
            ]
        );

        return back()->with('success', 'تم تحديد مدير المشروع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Manager $project_Manager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project_Manager $project_Manager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project_Manager $project_Manager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_Manager $project_Manager)
    {
        //
    }
}

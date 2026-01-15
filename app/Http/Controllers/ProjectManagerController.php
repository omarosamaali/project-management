<?php

namespace App\Http\Controllers;

use App\Models\Project_Manager;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'special_request_id' => 'nullable',
            'request_id'         => 'nullable',
        ]);

        $searchCriteria = [];
        $dataToUpdate = ['user_id' => $validated['user_id']];

        if ($request->filled('request_id')) {
            $searchCriteria = ['request_id' => $request->request_id];
            // نمرر null صريحة للحقل الآخر لضمان عدم اعتراض قاعدة البيانات
            $dataToUpdate['special_request_id'] = null;
        } elseif ($request->filled('special_request_id')) {
            $searchCriteria = ['special_request_id' => $request->special_request_id];
            // نمرر null صريحة للحقل الآخر لضمان عدم اعتراض قاعدة البيانات
            $dataToUpdate['request_id'] = null;
        }

        Project_Manager::updateOrCreate(
            $searchCriteria,
            $dataToUpdate
        );

        return back()->with('success', 'تم تعيين مدير المشروع بنجاح');
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewProject;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specialRequests = SpecialRequest::where('is_project', 1)->paginate(8);
        return view('dashboard.new_project.index', compact('specialRequests'));
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
    public function show(NewProject $newProject)
    {
        $myProposal = DB::table('project_proposals')
            ->where('project_id', $newProject->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('dashboard.new_project.show', compact('newProject', 'myProposal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewProject $newProject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewProject $newProject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewProject $newProject)
    {
        //
    }

    public function storeProposal(Request $request, $id)
    {
        $request->validate([
            'budget_to' => 'required|numeric',
            'execution_time' => 'required|integer',
            'proposal_details' => 'required|string',
        ]);

        DB::table('project_proposals')->updateOrInsert(
            [
                'project_id' => $id,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'budget_to' => $request->budget_to,
                'execution_time' => $request->execution_time,
                'proposal_details' => $request->proposal_details,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'تم حفظ عرضك بنجاح، بالتوفيق!');
    }
}

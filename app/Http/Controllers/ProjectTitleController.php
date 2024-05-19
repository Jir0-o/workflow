<?php

namespace App\Http\Controllers;

use App\Models\TitleName;
use Illuminate\Http\Request;

class ProjectTitleController extends Controller
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
        return view('user.createProject');
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
        {
            $request->validate([
                'title' => 'required',
            ]);
        
    
            $project= new TitleName();
    
            $project->project_title = $request->title;
            $project->description = $request->description;
            $project->start_date = $request->start_date;
            $project->save();
        
            return redirect('asign_tasks/create')->with('success', 'Task created successfully.');
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

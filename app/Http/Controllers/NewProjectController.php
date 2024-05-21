<?php

namespace App\Http\Controllers;

use App\Models\TitleName;
use Illuminate\Http\Request;

class NewProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects= TitleName::all();
        return view('user.project.projectTitle',compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.project.newProjectCreate',);
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
            $project->end_date = $request->end_date;
            $project->save();
        
            return redirect('new_project/create')->with('success', 'Task created successfully.');
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

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects= TitleName::all();

        //count
        $runningCount = TitleName::where('status', 'in_progress')->count();
        $completedCount = TitleName::where('status', 'completed')->count();
        $droppedCount = TitleName::where('status', 'dropped')->count();

        $runningProject = TitleName::where('status', 'in_progress')->with('user','task')->get();
        $completedProject = TitleName::where('status', 'completed')->with('user','task')->get();
        $droppedProject = TitleName::where('status', 'dropped')->with('user','task')->get();
    
        return view('user.project.projectTitle', compact('runningCount', 'completedCount', 'droppedCount','runningProject','completedProject','droppedProject','projects'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.project.createProject',);
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
            
            $previousUrl = $request->input('previous_url');
            return redirect($previousUrl)->with('success', 'Task created successfully.');
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
        $project= TitleName::find($id);
        return view('user.project.editProject', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
        ]);
    

        $project= TitleName::find($id);

        $project->project_title = $request->title;
        $project->description = $request->description;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->save();

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('permission:View Project Details',['only'=>['index']]);
        $this->middleware('permission:Create Project',['only'=>['create']]);
        $this->middleware('permission:Edit Project',['only'=>['update','edit']]);
        $this->middleware('permission:Delete Project',['only'=>['destroy']]);
        $this->middleware('permission:Project Change Status',['only'=>['complete','drop','running']]);

    }
    public function index()
    {   

        $projects= TitleName::with('user')->get();

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
        $users = User::all(); 
        return view('user.project.createProject',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
        {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
            ]);
        

            $project= new TitleName();
    
            $project->project_title = $request->title;
            $project->description = $request->description;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->user_id = implode(',', $request['user_id']);
            $project->save();

            
            $previousUrl = $request->input('previous_url');
            return redirect($previousUrl)->with('success', 'Project created successfully.');
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

        $users = User::all(); 
        $project = TitleName::findOrFail($id);
        $assignedUsers = explode(',', $project->user_id);
        return view('user.project.editProject', compact('project','users','assignedUsers'));
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
        $project->user_id = implode(',', $request->user_id);
        $project->save();

        return redirect()->back()->with('success', 'Project Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = TitleName::find($id);
        $task->delete();
 
        return back()->with('success', 'Project deleted successfully.');
    }
    public function complete($id)
    {
    
        $task = TitleName::findOrFail($id);
        $task->end_by_date = Carbon::now();
        $task->status = 'completed';
        $task->save();
    
        return back()->with('success', 'Project completed successfully.');
    }
    public function drop($id)
    {
    
        $task = TitleName::findOrFail($id);
        $task->status = 'dropped';
        $task->save();
    
        return back()->with('success', 'Project Dropped successfully.');
    }
    public function running($id)
    {
    
        $task = TitleName::findOrFail($id);
        $task->end_by_date = null;
        $task->status = 'in_progress';
        $task->save();
    
        return back()->with('success', 'Project in Now running.');
    }
    
}

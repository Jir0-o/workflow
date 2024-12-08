<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\WorkPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

        $runningProject = TitleName::where('status', 'in_progress')->with('user','task')->latest()->get();
        $completedProject = TitleName::where('status', 'completed')->with('user','task')->latest()->get();
        $droppedProject = TitleName::where('status', 'dropped')->with('user','task')->latest()->get();
        $users = User::all(); 
    
        return view('user.project.projectTitle', compact('runningCount', 'completedCount', 'droppedCount','runningProject','completedProject','droppedProject','projects','users'));

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
         try {
             // Validate the request data
             $validated = $request->validate([
                 'title' => 'required|string|max:255',
                 'start_date' => 'required|date|after_or_equal:today',
                 'end_date' => 'required|date|after_or_equal:start_date',
                 'user_id' => 'required|array|min:1',
                 'user_id.*' => 'exists:users,id' 
             ]);

             $project = new TitleName();
             $project->project_title = $validated['title'];
             $project->description = $request->description ?? null;
             $project->start_date = $validated['start_date'];
             $project->end_date = $validated['end_date'];
             $project->user_id = implode(',', $validated['user_id']);
             $project->save();
     
             return response()->json([
                 'status' => true,
                 'message' => 'Project created successfully',
                 'data' => [
                     'project_id' => $project->id,
                     'title' => $project->project_title,
                     'description' => $project->description,
                     'start_date' => $project->start_date,
                     'end_date' => $project->end_date,
                     'user_ids' => $validated['user_id'],
                 ]
             ], 201); 
     
            } catch (ValidationException $e) {
                // Catch validation errors and return them with a 422 status
                return response()->json([
                    'status' => false,
                    'errors' => $e->validator->errors(),
                ], 422);
            } catch (\Exception $e) {
                Log::error('Error creating project: ' . $e->getMessage());
        
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create project',
                    'error' => $e->getMessage(),
                ], 500);
            }
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
    public function newEdit(string $id)
    {
        try {
            $users = User::all(); 
            $project = TitleName::findOrFail($id);
            $assignedUsers = explode(',', $project->user_id);
    
            // Return data as JSON
            return response()->json([
                'status' => true,
                'project' => $project,
                'users' => $users,
                'assignedUsers' => $assignedUsers
            ]);
        } catch (\Exception $e) {
            Log::error('Error editing project: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to edit project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate the request data
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'required|array|min:1',
            'user_id.*' => 'exists:users,id',
        ]);
    

        $project= TitleName::find($id);

        $project->project_title = $request->title;
        $project->description = $request->description;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->user_id = implode(',', $request->user_id);
        $project->save();

        return response()->json([
            'status' => true,
            'message' => 'Project updated successfully',
            'data' => [
                'project_id' => $project->id,
                'title' => $project->project_title,
                'description' => $project->description,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'user_ids' => $request->user_id,
            ]
        ], 201);
    } catch (ValidationException $e) {
        // Catch validation errors and return them with a 422 status
        return response()->json([
            'status' => false,
            'errors' => $e->validator->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error updating project: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Failed to update project',
            'error' => $e->getMessage(),
        ], 500);
    }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Get all tasks associated with the project title
        $titleIDs = Task::where('title_name_id', $id)->pluck('id');
    
        // Get all work plans associated with those tasks and delete them
        WorkPlan::whereIn('task_id', $titleIDs)->delete();
    
        // Delete all tasks associated with the project title
        Task::where('title_name_id', $id)->delete();
    
        // Finally, delete the project itself
        $titleName = TitleName::find($id);
        if ($titleName) {
            $titleName->delete();
        }
    
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

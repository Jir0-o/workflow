<?php

namespace App\Http\Controllers;

use App\Models\asign_task;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;

class AsignTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('permission:View Assign Task',['only'=>['index']]);
        $this->middleware('permission:Create Assign task',['only'=>['create']]);
        $this->middleware('permission:Edit Assign Task',['only'=>['edit']]);
        $this->middleware('permission:Delete Assign Task',['only'=>['destroy']]);
        $this->middleware('permission:Change Status',['only'=>['incomplete','completed','requested','pendingdate']]);

    }
    public function index()
    {   
        $tasks = Task::all();

        $startOfToday = Carbon::today();

        foreach ($tasks as $task) {
            if ($task->status == 'pending' && Carbon::parse($task->submit_date)->isBefore($startOfToday)) {
                $task->message = 'Time Expired';
                $task->status = 'incomplete';
                $task->save();
            }
            if ($task->status == 'incomplete' && Carbon::parse($task->submit_date)->isAfter($startOfToday)) {
                $task->submit_by_date = null;
                $task->status = 'pending';
                $task->save();
            }
            if ($task->status == 'incomplete' && Carbon::parse($task->submit_date)->isSameDay($startOfToday)) {
                $task->submit_by_date = null;
                $task->status = 'pending';
                $task->save();
            }
        }

  
        $users = User::all(); 
        $title = TitleName::all();
        //count
        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();
        $incompleteCount = Task::where('status', 'incomplete')->count();
        $inprogressCount = Task::where('status', 'in_progress')->count();

        $pendingTasks = Task::where('status', 'pending')->with('user','title_name')->orderBy('created_at', 'desc')->get();
        $completeTasks = Task::where('status', 'completed')->with('user','title_name')->orderBy('submit_by_date', 'desc')->get();
        $incompleteTasks = Task::where('status', 'incomplete')->with('user','title_name')->latest()->get();
        $inprogressTasks = Task::where('status', 'in_progress')->with('user','title_name')->latest()->get();
    
        return view('user.asign_task', compact('pendingTasks', 'completeTasks', 'incompleteTasks','inprogressTasks','pendingCount','incompleteCount','completeCount','inprogressCount','users','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $request->validate([
            'description' => 'required',
            'user_id' => 'required|array|min:1',
            'user_id.*' => 'exists:users,id',
        ]);
    
        foreach ($request -> user_id as $id) {
            $task = new Task();
            $task->title_name_id = $request->title;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            $task->work_status = $request->work_status;
            $task->user_id = $id;
            $task->save();

            return response()->json([
                'status' => true,
                'message' => 'Task created successfully',
                'data' => [
                    'task_id' => $task->id,
                    'title' => $task->title_name_id,
                    'description' => $task->description,
                    'submit_date' => $task->submit_date,
                    'user_id' => $id,
                ],
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error creating project: '.$e->getMessage());

        // Return a JSON error response
        return response()->json([
            'status' => false,
            'message' => 'Failed to create project',
            'error' => $e->getMessage()
        ], 500); 
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $task = Task::findOrFail($id); // Retrieve task
            $users = User::all();
            $titles = TitleName::all();
    
            return response()->json([
                'status' => true,
                'message' => 'Task data retrieved successfully',
                'data' => [
                    'tasks' => $task,
                    'users' => $users,
                    'title' => $titles,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Error retrieving task data: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve task data',
                'error' => $e->getMessage()
            ], 500);
        }
    }    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'description' => 'required',
        ]);
    
        try {
            $task = Task::findOrFail($id);
            $task->user_id = $request->task_user_id;
            $task->title_name_id = $request->title;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            if ($request->submit_by_date) {
                $task->submit_by_date = $request->submit_by_date;
            }
            $task->work_status = $request->work_status;
            if ($request->status) {
                $task->status = $request->status;
            }else{
                $task->status = 'pending';
            }
            $task->admin_message = 'Task Edited by Admin';
            $task->save();
    
            // Return JSON response for AJAX
            return response()->json([
                'status' => true,
                'message' => 'Assign Task edited successfully.',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $task = Task::find($id);
       $task->delete();

       return back()->with('success', 'Task deleted successfully.');

    }
    public function incomplete($id)
{
    $task = Task::findOrFail($id);
    $task->status = 'incomplete';
    $task->save();

    return back()->with('success', 'Task marked as Incompleted successfully.');
}
public function completed($id)
{
    $task = Task::findOrFail($id);
    $task->submit_by_date = Carbon::now();
    $task->status = 'completed';
    $task->save();

    return back()->with('success', 'Task marked as completed successfully.');
}
public function requested($id)
{
    $task = Task::findOrFail($id);
    $task->status = 'in_progress';
    $task->save();

    return back()->with('success', 'Task moved to requested successfully.');
}
public function pendingdate($id)
{
    $task = Task::findOrFail($id);
    $task->submit_date = Carbon::now();
    $task->submit_by_date = null;
    $task->status = 'pending';
    $task->save();

    return back()->with('success', 'Task marked as pending successfully.');
}

}

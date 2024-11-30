<?php

namespace App\Http\Controllers;

use App\Models\asign_task;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;

class ManageWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function __construct(){
        $this->middleware('permission:View manage work plan',['only'=>['index']]);
        $this->middleware('permission:Manage Work Create',['only'=>['create']]);
        $this->middleware('permission:Manage Work Edit',['only'=>['edit']]);
        $this->middleware('permission:Manage Work Delete',['only'=>['destroy']]);
        $this->middleware('permission:Manage Work Change Status', ['only' => ['incomplete', 'completed', 'requested', 'pendingdate']]);
        $this->middleware('permission:Manage Work Accept/Reject', ['only' => ['incomplete', 'completed', 'requested', 'pendingdate']]);
    }
    public function index()
    {   
        $tasks = WorkPlan::all();

        $startOfToday = Carbon::today();

        foreach ($tasks as $task) {
            if ($task->status == 'pending' && Carbon::parse($task->submit_date)->isBefore($startOfToday)) {
                $task->message = 'Time Expired';
                $task->status = 'incomplete';
                $task->save();

            // Find the role by name
            $role = Role::where('name', 'Super Admin')->first();
            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role not found.',
                ], 404);
            }
            // Get the authenticated user
            $authUser = Auth::user();

            // Retrieve all users with the "Super Admin" role
            $superAdminUsers = User::role($role->name)->get();

            // Create and send notifications to all "Super Admin" users
            foreach ($superAdminUsers as $superAdminUser) {
                Notification::create([ // Assuming you're using custom notifications model
                    'title' => "{$authUser->name} incompleted task",
                    'text' => "{$authUser->name} has failed to complete a task.",
                    'from_user_id' => $authUser->id,
                    'to_user_id' => $superAdminUser->id,
                    'link' => route('asign_tasks.index'),
                ]);
            }
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
        $title = Task::where('status', 'pending')->get();
        $projectTitle = TitleName::where('status', 'in_progress')->get();
        //count
        $pendingCount = WorkPlan::where('status', 'pending')->count();
        $completeCount = WorkPlan::where('status', 'completed')->count();
        $incompleteCount = WorkPlan::where('status', 'incomplete')->count();
        $inprogressCount = WorkPlan::where('status', 'in_progress')->count();

        $pendingTasks = WorkPlan::where('status', 'pending')->with('user','task')->orderBy('updated_at', 'desc')->get();
        $completeTasks = WorkPlan::where('status', 'completed')->with('user','task')->orderBy('submit_by_date', 'desc')->get();
        $incompleteTasks = WorkPlan::where('status', 'incomplete')->with('user','task')->orderBy('updated_at', 'desc')->get();
        $inprogressTasks = WorkPlan::where('status', 'in_progress')->with('user','task')->orderBy('updated_at', 'desc')->get();
    
        return view('work_plan.manage_work', compact('pendingTasks', 'completeTasks', 'incompleteTasks','inprogressTasks','pendingCount','incompleteCount','completeCount','inprogressCount','users','title','projectTitle'));
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
            'title'=> 'required',
            'user_id' => 'required|array|min:1',
            'user_id.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
    
        foreach ($request -> user_id as $id) {
            $task = new WorkPlan();
            $task->task_id = $request->title;
            $task->title_name_id = $request->projectId;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            $task->work_status = $request->work_status;
            $task->user_id = $id;
            $task->save();

            // Format the submit date
            $submitDateFormatted = Carbon::parse($request->last_submit_date)->locale('en')->isoFormat('DD MMMM YYYY');
            $notification = new Notification();
            $notification->title = 'New Work Plan has assigned to you';
            $notification->from_user_id = $user->id;
            $notification->to_user_id = $id;
            $notification->link = route('tasks.index');
            $notification->text = "You have a new Work Plan, please complete it by {$submitDateFormatted} or it will mark as incomplete.";
            $notification->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Work Plan created successfully',
            'data' => [
                'task_id' => $task->id,
                'title' => $task->title_name_id,
                'description' => $task->description,
                'submit_date' => $task->submit_date,
                'user_id' => $id,
            ],
        ]);

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
            $task = WorkPlan::findOrFail($id); // Retrieve task
            $users = User::all();
            $titles = Task::all();
    
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

        //today's date
        $currentDate = Carbon::now()->toDateString();
    
        try {
            $task = WorkPlan::findOrFail($id);
            $task->task_id = $request->title;
            $task->user_id = $request->task_user_id;
            $task->title_name_id = $request->projectId;
            $task->description = $request->description;

            if ($request->last_submit_date == $task->submit_date) {
                $task->submit_date = $currentDate;
            }else{
                $task->submit_date = $request->last_submit_date;
            }
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

            $authUser = Auth::user();
            $userId = User::find($request->task_user_id);

            Notification::create([ 
                'title' => "{$authUser->name} has edited your work plan",
                'text' => "{$authUser->name} has edited your work plan. Please check your Pending work plan tab",
                'from_user_id' => $authUser->id,
                'to_user_id' => $userId->id,
                'link' => route('asign_tasks.index'),
                ]);
    
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
       $task = WorkPlan::find($id);
       $task->delete();

       return back()->with('success', 'work deleted successfully.');

    }
    public function incomplete($id)
{
    $task = WorkPlan::findOrFail($id);
    $task->status = 'incomplete';
    $task->save();

    
    return back()->with('success', 'Work plan marked as Incompleted successfully.');
}
public function completed($id)
{
    $task = WorkPlan::findOrFail($id);
    $task->submit_by_date = Carbon::now();
    $task->status = 'completed';
    $task->save();

    return back()->with('success', 'Task marked as completed successfully.');
}
public function requested($id)
{
    $task = WorkPlan::findOrFail($id);
    $task->status = 'in_progress';
    $task->save();

    return back()->with('success', 'Task moved to requested successfully.');
}
public function pendingdate($id)
{
    $task = WorkPlan::findOrFail($id);
    $task->submit_date = Carbon::now();
    $task->submit_by_date = null;
    $task->status = 'pending';
    $task->save();

    return back()->with('success', 'Task marked as pending successfully.');
}

}

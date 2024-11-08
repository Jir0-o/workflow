<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\AssignOp\Mod;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\ModelHasRole;
use Spatie\Permission\Traits\HasRoles;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('permission:View Work Plan',['only'=>['index']]);
        $this->middleware('permission:Create Work Plan',['only'=>['create']]);
        $this->middleware('permission:Work Plan Allow Action',['only'=>['update','complete','extend','redo','cancel']]);

    }
    public function index()
    {

        $tasks = Task::all();
        $userId = auth()->id();
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
        //Create Work Plan
        $titles = TitleName::where('status', 'in_progress')->get();
        //count
        $pendingCount = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $completeCount = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $incompleteCount = Task::where('user_id', $userId)->where('status', 'incomplete')->count();
        $inprogressCount = Task::where('user_id', $userId)->where('status', 'in_progress')->count();


        $pendingTasks = Task::where('user_id', $userId)->where('status', 'pending')->with('user','title_name')->orderBy('created_at', 'desc')->get();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'completed')->with('user','title_name')->orderBy('submit_by_date', 'desc')->get();
        $incompletedTasks = Task::where('user_id', $userId)->where('status', 'incomplete')->with('user','title_name')->latest()->get();
        $requestedTasks = Task::where('user_id', $userId)->where('status', 'in_progress')->with('user','title_name')->latest()->get();
    
        return view('user.task', compact('pendingTasks', 'completedTasks', 'incompletedTasks','requestedTasks','pendingCount','completeCount','incompleteCount','inprogressCount','tasks','userId','titles'));

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
        // Validate the incoming request
        $request->validate([
            'title' => 'required', 
            'status' => 'required',
            'description' => 'required',
            'last_submit_date' => 'required|date',
        ]);
    // Find the role by name
    $role = Role::where('name', 'Super Admin')->first();

    if ($role) {
    // Get the authenticated user
    $authUser = auth()->user();
    $submitDateFormatted = Carbon::parse($request->last_submit_date)->locale('en')->isoFormat('DD MMMM YYYY');

    try {
        // Create a new task
        $task = new Task();
        $task->user_id = $authUser->id;
        $task->title_name_id = $request->title;
        $task->description = $request->description;
        $task->submit_date = $request->last_submit_date;
        $task->work_status = $request->status;
        $task->save();

        // Retrieve all users with the "Super Admin" role
        $superAdminUsers = User::role($role->name)->get();

        // Create a notification for each "Super Admin" user
        foreach ($superAdminUsers as $superAdminUser) {
            Notification::create([
                'title' => "{$authUser->name} created a new task",
                'text' => "{$authUser->name} created a new task. Last Submit Date: {$submitDateFormatted}",
                'from_user_id' => $authUser->id,
                'to_user_id' => $superAdminUser->id,
                'link' => route('asign_tasks.index'),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Task created successfully!',
            'data' => $task
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to create task!',
            'error' => $e->getMessage()
        ], 500);
    }
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
        $task = Task::find($id);
        return view('user.edit_task', compact('task','id'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
    try {
        // Validate request data
        $request->validate([
            'message' => 'required|string|max:255', // Added string type and max length for message
        ]);

        // Find the role by name
        $role = Role::where('name', 'Super Admin')->first();

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        // Get the authenticated user
        $authUser = auth()->user();

        // Find task by ID
        $task = Task::findOrFail($id); // Will automatically throw 404 if task not found
        $task->message = $request->message;
        $task->status = 'in_progress'; // Ensure the status is updated
        $task->save();

        // Retrieve all users with the "Super Admin" role
        $superAdminUsers = User::role($role->name)->get();

        // Create and send notifications to all "Super Admin" users
        foreach ($superAdminUsers as $superAdminUser) {
            Notification::create([ // Assuming you're using custom notifications model
                'title' => "{$authUser->name} requested to edit this task",
                'text' => "{$authUser->name} requested to edit this task. Please check your Requested Task tab",
                'from_user_id' => $authUser->id,
                'to_user_id' => $superAdminUser->id,
                'link' => route('asign_tasks.index'),
            ]);
        }
    
            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Edit message sent to admin successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task: ' . $e->getMessage(),
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

        // Find the role by name
        $role = Role::where('name', 'Super Admin')->first();

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        // Retrieve all users with the "Super Admin" role
        $superAdminUsers = User::role($role->name)->get();
        // Get the authenticated user
        $authUser = auth()->user();

        // Create and send notifications to all "Super Admin" users
        foreach ($superAdminUsers as $superAdminUser) {
            Notification::create([ // Assuming you're using custom notifications model
                'title' => "Task deleted by user",
                'text' => "Task deleted by user",
                'from_user_id' => $authUser->id,
                'to_user_id' => $superAdminUser->id,
                'link' => route('asign_tasks.index'),
            ]);
        }
 
        return back()->with('success', 'Task deleted successfully.');
    }
    public function complete($id)
{

    $task = Task::findOrFail($id);
    $task->submit_by_date = Carbon::now();
    $task->status = 'completed';
    $task->save();

    // Find the role by name
    $role = Role::where('name', 'Super Admin')->first();

    if (!$role) {
        return response()->json([
            'status' => false,
            'message' => 'Role not found.',
        ], 404);
    }

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    // Get the authenticated user
    $authUser = auth()->user();

    // Create and send notifications to all "Super Admin" users
    foreach ($superAdminUsers as $superAdminUser) {
        Notification::create([ // Assuming you're using custom notifications model
            'title' => "{$authUser->name} Task completed",
            'text' => "{$authUser->name} has completed his task. Please check your Completed Task tab",
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('asign_tasks.index'),
        ]);
    }
    

    return back()->with('success', 'Task marked as completed successfully.');
}

public function extend($id)
{
    $task = Task::findOrFail($id);

    $task->status = 'in_progress';
    $task->message = 'Requested to extend time';
    $task->save();

    // Find the role by name
    $role = Role::where('name', 'Super Admin')->first();

    if (!$role) {
        return response()->json([
            'status' => false,
            'message' => 'Role not found.',
        ], 404);
    }

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    // Get the authenticated user
    $authUser = auth()->user();

    // Create and send notifications to all "Super Admin" users
    foreach ($superAdminUsers as $superAdminUser) {
        Notification::create([ // Assuming you're using custom notifications model
            'title' => "{$authUser->name} requested to extend task",
            'text' => "{$authUser->name} requested to extend this task. Please check your Requested Task tab",
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('asign_tasks.index'),
        ]);
    }

    return back()->with('success', 'Task extend request send successfully.');
}
public function redo($id)
{
    $task = Task::findOrFail($id);
    $task->submit_date = Carbon::now();
    $task->submit_by_date = null;
    $task->status = 'pending';
    $task->message = 'Task re-opned';
    $task->save();

    // Find the role by name
    $role = Role::where('name', 'Super Admin')->first();

    if (!$role) {
        return response()->json([
            'status' => false,
            'message' => 'Role not found.',
        ], 404);
    }

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    // Get the authenticated user
    $authUser = auth()->user();

    // Create and send notifications to all "Super Admin" users
    foreach ($superAdminUsers as $superAdminUser) {
        Notification::create([ // Assuming you're using custom notifications model
            'title' => "{$authUser->name} re-opened a task",
            'text' => "{$authUser->name} has re-opened a completed task. Please check your Pending Task tab",
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('asign_tasks.index'),
        ]);
    }

    return back()->with('success', 'Task marked as pending successfully.');
}
public function cancel($id)
{
    $task = Task::findOrFail($id);
    $task->message = 'Request Cancel';
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

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    // Get the authenticated user
    $authUser = auth()->user();

    // Create and send notifications to all "Super Admin" users
    foreach ($superAdminUsers as $superAdminUser) {
        Notification::create([ // Assuming you're using custom notifications model
            'title' => "{$authUser->name} canceled request task",
            'text' => "{$authUser->name} has canceled a requested. Please check your Task tab",
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('asign_tasks.index'),
        ]);
    }

    return back()->with('success', 'Request canceled successfully.');
}
public function incompleted($id)
{
    $task = Task::findOrFail($id);
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

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    // Get the authenticated user
    $authUser = auth()->user();

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

    return back()->with('success', 'Task incompleted .');
}
}

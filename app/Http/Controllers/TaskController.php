<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
        $this->middleware('permission:View Task Details',['only'=>['index']]);
        $this->middleware('permission:Create Task Details',['only'=>['create']]);
        $this->middleware('permission:Task Details Allow Action',['only'=>['update','complete','extend','redo','cancel']]);

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

        //dropdown task
        $taskDropdown = WorkPlan::all();
        //count
        $pendingCount = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $completeCount = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $incompleteCount = Task::where('user_id', $userId)->where('status', 'incomplete')->count();
        $inprogressCount = Task::where('user_id', $userId)->where('status', 'in_progress')->count();



        $pendingTasks = Task::where('user_id', $userId)->where('status', 'pending')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'completed')->with('user','title_name')->orderBy('submit_by_date', 'desc')->get();
        $incompletedTasks = Task::where('user_id', $userId)->where('status', 'incomplete')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
        $requestedTasks = Task::where('user_id', $userId)->where('status', 'in_progress')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
    
        return view('user.task', compact('pendingTasks', 'completedTasks', 'incompletedTasks','requestedTasks','pendingCount','completeCount','incompleteCount','inprogressCount','tasks','userId','titles','taskDropdown'));

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
            'title' => 'required|exists:title_names,id',
            'task_title' => 'required',
            'description' => 'required',
            'last_submit_date' => 'required|date|after_or_equal:today',
        ]);
    // Retrieve the project using the ID from the `title` field
    $project = TitleName::find($request->title);

    // Check if the last_submit_date is greater than the project_end_date
    if (Carbon::parse($request->last_submit_date)->greaterThan(Carbon::parse($project->end_date))) {
        return response()->json([
            'status' => false,
            'errors' => ['last_submit_date' => ['The last submit date cannot be more than the project\'s end date. End date: ' .  Carbon::parse($project->end_date)->format('j F Y')]],
        ], 422);
    }
    // Find the role by name
    $role = Role::where('name', 'Super Admin')->first();

    if ($role) {
    // Get the authenticated user
    $authUser = auth()->user();
    $submitDateFormatted = Carbon::parse($request->last_submit_date)->locale('en')->isoFormat('DD MMMM YYYY');

    try {
        // Create a new task
        $task = new Task();
        $task->task_title = $request->task_title;
        $task->user_id = $authUser->id;
        $task->title_name_id = $request->title;
        $task->description = $request->description;
        $task->submit_date = $request->last_submit_date;
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
    } catch (ValidationException $e) {
        return response()->json([
            'status' => false,
            'errors' => $e->validator->errors(),
        ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create task',
                'error' => $e->getMessage(),
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
            'message' => 'required|string|max:255',
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
        } catch (ValidationException $e) {
            // Catch validation errors and return them with a 422 status
            return response()->json([
                'status' => false,
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task',
                'error' => $e->getMessage(),
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
    $startTime = Carbon::parse($task->created_at);
    $currentDateTime = Carbon::now();

    $totalDuration = $startTime->diffInSeconds($currentDateTime);

    // Convert seconds to H:i:s format
    $hours = floor($totalDuration / 3600);
    $minutes = floor(($totalDuration % 3600) / 60);
    $seconds = $totalDuration % 60;
    $hoursHours = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

    $task->submit_by_date = Carbon::now();
    $task->status = 'completed';
    $task->work_hour = $hoursHours;
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

public function extend(Request $request, $id)
{
    $task = Task::findOrFail($id);
    $extension_reason = $request->input('extension_reason');

    // Get the current message and append the new reason
    $currentReasons = $task->reason_message ? explode('|', $task->reason_message) : [];
    $currentReasons[] = $extension_reason;

    // Save the updated message with the appended reason
    $task->status = 'in_progress';
    $task->reason_message = implode('|', $currentReasons);
    $task->save();

    $role = Role::where('name', 'Super Admin')->first();
    if (!$role) {
        return response()->json([
            'status' => false,
            'message' => 'Role not found.',
        ], 404);
    }

    // Retrieve all users with the "Super Admin" role
    $superAdminUsers = User::role($role->name)->get();
    $authUser = auth()->user();

    // Create and send notifications to all "Super Admin" users
    foreach ($superAdminUsers as $superAdminUser) {
        Notification::create([
            'title' => "{$authUser->name} requested to extend task",
            'text' => "{$authUser->name} requested to extend this task. Please check your Requested Task tab",
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('asign_tasks.index'),
        ]);
    }

    // Return JSON response for AJAX
    return response()->json([
        'status' => true,
        'message' => 'Task extend request sent successfully.',
    ]);
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

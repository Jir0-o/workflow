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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;

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

  
        $users = User::where('two_factor_recovery_codes', 1)->get();
        $title = TitleName::all();
        //count
        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();
        $incompleteCount = Task::where('status', 'incomplete')->count();
        $inprogressCount = Task::where('status', 'in_progress')->count();

        $pendingTasks = Task::where('status', 'pending')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
        $completeTasks = Task::where('status', 'completed')->with('user','title_name')->orderBy('submit_by_date', 'desc')->get();
        $incompleteTasks = Task::where('status', 'incomplete')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
        $inprogressTasks = Task::where('status', 'in_progress')->with('user','title_name')->orderBy('updated_at', 'desc')->get();
    
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
            // Validation with automatic response for failures
            $request->validate([
                'description' => 'required',
                'task_title' => 'required',
                'title' => 'required|exists:title_names,id',
                'last_submit_date' => 'required|date|after_or_equal:today',
                'attachment' => 'nullable|max:2048',
                'user_id' => 'required|array|min:1',
                'user_id.*' => 'exists:users,id',
            ]);

            $attachments = [];
            $attachmentNames = [];
            
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $randomNumber = rand(1000, 9999);
                    $extension = $file->getClientOriginalExtension();
            
                    $filename = $originalName . '_' . $randomNumber . '.' . $extension;
                    $filePath = 'storage/attachment/' . $filename;
            
                    // Save file
                    $file->move(public_path('storage/attachment'), $filename);
            
                    // Add to arrays
                    $attachments[] = $filePath;
                    $attachmentNames[] = $originalName . '.' . $extension;
                }
            }

            // Retrieve the project using the ID from the `title` field
            $project = TitleName::find($request->title);

            // Check if the last_submit_date is greater than the project_end_date
            if (Carbon::parse($request->last_submit_date)->greaterThan(Carbon::parse($project->end_date))) {
                return response()->json([
                    'status' => false,
                    'errors' => ['last_submit_date' => ['The last submit date cannot be more than the project\'s end date. End date: ' .  Carbon::parse($project->end_date)->format('j F Y')]],
                ], 422);
            }
    
            $user = Auth::user();
    
            // Task creation
            $task = new Task();
            $task->task_title = $request->task_title;
            $task->title_name_id = $request->title;
            $task->description = $request->description; 
            $task->submit_date = $request->last_submit_date;
            $task->attachment = json_encode($attachments); 
            $task->attachment_name = json_encode($attachmentNames); 
            $task->user_id = implode(',', $request->user_id);
            $task->save();
    
            // Notifications
            foreach ($request->user_id as $id) {
                $submitDateFormatted = Carbon::parse($request->last_submit_date)->locale('en')->isoFormat('DD MMMM YYYY');
                $notification = new Notification();
                $notification->title = 'New Task has been assigned to you';
                $notification->from_user_id = $user->id;
                $notification->to_user_id = $id;
                $notification->link = route('tasks.index');
                $notification->text = "You have a new task, please complete it by {$submitDateFormatted} or it will be marked as incomplete.";
                $notification->save();
            }
    
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
    
        } catch (ValidationException $e) {
            // Catch validation errors and return them with a 422 status
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
            $users = User::where('two_factor_recovery_codes', 1)->get();
            $titles = TitleName::all();
            $assignedUsers = explode(',', $task ->user_id);
            
            return response()->json([ 
                'status' => true,
                'message' => 'Task data retrieved successfully',
                'data' => [
                    'tasks' => $task,
                    'users' => $users,
                    'title' => $titles,
                    'assignedUsers'=> $assignedUsers
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
            'task_title' => 'required',
            'title'=> 'required|exists:title_names,id',
            'user_id' => 'required|array',
            'user_id.*' => 'integer|exists:users,id',
            'last_submit_date'=> 'required|date',
            'attachment' => 'nullable|max:2048',
            'attachment.*' => 'nullable|max:2048', 
            'currentAttachments' => 'nullable', 
        ]);
    
        // Today's date
        $currentDate = Carbon::now()->toDateString();
    
        // Retrieve the project using the ID from the `title` field
        $project = TitleName::find($request->title);
    
        // Check if the last_submit_date is greater than the project_end_date
        if (Carbon::parse($request->last_submit_date)->greaterThan(Carbon::parse($project->end_date))) {
            return response()->json([
                'status' => false,
                'errors' => ['last_submit_date' => ['The last submit date cannot be more than the project\'s end date. End date: ' .  Carbon::parse($project->end_date)->format('j F Y')]],
            ], 422);
        }
    
        try {
            $task = Task::find($id);
    
            // Decode current attachments and their names
            $currentAttachments = json_decode($request->currentAttachments, true) ?? [];
            $currentAttachmentNames = json_decode($task->attachment_name, true) ?? [];
            $projectAttachments = [];
            $attachmentNames = [];
    
            // Ensure the "storage/attachment" directory exists
            $attachmentDir = public_path('storage/attachment');
            if (!File::exists($attachmentDir)) {
                File::makeDirectory($attachmentDir, 0777, true, true);
            }
    
            // Handle new attachments upload
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    // Generate a unique filename to avoid overwriting
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $randomNumber = rand(1000, 9999);
                    $extension = $file->getClientOriginalExtension();
    
                    $filename = $originalName . '_' . $randomNumber . '.' . $extension;
                    $attachmentPath = 'storage/attachment/' . $filename;
    
                    // Move the file to the directory
                    $file->move(public_path('storage/attachment'), $filename);
    
                    $projectAttachments[] = $attachmentPath;

                    $attachmentNames[] = $originalName . '.' . $extension;
                }
            }
    
            // Add existing attachments and their original names to the updated list
            $projectAttachments = array_merge($projectAttachments, $currentAttachments);
            $attachmentNames = array_merge($attachmentNames, $currentAttachmentNames);
    
            // Remove old files that are no longer in the current attachments
            $deletedFiles = array_diff(json_decode($task->attachment, true) ?? [], $projectAttachments);
            foreach ($deletedFiles as $deletedFile) {
                $filePath = public_path($deletedFile);
                if (file_exists($filePath)) {
                    unlink($filePath); 
                }
            }
    
            $task->task_title = $request->task_title;
            // Convert the array of user IDs to a comma-separated string
            if (is_array($request->user_id)) {
                $task->user_id = implode(',', $request->user_id);
            } else {
                $task->user_id = $request->user_id; 
            }
            $task->title_name_id = $request->title;
            $task->description = $request->description;
    
            if ($request->last_submit_date == $task->submit_date) {
                $task->submit_date = $currentDate;
            } else {
                $task->submit_date = $request->last_submit_date;
            }
            if ($request->submit_by_date) {
                $task->submit_by_date = $request->submit_by_date;
            }
            if ($request->status) {
                $task->status = $request->status;
            } else {
                $task->status = 'pending';
            }
            $task->attachment = json_encode($projectAttachments); 
            $task->attachment_name = json_encode($attachmentNames);
            $task->admin_message = 'Task Edited by Admin';
            $task->save();    

            $authUser = Auth::user();
            $users = User::find($request->user_id);
            
            foreach ($users as $user) {
                Notification::create([
                    'title' => "{$authUser->name} Task edited your task",
                    'text' => "{$authUser->name} has edited your task. Please check your Pending Task tab",
                    'from_user_id' => $authUser->id,
                    'to_user_id' => $user->id, // Use individual user ID
                    'link' => route('asign_tasks.index'),
                ]);
            }
    
            // Return JSON response for AJAX
            return response()->json([
                'status' => true,
                'message' => 'Assign Task edited successfully.',
                'data' => $task
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
    
        $workPlan = WorkPlan::where('task_id', $id)->get();
        foreach ($workPlan as $work) {
            $work->delete();
        }

       $task = Task::find($id);
       $deleteAttachment = json_decode($task->attachment, true) ?? [];

       foreach ($deleteAttachment as $deletedFile) {
        $filePath = public_path($deletedFile);
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }
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
    $startTime = Carbon::parse($task->created_at);
    $currentDateTime = Carbon::now();

    $totalDurationInSeconds = (int) $startTime->diffInSeconds($currentDateTime);

    $maxMysqlTimeInSeconds = 838 * 3600 + 59 * 60 + 59;
    $safeDuration = min($totalDurationInSeconds, $maxMysqlTimeInSeconds);

    $hours = floor($safeDuration / 3600);
    $minutes = floor(($safeDuration % 3600) / 60);
    $seconds = $safeDuration % 60;
    $timeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

    $task->submit_by_date = $currentDateTime;
    $task->status = 'completed';
    $task->work_hour = $timeFormatted; 
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

public function submitFeedbackAsign(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'feedback' => 'nullable|string',
    ]);

    $task = Task::findOrFail($request->task_id);

    $startTime = Carbon::parse($task->submit_date);
    $currentDateTime = Carbon::now();

    $totalDurationInSeconds = (int) $startTime->diffInSeconds($currentDateTime);

    // MySQL max time value safety
    $maxMysqlTimeInSeconds = 838 * 3600 + 59 * 60 + 59;
    $safeDuration = min($totalDurationInSeconds, $maxMysqlTimeInSeconds);

    $hours = floor($safeDuration / 3600);
    $minutes = floor(($safeDuration % 3600) / 60);
    $seconds = $safeDuration % 60;
    $timeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

    $task->submit_by_date = $currentDateTime;
    $task->status = 'completed';
    $task->work_hour = $timeFormatted;
    $task->message = $request->feedback; 
    $task->save();

    return back()->with('success', 'Feedback submitted and task marked as completed.');
}

}

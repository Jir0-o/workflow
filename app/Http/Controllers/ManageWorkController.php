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
use Illuminate\Validation\ValidationException;
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
            'title'=> 'required|exists:tasks,id',
            'last_submit_date'=> 'required|date|after_or_equal:today',
            'work_status'=> 'required',
            'attachment.*' => 'nullable|file|max:2048', // Validate each file
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

        // Retrieve the task using the ID from the `title` field
        $task = Task::find($request->title);

        // Check if the last_submit_date is greater than the task_end_date
        if (Carbon::parse($request->last_submit_date)->greaterThan(Carbon::parse($task->submit_date))) {
            return response()->json([
                'status' => false,
                'errors' => ['last_submit_date' => ['The last submit date cannot be more than the task\'s end date. End date: ' .  Carbon::parse($task->submit_date)->format('j F Y')]],
            ], 422);
        }

        $user = Auth::user();
    
        foreach ($request -> user_id as $id) {
            $task = new WorkPlan();
            $task->task_id = $request->title;
            $task->title_name_id = $request->projectId;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            $task->work_status = $request->work_status;
            $task->attachment = json_encode($attachments); 
            $task->attachment_name = json_encode($attachmentNames); 
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

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating work plan: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create work plan',
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
            'title'=> 'required|exists:tasks,id',
            'last_submit_date'=> 'required|date',
            'work_status'=> 'required',
            'task_user_id'=> ' required',
            'attachment' => 'nullable|max:2048',
            'attachment.*' => 'nullable|max:2048', // Allow multiple files, each with a max size of 2MB
            'currentAttachments' => 'nullable|string', // JSON string of current attachments
        ]);

        // Retrieve the task using the ID from the `title` field
        $task = Task::find($request->title);

        // Check if the last_submit_date is greater than the task_end_date
        if (Carbon::parse($request->last_submit_date)->greaterThan(Carbon::parse($task->submit_date))) {
            return response()->json([
                'status' => false,
                'errors' => ['last_submit_date' => ['The last submit date cannot be more than the task\'s end date. End date: ' .  Carbon::parse($task->submit_date)->format('j F Y')]],
            ], 422);
        }

        //today's date
        $currentDate = Carbon::now()->toDateString();
    
        try {
            $task = WorkPlan::findOrFail($id);

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
            $task->attachment = json_encode($projectAttachments); 
            $task->attachment_name = json_encode($attachmentNames);
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
                'message' => 'Work plan edited successfully.',
                'data' => $task
            ], 200);
        } catch (ValidationException $e) {
            // Catch validation errors and return them with a 422 status
            return response()->json([
                'status' => false,
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating work: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to update work',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $task = WorkPlan::find($id);

       $deleteAttachment = json_decode($task->attachment, true) ?? [];

       foreach ($deleteAttachment as $deletedFile) {
        $filePath = public_path($deletedFile);
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }

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

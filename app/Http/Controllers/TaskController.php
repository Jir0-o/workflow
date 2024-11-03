<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TitleName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


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

        try {
            $task = new Task();
            $task->user_id = auth()->user()->id;
            $task->title_name_id = $request->title;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            $task->work_status = $request->status;
            $task->save();

            return response()->json([
                'status' => true,
                'message' => 'Task created successfully!',
                'data' => $task
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create task. ' . $e->getMessage()
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
        $task = Task::find($id);
        return view('user.edit_task', compact('task','id'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, string $id)
     {
         try {
             $request->validate([
                 'message' => 'required',
             ]);
 
             $task = Task::findOrFail($id);
             $task->message = $request->message;
             $task->status = 'in_progress';
             $task->save();
 
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
 
        return back()->with('success', 'Task deleted successfully.');
    }
    public function complete($id)
{

    $task = Task::findOrFail($id);
    $task->submit_by_date = Carbon::now();
    $task->status = 'completed';
    $task->save();

    return back()->with('success', 'Task marked as completed successfully.');
}

public function extend($id)
{
    $task = Task::findOrFail($id);

    $task->status = 'in_progress';
    $task->message = 'Requested to extend time';
    $task->save();
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

    return back()->with('success', 'Task marked as pending successfully.');
}
public function cancel($id)
{
    $task = Task::findOrFail($id);
    $task->message = 'Request Cancel';
    $task->status = 'incomplete';
    $task->save();

    return back()->with('success', 'Request canceled successfully.');
}
public function incompleted($id)
{
    $task = Task::findOrFail($id);
    $task->status = 'incomplete';
    $task->save();

    return back()->with('success', 'Task incompleted .');
}
}

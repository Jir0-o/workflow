<?php

namespace App\Http\Controllers;

use App\Models\asign_task;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

class AsignTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $tasks = Task::all();

        $startOfToday = Carbon::today();

        foreach ($tasks as $task) {
            if ($task->status == 'pending' && Carbon::parse($task->submit_date)->isBefore($startOfToday)) {
                $task->status = 'incomplete';
                $task->save();
            }
            if ($task->status == 'incomplete' && Carbon::parse($task->submit_date)->isAfter($startOfToday)) {
                $task->status = 'pending';
                $task->save();
            }
            if ($task->status == 'incomplete' && Carbon::parse($task->submit_date)->isSameDay($startOfToday)) {
                $task->status = 'pending';
                $task->save();
            }
        }

  

        //count
        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();
        $incompleteCount = Task::where('status', 'incomplete')->count();
        $inprogressCount = Task::where('status', 'in_progress')->count();

        $pendingTasks = Task::where('status', 'pending')->with('user','title')->get();
        $completeTasks = Task::where('status', 'completed')->with('user','title')->get();
        $incompleteTasks = Task::where('status', 'incomplete')->with('user','title')->get();
        $inprogressTasks = Task::where('status', 'in_progress')->with('user','title')->get();
    
        return view('user.asign_task', compact('pendingTasks', 'completeTasks', 'incompleteTasks','inprogressTasks','pendingCount','incompleteCount','completeCount','inprogressCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all(); 
        $title = TitleName::all();
        return view('user.create_task', compact('users','title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'user_id' => 'required|array|min:1',
            'user_id.*' => 'exists:users,id',
        ]);
    
        foreach ($request -> user_id as $id) {
            $task = new Task();
            $task->title_id = $request->title;
            $task->description = $request->description;
            $task->submit_date = $request->last_submit_date;
            $task->user_id = $id;
            $task->save();
        }

        // $task = new Task();

        // $task->user_id = $request->user_id;
        // $task->title_id = $request->title;
        // $task->description = $request->description;
        // $task->submit_date = $request->last_submit_date;
        // $task->save();
    
        return back()->with('success', 'Task created successfully.');
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

        $users = User::all();
        $tasks = Task::find($id);
        return view('user.edit_asign', compact('tasks','id','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'description' => 'required',

        ]);
    
        $task = Task::findOrFail($id);

        $task->user_id = $request->task_user_id;
        $task->description = $request->description;
        $task->submit_date = $request->last_submit_date;
        $task->status = $request->status;
        $task->save();
    
        return back()->with('success', 'Task created successfully.');
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

    return back()->with('success', 'Task marked as completed successfully.');
}
}

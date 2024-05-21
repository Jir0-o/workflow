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
    public function index()
    {

        $tasks = Task::all();
        $userId = auth()->id();
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
        }
        //count
        $pendingCount = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $completeCount = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $incompleteCount = Task::where('user_id', $userId)->where('status', 'incomplete')->count();
        $inprogressCount = Task::where('user_id', $userId)->where('status', 'in_progress')->count();


        $pendingTasks = Task::where('user_id', $userId)->where('status', 'pending')->with('user','title')->get();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'completed')->with('user','title')->get();
        $incompletedTasks = Task::where('user_id', $userId)->where('status', 'incomplete')->with('user','title')->get();
        $requestedTasks = Task::where('user_id', $userId)->where('status', 'in_progress')->with('user','title')->get();
    
        return view('user.task', compact('pendingTasks', 'completedTasks', 'incompletedTasks','requestedTasks','pendingCount','completeCount','incompleteCount','inprogressCount'));

    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = TitleName::all();
        return view('user.user_create_task', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        $task = new Task();
        $task->user_id = auth()->user()->id;
        $task->title_id = $request->title;
        $task->description = $request->description;
        $task->submit_date = $request->last_submit_date;
        $task->save();
    
        return redirect('/tasks')->with('success', 'Task created successfully.');
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
        $request->validate([
            'message' => 'required',
        ]);
    
        $task = Task::findOrFail($id);

        $task->message = $request->message;
        $task->status = 'in_progress';
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
    public function complete($id)
{

    $task = Task::findOrFail($id);
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

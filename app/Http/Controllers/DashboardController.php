<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todayDate = Carbon::today();
        $tasks = Task::all();
        $user = auth()->user();
        $userId = auth()->id();
        $Today = Carbon::today();
        
        $activeUsers = DetailLogin::where('user_id', $userId)->get();

        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();

        $pendingUserTasks = Task::where('status', 'pending')->where('user_id', $userId)->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeUserTasks = Task::where('status', 'completed')->where('user_id', $userId)->with('user')->orderBy('submit_by_date', 'desc')->get();

        $pendingAdminTasks = Task::where('status', 'pending')->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeAdminTasks = Task::where('status', 'completed')->with('user')->orderBy('submit_by_date', 'desc')->get();

        $notice = Notice::where('status', 0)->get();
        $noticeUser = Notice::where('status', 0)->where('user_id', $userId)->get();

        $noticeDate = Notice::all();

        foreach ($noticeDate as $noticeDates) {
            $endDate = $noticeDates->end_date ? Carbon::parse($noticeDates->end_date) : null;
        
            if (($endDate && Carbon::now()->startOfDay()->isAfter($endDate->endOfDay())) || is_null($endDate)) {
                $noticeDates->status = 1;
            } else {
                $noticeDates->status = 0;
            }
        
            $noticeDates->save();
        }

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

        //workPlan Task Incomplete
        $Worktasks = WorkPlan::all();
        foreach ($Worktasks as $task) {
            if ($task->status == 'pending' && Carbon::parse($task->submit_date)->isBefore($startOfToday)) {
                $task->message = 'Time Expired';
                $task->status = 'incomplete';
                $task->save();
            // Find the role by name
            $Workrole = Role::where('name', 'Super Admin')->first();
            if (!$Workrole) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role not found.',
                ], 404);
            }
            // Get the authenticated user
            $authWorkUser = Auth::user();

            // Retrieve all users with the "Super Admin" role
            $superWorkAdminUsers = User::role($Workrole->name)->get();

            // Create and send notifications to all "Super Admin" users
            foreach ($superWorkAdminUsers as $superAdminUser) {
                Notification::create([ // Assuming you're using custom notifications model
                    'title' => "{$authWorkUser->name} incompleted A workplan",
                    'text' => "{$authWorkUser->name} has failed to complete a workplan.",
                    'from_user_id' => $authWorkUser->id,
                    'to_user_id' => $superAdminUser->id,
                    'link' => route('manage_work.index'),
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


        return view('dashboard',compact('tasks','pendingCount','completeCount','pendingUserTasks','completeUserTasks','pendingAdminTasks','completeAdminTasks','user','activeUsers','notice','noticeUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

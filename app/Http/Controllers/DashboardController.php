<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\LoginInfo;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $notice = Notice::where('status', 0)->with('user')->get();

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

        // Count the number of all card in dashboard

        $countTotalLoginUser = DetailLogin::whereDate('login_date', $todayDate)->count();
        $pendingCount = Task::where('status', 'pending')->count();
        $runningWork = WorkPlan::where('status', 'pending')->count();
        $runningProject = TitleName::where('status', 'in_progress')->count();

        $userIds = User::pluck('id')->unique();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek(); 
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // User IDs in DetailsLogin and User
        $detailsLoginUserIds = DetailLogin::whereDate('login_date', $todayDate)
        ->pluck('user_id')
        ->unique();
        // Missing user IDs in DetailsLogin and User
        $missingInUsers = $detailsLoginUserIds->diff($userIds);
        $missingInDetailsLogins = $userIds->diff($detailsLoginUserIds);
        
        $missingInUsersCount = User::whereIn('id', $missingInDetailsLogins)->count(); 

        //To Do table data

        $WorkingData = WorkPlan::where('status', 'pending')->with('user')->latest()->get();

        // Users who do not have any work plan

        // Get IDs of users with pending work plans
        $userIdsWithPendingWork = WorkPlan::where('status', 'pending')->pluck('user_id');

        // Fetch users without any pending work plans
        $usersWithoutWorkPlan = User::whereNotIn('id', $userIdsWithPendingWork)->get();

        //today total WorkHour
        $workingHourToday = LoginInfo::whereDate('login_date', $startOfToday)
        ->with('user') // Eager load user relationships
        ->select('user_id', DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(login_hour))) as total_hours'))
        ->groupBy('user_id') // Group by user
        ->get();
    
        // Convert total_hours into human-readable format
        foreach ($workingHourToday as $record) {
            if ($record->total_hours && strpos($record->total_hours, ':') !== false) {
                // Split HH:mm:ss
                $time = explode(':', $record->total_hours);
        
                // Ensure the correct format
                $record->formatted_hours = (int)$time[0] . ' hour' . ((int)$time[0] > 1 ? 's' : '') . ' ' .
                                            (int)$time[1] . ' minute' . ((int)$time[1] > 1 ? 's' : '');
            } else {
                // Handle the case when total_hours is empty or invalid
                $record->formatted_hours = 'No Data Available'; // or any default value you prefer
            }
        }

        // Weekly working hours grouped by user
        $weeklyWorkingHours = LoginInfo::whereBetween('login_date', [$startOfWeek, $endOfWeek])
        ->with('user')
        ->select('user_id', DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(login_hour))) as total_hours'))
        ->groupBy('user_id')
        ->get();

        // Monthly working hours grouped by user
        $monthlyWorkingHours = LoginInfo::whereBetween('login_date', [$startOfMonth, $endOfMonth])
        ->with('user')
        ->select('user_id', DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(login_hour))) as total_hours'))
        ->groupBy('user_id')
        ->get();

        // Format the time into human-readable hours and minutes for weekly working hours
        foreach ($weeklyWorkingHours as $record) {
            // Check if total_hours exists and is not empty
            if ($record->total_hours && strpos($record->total_hours, ':') !== false) {
                // Split HH:mm:ss
                $time = explode(':', $record->total_hours);

                // Ensure the correct format
                $record->formatted_hours = (int)$time[0] . ' hour' . ((int)$time[0] > 1 ? 's' : '') . ' ' .
                                            (int)$time[1] . ' minute' . ((int)$time[1] > 1 ? 's' : '');
            } else {
                // Handle the case when total_hours is empty or invalid
                $record->formatted_hours = 'No Data Available'; // or any default value you prefer
            }
        }

        // Format the time into human-readable hours and minutes for monthly working hours
        foreach ($monthlyWorkingHours as $record) {
            // Check if total_hours exists and is not empty
            if ($record->total_hours && strpos($record->total_hours, ':') !== false) {
                // Split HH:mm:ss
                $time = explode(':', $record->total_hours);

                // Ensure the correct format
                $record->formatted_hours = (int)$time[0] . ' hour' . ((int)$time[0] > 1 ? 's' : '') . ' ' .
                                            (int)$time[1] . ' minute' . ((int)$time[1] > 1 ? 's' : '');
            } else {
                // Handle the case when total_hours is empty or invalid
                $record->formatted_hours = 'No Data Available'; // or any default value you prefer
            }
        }

        // Count card For Auth User Only
        $pendingAuthCount = Task::where('status', 'pending')->where('user_id', auth()->user()->id)->count();
        $runningAuthWork = WorkPlan::where('status', 'pending')->where('user_id', auth()->user()->id)->count();
        $runningAuthProject = TitleName::where('status', 'in_progress')->where('user_id', auth()->user()->id)->count();

        $WorkingAuthData = WorkPlan::where('status', 'pending')->with('user')->where('user_id', auth()->user()->id)->latest()->get();

        //Project Hour Table work

        $allProjects = TitleName::all();
        $tasksHours = [];
    
        // Helper function to convert decimal hours to HH:MM:SS
        $convertToTimeFormat = function ($decimalHours) {
            $hours = floor($decimalHours);
            $minutes = floor(($decimalHours - $hours) * 60);
            $seconds = floor((($decimalHours - $hours) * 60 - $minutes) * 60);
    
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        };
    
        foreach ($allProjects as $project) {
            $tasks = Task::where('title_name_id', $project->id)
                ->with('user') // Load related users
                ->get();
    
            $convertToHours = function ($time) {
                if (!$time) return 0;
                $parts = explode(':', $time);
                return $parts[0] + ($parts[1] / 60) + ($parts[2] / 3600);
            };
    
            $completedDecimalHours = $tasks->where('status', 'completed')->sum(function ($task) use ($convertToHours) {
                return $convertToHours($task->work_hour);
            });
    
            // Calculate pending hours by comparing least submit_date with now
            $pendingDecimalHours = $tasks->where('status', 'pending')->sum(function ($task) use ($convertToHours) {
                $submitDate = Carbon::parse($task->submit_date);
                $now = Carbon::now();
                $diffInHours = $now->diffInHours($submitDate); 
                return $convertToHours($task->work_hour) + $diffInHours; 
            });
    
            $today = Carbon::today();
            $todayDecimalHours = $tasks->where('submit_by_date', '>=', $today)->sum(function ($task) use ($convertToHours) {
                return $convertToHours($task->work_hour);
            });

            // Handle user IDs as a comma-separated string
            $userIds = explode(',', $project->user_id); 
            $users = User::whereIn('id', $userIds)->get(); 
        
            $tasksHours[] = [
                'project_name' => $project->project_title,
                'users' => $users,
                'completed_hours' => $convertToTimeFormat($completedDecimalHours),
                'pending_hours' => $convertToTimeFormat($pendingDecimalHours),
                'today_work_hours' => $convertToTimeFormat($todayDecimalHours),
            ];
        }

        return view('dashboard',compact('tasks','pendingCount','completeCount','pendingUserTasks','completeUserTasks','pendingAdminTasks','completeAdminTasks','user','activeUsers',
        'countTotalLoginUser','notice','pendingCount','missingInUsersCount','runningWork','runningProject','WorkingData','usersWithoutWorkPlan','workingHourToday','weeklyWorkingHours','monthlyWorkingHours',
        'pendingAuthCount','runningAuthWork','runningAuthProject','WorkingAuthData','tasksHours'));
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

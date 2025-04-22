<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\LoginInfo;
use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WorkingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id(); // Get the logged-in user ID
    

        $rankings = WorkPlan::select('user_id')
            ->selectRaw('COUNT(id) as task_count, COUNT(id) * 10 as total_points')
            ->where('status', 'completed')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();

        $userRanks = [];
        $userPoints = [];
        foreach ($rankings as $index => $rank) {
            $userRanks[$rank->user_id] = $index + 1; 
            $userPoints[$rank->user_id] = $rank->total_points; 
        }
    
        // Get the logged-in user's rank and points
        $userRank = $userRanks[$userId] ?? 'Unranked';
        $userPoints = $userPoints[$userId] ?? 0; 

        $loginRank = LoginInfo::select('user_id')
        ->selectRaw('COUNT(DISTINCT DATE(created_at)) as login_days, COUNT(DISTINCT DATE(created_at)) * 1 as total_points')
        ->groupBy('user_id')
        ->orderByDesc('total_points')
        ->get();
    
        $loginRanks = [];
        $loginPoints = [];
        foreach ($loginRank as $index => $rank) {
            $loginRanks[$rank->user_id] = $index + 1; 
            $loginPoints[$rank->user_id] = $rank->total_points; 
        }
        
        // Get the logged-in user's rank and points
        $loginRank = $loginRanks[$userId] ?? 'Unranked';
        $loginPoints = $loginPoints[$userId] ?? 0;
        

        $totalWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $totalTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $totalProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->count();

        $DailyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereDate('submit_by_date', today())->count();
        $DailyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereDate('submit_by_date', today())->count();
        $DailyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereDate('end_by_date', today())
            ->count();

        $WeeklyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $WeeklyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $WeeklyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        
        $MonthlyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $MonthlyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $MonthlyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $YearlyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfYear(), now()->endOfYear()])->count();
        $YearlyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfYear(), now()->endOfYear()])->count();
        $YearlyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfYear(), now()->endOfYear()])
            ->count();

        $incompleteWorkPlanCount = WorkPlan::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteTaskCount = Task::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteProjectCount = TitleName::where('status', 'incomplete')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->count();

        $pendingWorkPlanCount = WorkPlan::where('status', 'pending')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteWorkPlanCount = WorkPlan::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();


        //Login Details Start

        $todayLogins = DetailLogin::where('user_id', $userId)->whereDate('login_date', today())->get();

        $onTimeStart = Carbon::createFromTime(10, 0, 0, 'Asia/Dhaka')->format('H:i:s'); // 10:00 AM

        // Date Ranges
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $today = Carbon::today();
        
        // Weekly Count
        $onTimeLoginCountWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();
        
        // Monthly Count
        $onTimeLoginCountMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();
        
        // Yearly Count
        $onTimeLoginCountYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();

        // Weekly Average Login Time
        $averageLoginHourWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        // Monthly Average Login Time
        $averageLoginHourMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        // Yearly Average Login Time
        $averageLoginHourYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        //Weekly Login Count
        $weeklyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfWeek, $today])
        ->count();

        //Monthly Login Count
        $monthlyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfMonth, $today])
        ->count();

        //Yearly Login Count
        $yearlyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfYear, $today])
        ->count();

        // Weekly Login Days
        $weeklyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->distinct('login_date')
            ->count('login_date');

        // Monthly Login Days
        $monthlyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->distinct('login_date')
            ->count('login_date');

        // Yearly Login Days
        $yearlyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->distinct('login_date')
            ->count('login_date');


        $previousLoginTime = LoginInfo::where('user_id', $userId)
        ->whereDate('login_date', '<', today())
        ->first();

        //Login Details End

        //User Info Start

        $userInfo = UserDetail::where('user_id', $userId)->first();

        //user login log start
        $dailyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereDate('login_date', today())
        ->orderBy('created_at', 'desc') 
        ->get();

        $weeklyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfWeek, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        $monthlyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfMonth, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        $yearlyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfYear, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        //end user login log

        //start completed task

        $TodayCompletedTask = Task::where('status', 'completed') 
        ->whereDate('submit_by_date', today())
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('submit_by_date', 'desc') 
        ->get();

        $WeeklyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfWeek, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('submit_by_date', 'desc') 
        ->get();

        $MonthlyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfMonth, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('submit_by_date', 'desc') 
        ->get();

        $YearlyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfYear, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('submit_by_date', 'desc') 
        ->get();

        //end completed task

        //start Completed work Plan

        $TodayCompletedWork = WorkPlan::where('status','completed')
        ->whereDate('submit_by_date', today())
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $WeeklyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfWeek, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $MonthlyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfMonth, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $YearlyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfYear, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();


        return view('profile.userProfile', compact('userRank', 'userPoints', 'loginRank', 'loginPoints', 'totalWorkPLanCount', 'totalTaskCount', 'totalProjectCount',
        'DailyWorkPLanCount', 'DailyTaskCount', 'DailyProjectCount',
        'WeeklyWorkPLanCount', 'WeeklyTaskCount', 'WeeklyProjectCount',
        'MonthlyWorkPLanCount', 'MonthlyTaskCount', 'MonthlyProjectCount',
        'YearlyWorkPLanCount', 'YearlyTaskCount', 'YearlyProjectCount',
        'incompleteWorkPlanCount', 'incompleteTaskCount', 'incompleteProjectCount',
        'pendingWorkPlanCount', 'incompleteWorkPlanCount','todayLogins',
        'onTimeLoginCountWeekly', 'lateLoginCountWeekly',
        'onTimeLoginCountMonthly', 'lateLoginCountMonthly',
        'onTimeLoginCountYearly', 'lateLoginCountYearly',
        'averageLoginHourWeekly', 'averageLoginHourMonthly', 'averageLoginHourYearly',
        'weeklyLoginCount', 'monthlyLoginCount', 'yearlyLoginCount',
        'previousLoginTime', 'userInfo','dailyUserLogin','weeklyUserLogin','monthlyUserLogin',
        'yearlyUserLogin','TodayCompletedTask','WeeklyCompletedTask','MonthlyCompletedTask',
        'YearlyCompletedTask','TodayCompletedWork','WeeklyCompletedWork','MonthlyCompletedWork',
        'YearlyCompletedWork','monthlyLoginDays','weeklyLoginDays','yearlyLoginDays'));
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
       try {
        $request->validate([
            'user_title' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'age' => 'required',
            'gender' => 'required',
            'phone' => 'nullable|min:11',
            'address' => 'required',
        ]);

        // Get the first assigned role using Spatie
        $role_name = auth()->user()->getRoleNames()->first(); 

        $details = UserDetail::create([
            'user_title' => $request->user_title,
            'email' => $request->email,
            'age' => $request->age,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => 'Bangladesh',
            'role_name' => $role_name, // Corrected role name retrieval
            'user_id' => auth()->user()->id,
            'status' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User details created successfully',
            'details' => $details,
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create user details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userId = $id;

        $userProfile = User::find($userId);
    

        // $rankings = WorkPlan::select('user_id')
        //     ->selectRaw('COUNT(id) as task_count, COUNT(id) * 10 as total_points')
        //     ->where('status', 'completed')
        //     ->groupBy('user_id')
        //     ->orderByDesc('total_points')
        //     ->get();

        // $userRanks = [];
        // $userPoints = [];
        // foreach ($rankings as $index => $rank) {
        //     $userRanks[$rank->user_id] = $index + 1; 
        //     $userPoints[$rank->user_id] = $rank->total_points; 
        // }
    
        // // Get the logged-in user's rank and points
        // $userRank = $userRanks[$userId] ?? 'Unranked';
        // $userPoints = $userPoints[$userId] ?? 0; 

        // $loginRank = LoginInfo::select('user_id')
        // ->selectRaw('COUNT(DISTINCT DATE(created_at)) as login_days, COUNT(DISTINCT DATE(created_at)) * 1 as total_points')
        // ->groupBy('user_id')
        // ->orderByDesc('total_points')
        // ->get();
    
        // $loginRanks = [];
        // $loginPoints = [];
        // foreach ($loginRank as $index => $rank) {
        //     $loginRanks[$rank->user_id] = $index + 1; 
        //     $loginPoints[$rank->user_id] = $rank->total_points; 
        // }
        
        // // Get the logged-in user's rank and points
        // $loginRank = $loginRanks[$userId] ?? 'Unranked';
        // $loginPoints = $loginPoints[$userId] ?? 0;
        

        $totalWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $totalTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $totalProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->count();

        $DailyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereDate('submit_by_date', today())->count();
        $DailyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereDate('submit_by_date', today())->count();
        $DailyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereDate('end_by_date', today())
            ->count();

        $WeeklyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $WeeklyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $WeeklyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        
        $MonthlyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $MonthlyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $MonthlyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $YearlyWorkPLanCount = WorkPlan::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfYear(), now()->endOfYear()])->count();
        $YearlyTaskCount = Task::where('status', 'completed')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->whereBetween('submit_by_date', [now()->startOfYear(), now()->endOfYear()])->count();
        $YearlyProjectCount = TitleName::where('status', 'completed')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->whereBetween('end_by_date', [now()->startOfYear(), now()->endOfYear()])
            ->count();

        $incompleteWorkPlanCount = WorkPlan::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteTaskCount = Task::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteProjectCount = TitleName::where('status', 'incomplete')
            ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
            ->count();

        $pendingWorkPlanCount = WorkPlan::where('status', 'pending')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();
        $incompleteWorkPlanCount = WorkPlan::where('status', 'incomplete')->whereRaw("FIND_IN_SET(?, user_id)", [$userId])->count();


        //Login Details Start

        $todayLogins = DetailLogin::where('user_id', $userId)->whereDate('login_date', today())->get();

        $onTimeStart = Carbon::createFromTime(10, 0, 0, 'Asia/Dhaka')->format('H:i:s'); // 10:00 AM

        // Date Ranges
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $today = Carbon::today();
        
        // Weekly Count
        $onTimeLoginCountWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();
        
        // Monthly Count
        $onTimeLoginCountMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();
        
        // Yearly Count
        $onTimeLoginCountYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '>=', $onTimeStart)
            ->count();
        
        $lateLoginCountYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('MIN(login_time) as first_login_time, login_date')
            ->groupBy('login_date')
            ->having('first_login_time', '<', $onTimeStart)
            ->count();

        // Weekly Average Login Time
        $averageLoginHourWeekly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        // Monthly Average Login Time
        $averageLoginHourMonthly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        // Yearly Average Login Time
        $averageLoginHourYearly = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(login_time))) as avg_time')
            ->value('avg_time');

        //Weekly Login Count
        $weeklyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfWeek, $today])
        ->count();

        //Monthly Login Count
        $monthlyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfMonth, $today])
        ->count();

        //Yearly Login Count
        $yearlyLoginCount = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfYear, $today])
        ->count();

        // Weekly Login Days
        $weeklyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfWeek, $today])
            ->distinct('login_date')
            ->count('login_date');

        // Monthly Login Days
        $monthlyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfMonth, $today])
            ->distinct('login_date')
            ->count('login_date');

        // Yearly Login Days
        $yearlyLoginDays = LoginInfo::where('user_id', $userId)
            ->whereBetween('login_date', [$startOfYear, $today])
            ->distinct('login_date')
            ->count('login_date');


        $previousLoginTime = LoginInfo::where('user_id', $userId)
        ->whereDate('login_date', '<', today())
        ->first();

        //Login Details End

        //User Info Start

        $userInfo = UserDetail::where('user_id', $userId)->first();

        //user login log start
        $dailyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereDate('login_date', today())
        ->orderBy('created_at', 'desc') 
        ->get();

        $weeklyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfWeek, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        $monthlyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfMonth, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        $yearlyUserLogin = LoginInfo::where('user_id', $userId)
        ->whereBetween('login_date', [$startOfYear, $today])
        ->orderBy('created_at', 'desc') 
        ->get();

        //end user login log

        //start completed task

        $TodayCompletedTask = Task::where('status','completed')
        ->whereDate('submit_by_date', today())
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('created_at', 'desc') 
        ->get();

        $WeeklyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfWeek, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('created_at', 'desc') 
        ->get();

        $MonthlyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfMonth, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('created_at', 'desc') 
        ->get();

        $YearlyCompletedTask = Task::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfYear, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'title_name')
        ->orderBy('created_at', 'desc') 
        ->get();

        //end completed task

        //start Completed work Plan

        $TodayCompletedWork = WorkPlan::where('status','completed')
        ->whereDate('submit_by_date', today())
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $WeeklyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfWeek, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $MonthlyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfMonth, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();

        $YearlyCompletedWork = WorkPlan::where('status','completed')
        ->whereBetween('submit_by_date', [$startOfYear, $today])
        ->whereRaw("FIND_IN_SET(?, user_id)", [$userId])
        ->with('user', 'task')
        ->orderBy('created_at', 'desc') 
        ->get();


        return view('profile.individualProfile', compact('totalWorkPLanCount', 'totalTaskCount', 'totalProjectCount',
        'DailyWorkPLanCount', 'DailyTaskCount', 'DailyProjectCount',
        'WeeklyWorkPLanCount', 'WeeklyTaskCount', 'WeeklyProjectCount',
        'MonthlyWorkPLanCount', 'MonthlyTaskCount', 'MonthlyProjectCount',
        'YearlyWorkPLanCount', 'YearlyTaskCount', 'YearlyProjectCount',
        'incompleteWorkPlanCount', 'incompleteTaskCount', 'incompleteProjectCount',
        'pendingWorkPlanCount', 'incompleteWorkPlanCount','todayLogins',
        'onTimeLoginCountWeekly', 'lateLoginCountWeekly',
        'onTimeLoginCountMonthly', 'lateLoginCountMonthly',
        'onTimeLoginCountYearly', 'lateLoginCountYearly',
        'averageLoginHourWeekly', 'averageLoginHourMonthly', 'averageLoginHourYearly',
        'weeklyLoginCount', 'monthlyLoginCount', 'yearlyLoginCount',
        'previousLoginTime', 'userInfo','dailyUserLogin','weeklyUserLogin','monthlyUserLogin',
        'yearlyUserLogin','TodayCompletedTask','WeeklyCompletedTask','MonthlyCompletedTask',
        'YearlyCompletedTask','TodayCompletedWork','WeeklyCompletedWork','MonthlyCompletedWork',
        'YearlyCompletedWork','userProfile','weeklyLoginDays','monthlyLoginDays','yearlyLoginDays'));
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
        try {
            // Validate the request data
            $request->validate([
                'edit_user_title' => 'required|string|max:255',
                'edit_age' => 'required|date',
                'edit_gender' => 'required|in:1,2,3',
                'edit_address' => 'required|string|max:500',
                'edit_phone' => 'nullable|string|min:11',
                'edit_con_email' => 'nullable|email|max:255',
            ]);
    
            // Find user
            $user = UserDetail::where('user_id', $id)->first();
    
            // Update user details
            $user->update([
                'user_title' => $request->edit_user_title,
                'age' => $request->edit_age,
                'gender' => $request->edit_gender,
                'address' => $request->edit_address,
                'phone' => $request->edit_phone,
                'email' => $request->edit_con_email,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'User details updated successfully!',
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity (Validation Error)
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->has('user_id')) {
            $user = User::find($request->user_id);
        }
        else {
            $user = Auth::user();
        }
    
        // Define the path
        $folderPath = public_path('storage/profile-photos/');
    
        // Create directory if not exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }
    
        // Delete old image if exists
        if ($user->profile_photo_path && File::exists(public_path('storage/' . $user->profile_photo_path))) {
            File::delete(public_path('storage/' . $user->profile_photo_path));
        }
    
        // Store new image with correct path
        $file = $request->file('profile_photo');
        $fileName = 'profile-photos/' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('storage/profile-photos/'), basename($fileName));
    
        // Update user profile path
        $user->update(['profile_photo_path' => $fileName]);
    
        return response()->json([
            'success' => true,
            'image_url' => asset('public/storage/' . $fileName)
        ]);
    }

    public function updateUsername(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->has('user_id')) {
            $user = User::find($request->user_id);
        }
        else {
            $user = Auth::user();
        }

        $user->name = $request->name;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateEmail(Request $request)
    {
        if ($request->has('user_id')) {
            $userId = $request->user_id;

            $request->validate([
                'email' => 'required|email|unique:users,email,' . $userId,
            ]);
        }
        else {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . Auth::id(),
            ]);
        }

        if ($request->has('user_id')) {
            $user = User::find($request->user_id);
            //update User email
            $user->update(['email' => $request->email]);
        }
        else {
            $user = Auth::user();
            $user->email = $request->email;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    public function updateProfile(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'profile_picture' => 'nullable|image|max:2048',
            ]);
    
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
    
    
            if ($request->hasFile('profile_picture')) {
                // Generate a unique filename
                $filename = time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
    
                // Save the file in the public/profile-photos directory
                $filePath = 'profile-photos/' . $filename;
                $request->file('profile_picture')->move(public_path('/storage/profile-photos'), $filename);
    
                $user->profile_photo_path = $filePath;
            }

            $user->save();
    
            return response()->json(['success' => 'User updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('User Update Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update user.'], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed', // Ensure 'new_password_confirmation' exists in the form
            ]);
    
            $user = User::findOrFail($id);  // Find the user by ID
    
            // Check if the current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Current password is incorrect.'], 400);
            }
    
            // Update the password
            $user->password = Hash::make($request->new_password);
            $user->save();
    
            return response()->json(['success' => 'Password updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('Password Change Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to change password.'], 500);
        }
    }

    public function getUserDetails($id)
    {
        $user = UserDetail::where('user_id', $id)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user_title' => $user->user_title, 
                    'age' => $user->age,
                    'gender' => $user->gender,
                    'address' => $user->address,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]
            ]);
        }

        return response()->json(['success' => false], 404);
    }
    public function getUserProfile($id)
    {
        $user = User::where('id', $id)->with('userDetail')->first();

        $pendingWork = WorkPlan::whereRaw("FIND_IN_SET(?, user_id)", [$id])->where('status', 'pending')->count();
        $completedTask = Task::whereRaw("FIND_IN_SET(?, user_id)", [$id])->where('status', 'completed')->count();
        $completedWork = WorkPlan::whereRaw("FIND_IN_SET(?, user_id)", [$id])->where('status', 'completed')->count();
        $loginTime = LoginInfo::whereRaw("FIND_IN_SET(?, user_id)", [$id])->orderBy('created_at', 'desc')->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user, 
                    'user_detail' => $user->userDetail,
                    'pendingWork' => $pendingWork,
                    'completedTask' => $completedTask,
                    'completedWork' => $completedWork,
                    'loginTime' => $loginTime,
                ]
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function getTotalUsers()
    {
        // Get users who have logged in (from DetailLogin)
        $loggedInUsers = DetailLogin::orderBy('name')->where('login_date', Carbon::today())->get();

        // Get users who never logged in (users NOT in DetailLogin)
        $notLoggedInUsers = User::whereNotIn('id', $loggedInUsers->pluck('user_id'))->get();

        return response()->json([
            'loggedInUsers' => $loggedInUsers,
            'notLoggedInUsers' => $notLoggedInUsers
        ]);
    }
}

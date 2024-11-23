<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\DetailsLogin;
use App\Models\LoginInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LoginDetailsController extends Controller
{
    public function __construct(){
        $this->middleware('permission:View User login Details',['only'=>['index']]);
        $this->middleware('permission:Edit Login Details',['only'=>['edit']]);
        $this->middleware('permission:Delete Login Details',['only'=>['destroy']]);
        $this->middleware('permission:View Login Report',['only'=>['loginReport']]);
        $this->middleware('permission:View Login Report',['only'=>['loginReportView']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $todayDate = Carbon::today();

        // Count of unique email addresses with today's date
        $allLoginCount = LoginInfo::where('email_address', '!=', '')
            ->where('status', 0)
            ->whereDate('login_date', $todayDate)
            ->count('email_address');
    
        // User IDs in DetailsLogin and User
        $detailsLoginUserIds = DetailLogin::whereDate('login_date', $todayDate)
            ->pluck('user_id')
            ->unique();

        //detailed logins
        // $detailedLogins = DetailLogin::pluck(column: 'user_id')->unique();

        // All logins
        $AllLogin = LoginInfo::with('user')->latest()->get();
    
        $userIds = User::pluck('id')->unique();
    
        // Missing user IDs in DetailsLogin and User
        $missingInUsers = $detailsLoginUserIds->diff($userIds);
        $missingInDetailsLogins = $userIds->diff($detailsLoginUserIds);
    
        // Missing login details in DetailsLogin and User
        $missingInUsersDetails = DetailLogin::whereIn('user_id', $missingInUsers)
            ->whereDate('login_date', $todayDate)
            ->where('status', 0)
            ->get();
    
        $missingInDetailsLoginsDetails = User::whereIn('id', $missingInDetailsLogins)->get();
        $missingInDetailsLoginsCount = $missingInDetailsLoginsDetails->count();
    
        // Login information for today
        $loginToday = LoginInfo::whereDate('login_date', $todayDate)->with('user')->latest()->get();
    
        // Latest login for each email address (subquery to get unique email logins for today)
        $subQuery = LoginInfo::select('email_address')
            ->whereDate('login_date', $todayDate)
            ->groupBy('email_address')
            ->havingRaw('MAX(created_at) = created_at');
    
        $currentLogin = LoginInfo::whereIn('email_address', $subQuery)->with('user')->latest()->get();
        $loginCount = $currentLogin->count();
    
        
    
        return view('user.logindetails.loginDetails', compact(
            'loginToday', 'loginCount', 'allLoginCount', 'AllLogin', 'currentLogin', 
            'missingInDetailsLoginsDetails', 'missingInUsersDetails', 
            'missingInDetailsLoginsCount', 'missingInUsers'
        ));
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
        try {
            // Retrieve the log data by ID
            $log = LoginInfo::findOrFail($id);
    
            return response()->json([
                'status' => true,
                'message' => 'Log data retrieved successfully',
                'data' => [
                    'log' => $log,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve log data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $log = LoginInfo::findOrFail($id);
            
            // Parse login_date and login_time
            $date = $request->input('login_date');
            $time = $request->input('login_time');
            $logoutTime = $request->input('logout_time');
            
            
            $loginDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}", 'Asia/Dhaka')->setTimezone('Asia/Dhaka');
            
           
            $logoutDateTime = null;
            if (!empty($logoutTime)) {
                
                $logoutDateTime = Carbon::createFromFormat('H:i', $logoutTime, 'Asia/Dhaka')->setTimezone('Asia/Dhaka');
            }
        
            // Update the log data
            $log->login_date = $date; 
            $log->login_time = $loginDateTime->format('H:i:s'); 
            $log->logout_time = $logoutDateTime ? $logoutDateTime->format('H:i:s') : null; 
            $log->status = $request->input('status');
            $log->save();
        
            // Update details login user data
            $detailsLogin = DetailLogin::firstOrNew(['user_id' => $log->user_id]); // Retrieve or create new DetailLogin if it doesn't exist
            $detailsLogin->login_date = $date;
            $detailsLogin->login_time = $loginDateTime->format('H:i:s');
            $detailsLogin->logout_time = $logoutDateTime ? $logoutDateTime->format('H:i:s') : null;
            $detailsLogin->status = $request->input('status');
            $detailsLogin->save();
        
            return response()->json([
                'status' => true,
                'message' => 'Log data updated successfully'
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update log data',
                'error' => $e->getMessage()
            ], 500);
        }        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $logData = LoginInfo::findOrFail($id);
            $logData->delete();
    
            session()->flash('success', 'Log data deleted successfully');
    
            return redirect()->route('login_details.index');
    
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete log data: ' . $e->getMessage());
    
            return redirect()->route('login_details.index'); 
        }
    }

    public function loginReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'title_name_id' => 'nullable',
        ]);
    
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
    
        // Initialize the query with date criteria
        $query = LoginInfo::whereBetween('created_at', [$startDate, $endDate]);
    
        // Apply additional filters if they are provided
        if ($request->filled('title_name_id')) {
            $query->where('title_name_id', $request->title_name_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
    
        $tasks = $query->orderBy('created_at', 'desc')->get();

        $users = User::all();
        $selectedUser = $request->filled('user') ? User::find($request->user) : null;
        $formattedStartDate = $startDate->format('d F Y');
        $formattedEndDate = $endDate->format('d F Y');
    
        $oldInput = $request->all();
    
        return view('user.logindetails.loginDetailsReport', [
            'tasks' => $tasks,
            'users' => User::all(),
            'selectedUser' => $selectedUser,
            'formattedStartDate' => $formattedStartDate,
            'formattedEndDate' => $formattedEndDate,
            'oldInput' => $oldInput,
        ]);
    }


    public function loginReportView(){
    $projects = LoginInfo::orderBy('created_at', 'desc')->get();
    $users = User::all();
    return view('user.logindetails.loginDetailsReport', compact('projects', 'users'));
}

}

<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('permission:View Application',['only'=>['index']]);
        $this->middleware('permission:Create Application',['only'=>['create']]);
        $this->middleware('permission:Edit Application|Edit User Application', ['only' => ['edit']]);
        $this->middleware('permission:Delete Application',['only'=>['destroy']]);
        $this->middleware('permission:Cancel Application',['only'=>['cancel']]);
        $this->middleware('permission:Return Application',['only'=>['return']]);
        $this->middleware('permission:Accept/Reject Application',['only'=>['accept','reject']]);
        $this->middleware('permission:Send Application',['only'=>['send']]);

    }
    public function index()
    {
        $applications = Application::all();
        $pendingApplications = Application::where('status', 0)->orderBy('created_at','desc')->get();
        $approvedApplications = Application::where('status', 1)->orderBy('created_at','desc')->get();
        $rejectedApplications = Application::where('status', 2)->orderBy('created_at','desc')->get();
        $returnApplications = Application::where('status',4)->orderBy('created_at','desc')->get();

        //applications count
        $pendingApplicationsCount = Application::where('status', 0)->count();
        $approvedApplicationsCount = Application::where('status', 1)->count();
        $rejectedApplicationsCount = Application::where('status', 2)->count();
        $returnApplicationsCount = Application::where('status', 4)->count();
        $applicationCount = Application::all()->count();


        //Auth user count
        $pendingAuthCount = Application::where('user_id', Auth::user()->id)->where('status', 0)->count();
        $approvedAuthCount = Application::where('user_id', Auth::user()->id)->where('status', 1)->count();
        $rejectedAuthCount = Application::where('user_id', Auth::user()->id)->where('status', 2)->count();
        $returnAuthCount = Application::where('user_id', Auth::user()->id)->where('status', 4)->count();
        $authApplicationCount = Application::where('user_id', Auth::user()->id)->count();

        //Auth user applications
        $pendingAuth = Application::where('user_id', Auth::user()->id)->where('status', 0)->orderBy('created_at','desc')->get();
        $approvedAuth = Application::where('user_id', Auth::user()->id)->where('status', 1)->orderBy('created_at','desc')->get();
        $rejectedAuth = Application::where('user_id', Auth::user()->id)->where('status', 2)->orderBy('created_at','desc')->get();
        $returnAuth = Application::where('user_id', Auth::user()->id)->where('status', 4)->orderBy('created_at','desc')->get();
        $authApplications = Application::where('user_id', Auth::user()->id)->orderBy('created_at','desc')->get();


        return view('application.show', compact('applications','pendingApplications','approvedApplications','rejectedApplications',
        'pendingApplicationsCount','approvedApplicationsCount','rejectedApplicationsCount','applicationCount',
        'pendingAuthCount','approvedAuthCount','rejectedAuthCount','authApplicationCount',
        'pendingAuth','approvedAuth','rejectedAuth','authApplications',
        'returnApplications','returnApplicationsCount','returnAuthCount','returnAuth'));
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
            // Validate incoming request
            $request->validate([
                'role' => 'required|string',
                'leave_type' => 'required|string',
                'days_requested' => 'required|integer|min:1',
                'leave_start_date' => 'required|date',
                'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
                'reason' => 'required|string',
            ]);
            $AuthUser = Auth::user();
            $todayDate = Carbon::now()->toDateString();

            // Store the application in the database
            $application = new Application();
            $application->user_id = $AuthUser->id;
            $application->date = $todayDate;
            $application->name = $AuthUser->name;
            $application->role = $request->role;
            $application->leave_type = $request->leave_type;
            $application->days_number = $request->days_requested;
            $application->from_date = $request->leave_start_date;
            $application->end_date = $request->leave_end_date;
            $application->reason = $request->reason;
            $application->email = $AuthUser->email;
            $application->status = 0;
            $application->save();

            return response()->json([
                'status' => true,
                'message' => 'Application created successfully',
                'data' => [
                    'application'=> $application 
                ],
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error creating project: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create application',
                'error' => $e->getMessage()
            ], 500); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $application = Application::findOrFail($id);
    
            return response()->json($application);
        } catch (\Exception $e) {
            Log::error('Error fetching application: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // Retrieve the application by ID or throw a 404 error
            $application = Application::findOrFail($id);
    
            return response()->json([
                'status' => true,
                'message' => 'Application fetched successfully.',
                'data' => $application
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Application not found: ID $id");
            return response()->json([
                'status' => false,
                'message' => 'Application not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching application: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch application.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate incoming request
            $request->validate([
                'role' => 'required|string',
                'leave_type' => 'required|string',
                'days_number' => 'required|integer|min:1',
                'from_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'required|string',
            ]);

            $application = Application::findOrFail($id);
            $application->role = $request->role;
            $application->leave_type = $request->leave_type;
            $application->days_number = $request->days_number;
            $application->from_date = $request->from_date;
            $application->end_date = $request->end_date;
            $application->reason = $request->reason;
            $application->save();

            return response()->json([
                'status' => true,
                'message' => 'Application updated successfully',
                'data' => [
                    'application'=> $application 
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(), // Returns all validation errors
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $application = Application::find($id);
        if ($application) {
            $application->delete();
        }
    
        return back()->with('success', 'Application deleted successfully.');
    }  
    public function accept(string $id)
    {
        try {
            $application = Application::findOrFail($id); 
            $application->status = 1; 
            $application->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Application accepted successfully',
                'application' => $application,
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting application: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to accept application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function reject(string $id)
    {
        try {
            $application = Application::findOrFail($id); 
            $application->status = 2; 
            $application->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Application rejected successfully',
                'application' => $application,
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to reject application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function cancel(string $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Application cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error cancelling application: " . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel the application.',
            ], 500);
        }
    }
    public function return(Request $request, string $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string',
            ]);

            $application = Application::findOrFail($id);
            $application->status = 4; 
            $application->return_reason = $request->reason; 
            $application->save();

            return response()->json([
                'status' => true,
                'message' => 'Application returned successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error returning application: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to return application.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function send( string $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->status = 0; 
            $application->save();

            return response()->json([
                'status' => true,
                'message' => 'Application sent successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error sending application: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send application.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

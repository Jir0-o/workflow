<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\MailAddress;
use App\Models\MailLog;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Mail\DailyTaskReportMail;
use App\Mail\MonthlyTaskReportMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $email = MailAddress::all();
        return view('mail.create',compact('email'));
    }
 
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $logs = MailLog::with('mailAddress')
                ->orderBy('created_at', 'desc')
                ->get();
        return view('mail.mailLogs',compact('logs'));
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         try {
             $validated = $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|email|max:255|unique:mail_addresses,email_address',
                 'received_time' => 'required|date_format:H:i',
             ]);
     
             $email = MailAddress::create([
                 'name' => $request->name,
                 'email_address' => $request->email,
                 'daily_report_time' => $request->received_time
             ]);
     
             return response()->json([
                 'status' => true,
                 'message' => 'Mail created successfully',
                 'data' => [
                     'id' => $email->id,
                     'name' => $email->name,
                     'email' => $email->email_address,
                 ]
             ], 201);
     
         } catch (ValidationException $e) {
             return response()->json([
                 'status' => false,
                 'errors' => $e->errors()
             ], 422); // 422 Unprocessable Entity for validation errors
         } catch (\Exception $e) {
             Log::error('Error creating mail: '.$e->getMessage());
     
             return response()->json([
                 'status' => false,
                 'message' => 'Failed to create mail',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $email = MailAddress::findOrFail($id);
    
            return response()->json([
                'status' => true,
                'email' => $email,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            Log::error('Error loading mail data: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to load mail data',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:mail_addresses,email_address,' . $id,
                'received_time' => 'required|date_format:H:i',
            ]);
    
            $email = MailAddress::findOrFail($id);
            $email->update([
                'name' => $request->name,
                'email_address' => $request->email,
                'daily_report_time' => $request->received_time,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Mail updated successfully',
                'data' => $email
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update error: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to update email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $email = MailAddress::findOrFail($id);
        $email->delete();
 
        return back()->with('success', 'Mail deleted successfully.');
    }

    public function sendDaily(Request $request)
    {
        $mailAddresses = MailAddress::pluck('email_address');

        $userIds = User::pluck('id');

        $todayDate = Carbon::today()->toDateString();

        $detailsLoginUserIds = DetailLogin::whereDate('login_date', $todayDate)
            ->pluck('user_id')
            ->unique();

        $workingUsers = User::whereIn('id', $detailsLoginUserIds)->get();
        $notWorkingUsers = User::whereIn('id', $userIds->diff($detailsLoginUserIds))->get();
    
        foreach ($mailAddresses as $email) {

            $sendEmail = MailAddress::where('email_address', $email)->first();

            $yesterdayTasks = Task::with('user')
                ->whereDate('created_at', Carbon::yesterday())
                ->get();
    
            $todayTasks = Task::with( 'user')
                ->whereDate('created_at', Carbon::today())
                ->get();
    
            $yesterdayWorkPlans = WorkPlan::with('task', 'user')
                ->whereDate('created_at', Carbon::yesterday())
                ->get();
    
            $todayWorkPlans = WorkPlan::whereDate('created_at', Carbon::today())
                ->with('task', 'user')
                ->get();
    
            if ($yesterdayTasks->count() || $todayTasks->count()) {
                Mail::to($email)->send(new DailyTaskReportMail(
                    $yesterdayTasks,
                    $todayTasks,
                    $yesterdayWorkPlans,
                    $todayWorkPlans,
                    $workingUsers,
                    $notWorkingUsers,
                ));
            }

            MailLog::create([
                'mail_address_id' => $sendEmail->id,
                'name' => $sendEmail->name,
                'mail_type' => 'Daily Task Report',
                'mail_date' => Carbon::now(),
                'status' => 1,
                'is_active' => 1
            ]);
        }
    }

    public function sendMonthly(Request $request)
    {
        $mailAddresses = MailAddress::pluck('email_address');
    
        foreach ($mailAddresses as $email) {

            $sendEmail = MailAddress::where('email_address', $email)->first();
    
            $tasks = Task::with('user')
                ->whereMonth('created_at', Carbon::now()->month)
                ->get();
    
            if ($tasks->count()) {
                Mail::to($email)->send(new MonthlyTaskReportMail($tasks));
            }
    
            MailLog::create([
                'mail_address_id' => $sendEmail->id,
                'name' => $sendEmail->name,
                'mail_type' => 'Monthly Task Report',
                'mail_date' => Carbon::now(),
                'status' => 1,
                'is_active' => 1
            ]);
        }
    }
}

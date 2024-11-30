<?php

namespace App\Listeners;

use App\Models\DetailLogin;
use App\Models\DetailsLogin;
use App\Models\LoginInfo;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class LogLogoutInfo
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Logout  $event
     * @return void
     */

     public function handle(Logout $event)
     {
         $user = $event->user;
     
         if (!$user) {
             return;
         }
     
         // Today's date
         $todayDate = Carbon::today()->toDateString();
     
         // Generate a custom session token
         $sessionToken = session('custom_session_token');
     
         // Get all records for today with status 0 for this user
         $todayDateUserIds = LoginInfo::where('user_id', $user->id)
             ->where('status', 0)
             ->get();
     
            foreach ($todayDateUserIds as $loginInfo) {
            if ($user->session_id == $sessionToken) {
                $currentDateTime = Carbon::now();
                $loginTime = Carbon::parse($loginInfo->login_date . ' ' . $loginInfo->login_time);
                $logoutTime = $currentDateTime;
        
                // Check if logout occurs past midnight
                if ($loginTime->toDateString() !== $currentDateTime->toDateString()) {
                    $midnight = $loginTime->copy()->endOfDay(); 
                    $postMidnightDuration = $midnight->diffInSeconds($logoutTime); // Calculate seconds after midnight
        
                    // Calculate login hours up to midnight
                    $preMidnightDuration = $loginTime->diffInSeconds($midnight);
        
                    // Convert seconds to H:i:s format
                    $hours = floor($preMidnightDuration / 3600);
                    $minutes = floor(($preMidnightDuration % 3600) / 60);
                    $seconds = $preMidnightDuration % 60;
                    $preMidnightHours = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
                    // Update the current record
                    $loginInfo->update([
                        'logout_time' => $midnight,
                        'status' => 1,
                        'ip_address' => request()->getClientIp(),
                        'login_hour' => $preMidnightHours, // Duration before midnight
                    ]);
                } else {
                    // Regular logout logic for same-day sessions
                    $loginDuration = $loginTime->diffInSeconds($logoutTime);
                    $hours = floor($loginDuration / 3600);
                    $minutes = floor(($loginDuration % 3600) / 60);
                    $seconds = $loginDuration % 60;
                    $loginHour = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
                    $loginInfo->update([
                        'logout_time' => $logoutTime,
                        'status' => 1,
                        'ip_address' => request()->getClientIp(),
                        'login_hour' => $loginHour,
                    ]);
                }
            }
        }

         // If no records were found, create a new LoginInfo entry
         if ($todayDateUserIds->isEmpty()) {
             LoginInfo::create([
                 'user_id' => $user->id,
                 'name' => $user->name,
                 'email_address' => $user->email,
                 'login_date' => $todayDate,
                 'logout_time' => Carbon::now(),
                 'ip_address' => request()->getClientIp(),
                 'status' => 1,
                 'login_hour' => '00:00:00', // Default value if no login time
             ]);
         }
     
         // Update or create the DetailsLogin record for today's date
         DetailLogin::updateOrCreate(
             ['user_id' => $user->id],
             [
                 'user_id' => $user->id,
                 'name' => $user->name,
                 'email_address' => $user->email,
                 'logout_time' => Carbon::now(),
                 'ip_address' => request()->getClientIp(),
                 'status' => 1,
             ]
         );

         $role = Role::where('name', 'Super Admin')->first();
         if (!$role) {
             return response()->json([
                 'status' => false,
                 'message' => 'Role not found.',
             ], 404);
         }
 
         // Retrieve all users with the "Super Admin" role
         $superAdminUsers = User::role($role->name)->get();
 
         $authUser = Auth::user();
 
         // Create and send notifications to all "Super Admin" users
         foreach ($superAdminUsers as $superAdminUser) {
         Notification::create([ // Assuming you're using custom notifications model
             'title' => "{$authUser->name} has logged out",
             'text' => "{$authUser->name} has logged out. Logout time: " . Carbon::now()->format('d F Y, h:i:s A'),
             'from_user_id' => $authUser->id,
             'to_user_id' => $superAdminUser->id,
             'link' => route('login_details.index'),
             ]);
         }
     }
          
}

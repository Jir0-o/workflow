<?php

namespace App\Listeners;

use App\Models\DetailLogin;
use App\Models\DetailsLogin;
use App\Models\LoginInfo;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class LogLoginInfo
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Get the authenticated user
        $user = $event->user;

        // Generate a custom session token
        $customSessionToken = Str::uuid()->toString();

        // Store the token in the users table
        $user->session_id = $customSessionToken;
        $user->save();

        // Store the token in the current session
        session(['custom_session_token' => $customSessionToken]);

        $todayDate = Carbon::today()->toDateString();
        // // Check for previous logins not today
        // $previousLogins = DetailLogin::where('login_date', '!=', $todayDate)
        //                             ->get();

        // // If there are previous logins on different dates, delete them
        // if ($previousLogins->count() > 0) {
        //     DetailLogin::where('login_date', '!=', $todayDate)
        //               ->delete();
        // }

        // Store data in LoginInfo table
        // Save today's date in the session for midnight check
        session(['login_date' => $todayDate]);

        // Today's date
        $todayDate = Carbon::today()->toDateString();

        // Get all records for today with status 0 for this user
        $todayDateUserIds = LoginInfo::where('user_id', $user->id)
            ->where('status', 0)
            ->get();
    
        // Update each record individually
        foreach ($todayDateUserIds as $loginInfo) {
            $loginInfo->update([
                'logout_time' => Carbon::now(),
                'status' => 1,
                'is_active' => 1,
            ]);
        }

        LoginInfo::create([
            'name' => $user->name,
            'email_address' => $user->email,
            'user_id' => $user->id,
            'login_time' => Carbon::now()->format('H:i:s'), // Stores in 'HH:mm:ss' format
            'login_date' => Carbon::now()->format('Y-m-d'),
            'ip_address' => request()->getClientIp(),
            'status' => 0,
        ]);

        DetailLogin::updateOrCreate([
            'user_id' => $user->id
        ], [
            'name' => $user->name,
            'email_address' => $user->email,
            'user_id' => $user->id,
            'login_time' => Carbon::now()->format('H:i:s'), // Stores in 'HH:mm:ss' format
            'login_date' => Carbon::now()->format('Y-m-d'),
            'ip_address' => request()->getClientIp(),
            'status' => 0,
        ]);

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
            'title' => "{$authUser->name} has logged in",
            'text' => "{$authUser->name} has logged in. Login time: " . Carbon::now()->format('d F Y, h:i:s A'),
            'from_user_id' => $authUser->id,
            'to_user_id' => $superAdminUser->id,
            'link' => route('login_details.index'),
            ]);
        }
    }
}

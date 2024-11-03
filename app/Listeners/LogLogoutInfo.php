<?php

namespace App\Listeners;

use App\Models\DetailLogin;
use App\Models\DetailsLogin;
use App\Models\LoginInfo;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     
         // Get all records for today with status 0 for this user
         $todayDateUserIds = LoginInfo::where('user_id', $user->id)
             ->where('status', 0)
             ->get();
     
         // Update each record individually
         foreach ($todayDateUserIds as $loginInfo) {
             $loginInfo->update([
                 'logout_time' => Carbon::now(),
                 'ip_address' => request()->getClientIp(),
                 'status' => 1,
             ]);
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
             ]);
         }
     
         // Update or create the DetailsLogin record for today's date
         DetailLogin::updateOrCreate(
             ['user_id' => $user->id, 'login_date' => $todayDate],
             [
                 'user_id' => $user->id,
                 'name' => $user->name,
                 'email_address' => $user->email,
                 'logout_time' => Carbon::now(),
                 'ip_address' => request()->getClientIp(),
                 'status' => 1,
             ]
         );
     }
          
}

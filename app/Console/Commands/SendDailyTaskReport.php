<?php

namespace App\Console\Commands;

use App\Mail\DailyTaskReportMail;
use App\Models\DetailLogin;
use App\Models\MailAddress;
use App\Models\MailLog;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendDailyTaskReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-task-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $windowStart = $now->copy()->subMinutes(2)->format('H:i');
        $windowEnd = $now->format('H:i');
        
        $mailAddresses = MailAddress::whereBetween('daily_report_time', [$windowStart, $windowEnd])
            ->pluck('email_address');


        $userIds = User::pluck('id');

        $todayDate = Carbon::today()->toDateString();

        $detailsLoginUserIds = DetailLogin::whereDate('login_date', $todayDate)
            ->pluck('user_id')
            ->unique();

        $workingUsers = User::whereIn('id', $detailsLoginUserIds)->get();
        $notWorkingUsers = User::whereIn('id', $userIds->diff($detailsLoginUserIds))->get();
    
        foreach ($mailAddresses as $email) {

            $sendEmail = MailAddress::where('email_address', $email)->first();

            $alreadySent = MailLog::where('mail_address_id', $sendEmail->id)
                ->whereDate('mail_date', Carbon::today())
                ->where('mail_type', 'Daily Task Report')
                ->exists();

            if ($alreadySent) {
                continue; 
            }

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
                'is_active' => 2
            ]);
        }
    }
    
}

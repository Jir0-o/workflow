<?php

namespace App\Console\Commands;

use App\Mail\DailyTaskReportMail;
use App\Mail\MonthlyTaskReportMail;
use App\Models\MailAddress;
use App\Models\MailLog;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendMonthlyTaskReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-monthly-task-report';

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
                'is_active' => 2
            ]);
        }
    }
}

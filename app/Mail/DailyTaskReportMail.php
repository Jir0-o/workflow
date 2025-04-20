<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyTaskReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $yesterdayTasks;
    public $todayTasks;
    public $yesterdayWorkPlans;
    public $todayWorkPlans;
    public $workingUsers;
    public $notWorkingUsers;


    public function __construct($yesterdayTasks, $todayTasks, $yesterdayWorkPlans, $todayWorkPlans, $workingUsers, $notWorkingUsers)
    {
        $this->yesterdayTasks = $yesterdayTasks;
        $this->todayTasks = $todayTasks;
        $this->yesterdayWorkPlans = $yesterdayWorkPlans;
        $this->todayWorkPlans = $todayWorkPlans;
        $this->workingUsers = $workingUsers;
        $this->notWorkingUsers = $notWorkingUsers;

    }

    public function build()
    {
        return $this->subject('Daily Task Report')
                    ->view('emails.daily_report')
                    ->with([
                        'yesterdayTasks' => $this->yesterdayTasks,
                        'todayTasks' => $this->todayTasks,
                        'yesterdayWorkPlans' => $this->yesterdayWorkPlans,
                        'todayWorkPlans' => $this->todayWorkPlans,
                        'workingUsers' => $this->workingUsers,
                        'notWorkingUsers' => $this->notWorkingUsers,
                    ]);
    }
}

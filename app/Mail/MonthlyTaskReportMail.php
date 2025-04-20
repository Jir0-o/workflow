<?php

namespace App\Mail;

use App\Exports\TasksExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyTaskReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public $tasks) {}

    public function build()
    {
        $file = Excel::raw(new TasksExport($this->tasks), \Maatwebsite\Excel\Excel::XLSX);
        
        return $this->view('emails.monthly_report')
                    ->subject('Your Monthly Task Report')
                    ->attachData($file, 'Monthly_Report.xlsx', [
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
    }

    /**
     * Get the message envelope.
     */

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

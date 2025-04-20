<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class TasksExport implements FromCollection, WithHeadings
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection(): Collection
    {
        return $this->tasks->map(function ($task) {
            return [
                'Task Title' => $task->task_title ?? 'N/A',
                'User' => $task->user->name ?? 'N/A',
                'Task' => strip_tags($task->description) ?? 'N/A',
                'Status' => ucfirst($task->status),
                'Start Date' => optional($task->submit_date)->format('d F Y, h:i A'),
                'Submit Date' => $task->submit_by_date
                ? $task->submit_by_date->format('d F Y, h:i A')
                : 'Not Submitted',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Task Title',
            'User',
            'Task',
            'Status',
            'Start Date',
            'Submit Date',
        ];
    }
}


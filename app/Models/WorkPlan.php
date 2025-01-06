<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPlan extends Model
{
    protected $fillable = [
        'task_id',
        'description',
        'submit_date',
        'submit_by_date',
        'message',
        'admin_message',
        'reason_message',
        'work_status',
        'user_id',
        'title_name_id',
        'status',
        'attachment',
        'attachment_name',
        'created_at',
        'updated_at'
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }
    public function Task(){
        return $this->belongsTo(Task::class);
    }
}

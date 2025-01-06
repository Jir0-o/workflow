<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $fillable = [
        "task_title",
        "description",
        "status",
        "submit_date",
        "submit_by_date",
        "attachment",
        "attachment_name",
        "message",
        "admin_message",
        "reason_message",
        "work_hour",
        "user_id",
        "title_name_id",
        "created_at",
        "updated_at"
    ];

    use HasFactory;
    public function User(){
        return $this->belongsTo(User::class);
    }
    public function title_name(){
        return $this->belongsTo(TitleName::class);
    }
    protected $casts = [
        'submit_date' => 'datetime',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailAddress extends Model
{

    protected $fillable = [
        'name',
        'email_address',
        'status',
        'is_active',
        'daily_report_time',
        'monthly_report_time',
        'created_at',
        'updated_at',
    ];

    public function mailLogs()
    {
        return $this->hasMany(MailLog::class);
    }
}

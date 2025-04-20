<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{

    protected $fillable = [
        'mail_address_id',
        'name',
        'mail_type',
        'mail_date',
        'status',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function mailAddress()
    {
        return $this->belongsTo(MailAddress::class, 'mail_address_id');
    }
}

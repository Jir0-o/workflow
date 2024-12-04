<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subject',
        'date',
        'days_number',
        'leave_type',
        'from_date',
        'end_date',
        'reason',
        'name',
        'role',
        'email',
        'return_reason',
        'status',
        'is_active',
        'other',
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLogin extends Model
{
    protected $fillable = ['name', 'email_address', 'login_hour', 'login_time', 'logout_time', 'created_at','updated_at', 'login_date', 'ip_address','status', 'is_active', 'user_id'];
}

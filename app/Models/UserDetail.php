<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        "user_id",
        "user_title",
        "country",
        "age",
        "gender",
        "address",
        "phone",
        "email",
        "role_name",
        "is_active",
        "status",
        "created_at",
        "updated_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
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
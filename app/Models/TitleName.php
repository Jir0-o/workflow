<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleName extends Model
{
    use HasFactory;
    public function Task(){
        return $this->hasMany(Task::class);
    }
}

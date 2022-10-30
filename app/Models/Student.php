<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    public function registerUser()
    {
        return $this->haOne(RegisterUser::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    protected $fillable = [
        'user_id', 'class_id'
    ];
}

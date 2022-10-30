<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    public function registerUser(){
        return $this->haOne(RegisterUser::class);
    }
    public function marks(){
        return $this->hasMany(Mark::class);
    }
    public function school_classes(){
        return $this->belongsToMany(SchoolClass::class);
    }
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}

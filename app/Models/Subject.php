<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    
    public function marks(){
        return $this->hasMany(Mark::class);
    }
    public function school_classes(){
        return $this->belongsToMany(SchoolClass::class);
    }
    public function teachers(){
        return $this->hasMany(Teacher::class);
    }
}

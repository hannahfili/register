<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    public function registerUser()
    {
        return $this->hasOne(RegisterUser::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function sclasses()
    {
        return $this->belongsToMany(Sclass::class);
    }
    public function subject()
    {
        return $this->hasOne(Subject::class);
    }
    protected $fillable = [
        'user_id', 'subject_id'
    ];
}

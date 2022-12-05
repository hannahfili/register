<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterUser extends Model
{
    use HasFactory, HasApiTokens;

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'isAdmin', //'api_token'

    ];
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}

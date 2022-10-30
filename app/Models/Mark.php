<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function activity(){
        return $this->belongsTo(Activity::class);
    }
}

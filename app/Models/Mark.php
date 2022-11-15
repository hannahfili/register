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
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function registerUser()
    {
        return $this->belongsTo(RegisterUser::class);
    }
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function mark_modifications()
    {
        return $this->hasMany(Mark_modification::class);
    }
    protected $fillable = [
        'user_student_id', 'subject_id', 'moderator_id',
        'activity_id', 'mark_datetime',
        'value'
    ];
}

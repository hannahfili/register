<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Subset;

class SchoolClass extends Model
{
    use HasFactory;


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
    protected $fillable = [
        'name', 'class_start', 'class_end'
    ];
}

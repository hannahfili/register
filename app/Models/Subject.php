<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;


    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function sclasses()
    {
        return $this->belongsToMany(Sclass::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    protected $fillable = [
        'name', 'description'
    ];
}

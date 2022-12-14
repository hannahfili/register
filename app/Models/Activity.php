<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    protected $fillable = [
        'name', 'conversion_factor'
    ];
}

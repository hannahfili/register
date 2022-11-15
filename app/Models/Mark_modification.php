<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark_modification extends Model
{
    use HasFactory;

    protected $table = 'Mark_modifications';
    // protected $primaryKey='id';
    // protected $timestamps=true;
    // protected $dateFormat=
    public function mark()
    {
        $this->belongsTo(Mark::class);
    }
    protected $fillable = [
        'modification_datetime', 'moderator_id', 'mark_id',
        'mark_before_modification', 'mark_after_modification',
        'modification_reason'
    ];
}

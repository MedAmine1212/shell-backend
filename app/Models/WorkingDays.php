<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDays extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'working',
        'shiftStart',
        'shiftEnd',
        'pauseStart',
        'pauseEnd'
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}

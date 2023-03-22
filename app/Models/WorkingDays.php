<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDays extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_schedule_id',
        'day',
        'working',
        'shiftStart',
        'shiftEnd',
        'pause',
        'pauseStart',
        'pauseEnd'
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}

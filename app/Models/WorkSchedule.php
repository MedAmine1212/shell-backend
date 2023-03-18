<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'minimumConsultationTime'
    ];

    public function stations()
    {
        return $this->hasMany(Station::class);
    }

    public function workingDays()
    {
        return $this->hasMany(WorkingDays::class);
    }
}

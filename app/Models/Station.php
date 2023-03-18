<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'address',
        'station_admin_id',
        'work_schedule_id',
    ];

    public function stationAdmin()
    {
        return $this->belongsTo(StationAdmin::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }


    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function stationServices()
    {
        return $this->hasMany(StationService::class);
    }
    public function stationProducts()
    {
        return $this->hasMany(ProductStation::class);
    }

    public function bornes()
    {
        return $this->hasMany(Borne::class);
    }
}

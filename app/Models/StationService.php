<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'station_id',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}

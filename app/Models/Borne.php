<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Borne extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'status',
        'lastHeartBeat',
        'heartBeatInterval',
        'station_id'
    ];

    public function stations()
    {
        return $this->belongsTo(Station::class);
    }

    public function borneAdvertisements()
    {
        return $this->hasMany(BorneAdvertisements::class);
    }
}

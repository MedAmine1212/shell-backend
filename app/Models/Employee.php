<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends User
{
    use HasFactory;

    protected $fillable = [
        'active',
        'onConsultation',
        'user_id',
        'station_id',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

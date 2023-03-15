<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'brand',
        'model',
        'year',
        'fuelType',
        'mileage',
        'lastOilChange',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}


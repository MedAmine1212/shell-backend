<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'discount',
        'type',
        'status',
        'duration',
        'vehicle_id',
        'employee_id',
        'dateConsultation',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function consultationService()
    {
        return $this->hasMany(ConsultationService::class);
    }

}

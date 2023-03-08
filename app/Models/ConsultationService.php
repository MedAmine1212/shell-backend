<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'consultation_id',
        'status',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

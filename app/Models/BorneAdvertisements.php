<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorneAdvertisements extends Model
{
    use HasFactory;

    protected $fillable = [
        'borne_id',
        'advertisement_id'
    ];

    public function borne()
    {
        return $this->belongsTo(Station::class);
    }

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

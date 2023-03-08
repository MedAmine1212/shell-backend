<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationAdmin extends User
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function station()
    {
        return $this->hasOne(Station::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

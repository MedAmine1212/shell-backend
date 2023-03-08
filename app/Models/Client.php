<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends User
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'validated',
        'registeredAt'
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

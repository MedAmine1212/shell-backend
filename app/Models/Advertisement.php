<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isVideo',
        'file'
    ];

    public function borneAdvertisements()
    {
        return $this->hasMany(BorneAdvertisements::class);
    }
}

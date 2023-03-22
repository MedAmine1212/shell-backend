<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'station_id',
        'stock',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}

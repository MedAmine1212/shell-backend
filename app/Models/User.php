<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    /**
     * Get the child model associated with the user.
     */

    protected $fillable = [
        'firstName',
        'lastName',
        'phone',
        'barCode',
        'password',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function stationAdmin()
    {
        return $this->hasOne(StationAdmin::class);
    }

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }

}

<?php

namespace Vuetable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_admin' => 'boolean'
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}

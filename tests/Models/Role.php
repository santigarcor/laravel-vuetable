<?php

namespace Vuetable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Adress extends Model
{
    protected $fillable = ['PLZ', 'city', 'street', 'country'];

    public function users():HasMany {
        return $this->hasMany(User::class);
    }
}

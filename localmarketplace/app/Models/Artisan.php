<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artisan extends Model
{
    protected $table = 'artisan';

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function price()
    {
        return $this->hasMany(Price::class, 'id_artisan');
    }

}

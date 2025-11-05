<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artisan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'artisan';

    protected $fillable = [
        'name', 'address', 'rib', 'id_user', 'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Price::class, 'id_artisan', 'id', 'id', 'id_product')
                    ->with('prices');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'id_artisan');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artisan extends Model
{
    use HasFactory;

    protected $table = 'artisans';

    protected $fillable = [
        'name',
        'address',
        'rib',
        'phone',
        'email',
        'description',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, 'id_artisan', 'id', 'id', 'id_product')
                    ->with('prices');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
     use HasFactory ;  //, SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'nom',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function price()
    {
        return $this->hasMany(Prices::class, 'id_product');
    }

    public function latestPrice()
    {
        return $this->hasOne(Price::class, 'id_product')
                    ->whereNull('deleted_at')
                    ->latest('created_at');
    }

    public function creator()
    {
        return $this->belongsTo(Artisan::class, 'id_artisan_creator');
    }
}



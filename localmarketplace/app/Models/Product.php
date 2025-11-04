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

    public function prices()
    {
        return $this->hasMany(Price::class, 'id_product');
    }

}



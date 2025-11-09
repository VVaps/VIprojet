<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Product extends Model
{
     use HasFactory;

    protected $table = 'Products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'artisan_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function creator()
    {
        return $this->belongsTo(Artisan::class, 'id_artisan_creator');
    }
}



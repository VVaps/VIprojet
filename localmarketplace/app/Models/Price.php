<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'price';

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function artisan()
    {
        return $this->belongsTo(Artisan::class, 'id_artisan');
    }

}

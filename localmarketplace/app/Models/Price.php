<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
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

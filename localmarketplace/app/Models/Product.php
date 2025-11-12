<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Product extends Model
{
<<<<<<< HEAD
=======
     use HasFactory;

    protected $table = 'products';

>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'artisan_id',
    ];

<<<<<<< HEAD
    /**
     * Get the artisan that owns the product.
     */
    public function artisan()
    {
        return $this->belongsTo(Artisan::class);
    }

    /**
     * Get the comments for the product.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Get the users who have this product in their cart.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'cart_items')
            ->withPivot('quantity');
    }

    /**
     * Get the cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
=======
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function artisan()
    {
        return $this->belongsTo(Artisan::class, 'artisan_id');
>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
    }
}



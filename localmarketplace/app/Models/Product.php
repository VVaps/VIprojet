<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'artisan_id',
    ];

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
    }
}

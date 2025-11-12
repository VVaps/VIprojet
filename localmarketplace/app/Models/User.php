<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
<<<<<<< HEAD
        'user_type',
=======
        
>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

<<<<<<< HEAD
    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the cart items for the user.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the products in the user's cart.
     */
    public function cartProducts()
    {
        return $this->belongsToMany(Product::class, 'cart_items')
            ->withPivot('quantity');
    }

    /**
     * Get the comments for the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the artisans for the user.
     */
    public function artisans()
    {
        return $this->hasMany(Artisan::class);
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    /**
     * Check if the user is an artisan.
     */
    public function isArtisan()
    {
        return $this->user_type === 'artisan';
    }

    /**
     * Scope to only customers.
     */
    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    /**
     * Scope to only artisans.
     */
    public function scopeArtisans($query)
    {
        return $query->where('user_type', 'artisan');
=======
    public function artisans()
    {
        return $this->hasMany(Artisan::class, 'id_user');
    }

    public function isArtisan()
    {
        return $this->artisans()->exists();
>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
    }
}

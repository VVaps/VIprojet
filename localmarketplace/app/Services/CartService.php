<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Merge session cart with user database cart after authentication.
     * This method should be called after successful login/registration.
     */
    public function mergeSessionCartWithDatabase()
    {
        if (!Auth::check()) {
            return false;
        }

        $sessionCart = session('cart', []);
        
        if (empty($sessionCart)) {
            return true; // No session cart to merge
        }

        $userId = Auth::id();
        
        foreach ($sessionCart as $sessionItem) {
            // Check if item already exists in user's database cart
            $existingCartItem = CartItem::where('user_id', $userId)
                ->where('product_id', $sessionItem['product_id'])
                ->first();

            if ($existingCartItem) {
                // Update quantity if item exists
                $existingCartItem->quantity += $sessionItem['quantity'];
                $existingCartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $sessionItem['product_id'],
                    'quantity' => $sessionItem['quantity'],
                ]);
            }
        }

        // Clear session cart after successful merge
        session(['cart' => []]);
        
        return true;
    }

    /**
     * Get the current cart count for the authenticated user including merged session items.
     */
    public function getMergedCartCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        // First merge any pending session cart items
        $this->mergeSessionCartWithDatabase();
        
        return CartItem::where('user_id', Auth::id())->sum('quantity');
    }
}
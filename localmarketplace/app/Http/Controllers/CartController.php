<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get all cart items for the current user.
     * Works for both authenticated users (database) and guests (session).
     */
    public function index()
    {
        if (Auth::check()) {
            // Authenticated user: get from database
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with('product')
                ->get();

            $formattedItems = $cartItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'description' => $item->product->description,
                        'price' => $item->product->price,
                        'image' => $item->product->image,
                    ],
                    'quantity' => $item->quantity,
                    'total_price' => $item->product->price * $item->quantity
                ];
            });
        } else {
            // Guest user: get from session
            $sessionCart = session('cart', []);
            $cartItems = collect($sessionCart);
            
            $formattedItems = $cartItems->map(function($item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    return null;
                }
                
                return [
                    'id' => $item['id'],
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'image' => $product->image,
                    ],
                    'quantity' => $item['quantity'],
                    'total_price' => $product->price * $item['quantity']
                ];
            })->filter();
        }

        $totalAmount = $formattedItems->sum('total_price');
        $totalCount = $formattedItems->sum('quantity');

        return response()->json([
            'success' => true,
            'items' => $formattedItems->values(),
            'total_amount' => $totalAmount,
            'total_count' => $totalCount
        ]);
    }

    /**
     * Add a product to the cart.
     * Works for both authenticated users (database) and guests (session).
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        if (Auth::check()) {
            // Authenticated user: store in database
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update quantity if item exists
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }
        } else {
            // Guest user: store in session
            $sessionCart = session('cart', []);
            
            // Check if item already exists
            $existingIndex = null;
            foreach ($sessionCart as $index => $item) {
                if ($item['product_id'] == $product->id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                // Update quantity if item exists
                $sessionCart[$existingIndex]['quantity'] += $quantity;
            } else {
                // Add new item
                $sessionCart[] = [
                    'id' => uniqid(), // Generate unique ID for guest cart
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];
            }
            
            session(['cart' => $sessionCart]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier!',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update the quantity of a cart item.
     * Works for both authenticated users (database) and guests (session).
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if (Auth::check()) {
            // Authenticated user: update in database
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('id', $itemId)
                ->firstOrFail();

            $cartItem->quantity = $request->quantity;
            $cartItem->save();
        } else {
            // Guest user: update in session
            $sessionCart = session('cart', []);
            $itemIndex = null;
            
            foreach ($sessionCart as $index => $item) {
                if ($item['id'] == $itemId) {
                    $itemIndex = $index;
                    break;
                }
            }
            
            if ($itemIndex !== null) {
                $sessionCart[$itemIndex]['quantity'] = $request->quantity;
                session(['cart' => $sessionCart]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart.'
                ], 404);
            }
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Quantité mise à jour!',
            'cart_count' => $cartCount,
            'item' => [
                'id' => $itemId,
                'quantity' => $request->quantity,
                'total_price' => $this->calculateItemTotal($itemId)
            ]
        ]);
    }

    /**
     * Remove a cart item.
     * Works for both authenticated users (database) and guests (session).
     */
    public function remove($itemId)
    {
        if (Auth::check()) {
            // Authenticated user: remove from database
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('id', $itemId)
                ->firstOrFail();

            $cartItem->delete();
        } else {
            // Guest user: remove from session
            $sessionCart = session('cart', []);
            $sessionCart = array_filter($sessionCart, function($item) use ($itemId) {
                return $item['id'] != $itemId;
            });
            session(['cart' => array_values($sessionCart)]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier!',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Clear all cart items.
     * Works for both authenticated users (database) and guests (session).
     */
    public function clear()
    {
        if (Auth::check()) {
            // Authenticated user: clear database
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            // Guest user: clear session
            session(['cart' => []]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé!',
            'cart_count' => 0
        ]);
    }

    /**
     * Get cart count for the current user.
     * Works for both authenticated users (database) and guests (session).
     */
    public function count()
    {
        $cartCount = $this->getCartCount();
        
        return response()->json([
            'success' => true,
            'count' => $cartCount
        ]);
    }

    /**
     * Helper method to get cart count.
     * Works for both authenticated users (database) and guests (session).
     */
    private function getCartCount()
    {
        if (Auth::check()) {
            // Authenticated user: count from database
            return CartItem::where('user_id', Auth::id())->sum('quantity');
        } else {
            // Guest user: count from session
            $sessionCart = session('cart', []);
            return array_sum(array_column($sessionCart, 'quantity'));
        }
    }

    /**
     * Calculate total price for a specific item.
     */
    private function calculateItemTotal($itemId)
    {
        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('id', $itemId)
                ->with('product')
                ->first();
            
            return $cartItem ? $cartItem->product->price * $cartItem->quantity : 0;
        } else {
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $item) {
                if ($item['id'] == $itemId) {
                    $product = Product::find($item['product_id']);
                    return $product ? $product->price * $item['quantity'] : 0;
                }
            }
            return 0;
        }
    }
}

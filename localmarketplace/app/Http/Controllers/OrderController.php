<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Middleware is applied in routes/web.php
    public function __construct()
    {
        // Routes are already protected by 'auth' middleware in web.php
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderItems.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order from cart.
     */
    public function create()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Votre panier est vide.');
        }

        $totalAmount = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('orders.create', compact('cartItems', 'totalAmount'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Calculate total amount
        $totalAmount = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'order_date' => now(),
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product->id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->product->price,
                'total_price' => $cartItem->product->price * $cartItem->quantity,
            ]);
        }

        // Clear cart after order is created
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Commande créée avec succès!');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product', 'user');

        return view('orders.show', compact('order'));
    }

    /**
     * Update the order status (for admin use in a real system).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour.',
            'order' => $order
        ]);
    }

    /**
     * Cancel an order (if still pending).
     */
    public function cancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')->with('success', 'Commande annulée avec succès.');
    }
}
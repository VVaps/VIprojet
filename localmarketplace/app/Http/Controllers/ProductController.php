<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Middleware is applied at route level instead of constructor for better compatibility

    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::with('artisan')->latest()->paginate(12);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Get current user's artisans
        $artisans = Auth::user()->artisans;
        
        if ($artisans->isEmpty()) {
            return redirect()->route('artisans.index')->with('error', 'Vous devez d\'avoir un profil d\'artisan pour créer un produit.');
        }

        return view('products.create', compact('artisans'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'artisan_id' => 'required|exists:artisans,id',
        ]);

        // Verify the artisan belongs to the current user
        $artisan = Auth::user()->artisans()->where('id', $request->artisan_id)->firstOrFail();

        $productData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'artisan_id' => $request->artisan_id,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = $imagePath;
        }

        Product::create($productData);

        return redirect()->route('products.index')->with('success', 'Produit créé avec succès!');
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['artisan', 'comments.user'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        // Verify the product belongs to current user's artisans
        if ($product->artisan->user_id !== Auth::id()) {
            abort(403);
        }

        $artisans = Auth::user()->artisans;
        
        return view('products.edit', compact('product', 'artisans'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Verify the product belongs to current user's artisans
        if ($product->artisan->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'artisan_id' => 'required|exists:artisans,id',
        ]);

        // Verify the new artisan belongs to the current user
        $artisan = Auth::user()->artisans()->where('id', $request->artisan_id)->firstOrFail();

        $productData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'artisan_id' => $request->artisan_id,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = $imagePath;
        }

        $product->update($productData);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour avec succès!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Verify the product belongs to current user's artisans
        if ($product->artisan->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès!');
    }
}

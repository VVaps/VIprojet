<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FruitImgService;

class ProductController extends Controller
{   
    //API recherche d'images de fruits et légumes
    protected $fruitImgService;

    public function index(Request $request)
    {
    
        //filtre sur l'artisan si artisan a été sélectionné
        // if ( (Auth::check() && Auth::user()->isArtisan()) || ($request->query('artisan'))){
            // if ( (Auth::check() && Auth::user()->isArtisan())) {               
            //    $artisanId = (Artisan::where('id_user', Auth::user()->id)->first())->id;     
            // } else{
                $artisanId = $request->query('artisan'); 
            // }           
        // }
        
        // Liste de base des produits
        // Affiche tous les produits avec les infos artisan
        $query = Product::with('artisan');
        
        // Si un artisan est sélectionné dans la requête
        if ($artisanId) {
            $query->where('artisan_id', $artisanId);
        }    

        $products = $query
                    ->orderBy('name', 'asc')
                    ->get();
    
        return view('products.index', compact('products'));
    }

     /**
     * Afficher un produit spécifique
     */
    public function show($product)
    {
        return view('products.index', compact('product'));    
    }


    public function create()
    {
        return view('products.create');
    }

    public function addProduct(Request $request)
    {
         if (!Auth::user()->isArtisan()) {abort(403, 'Accès interdit');}
        
        // récupère le produit saisi et le charge dans la base
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'artisan_id'=>'required',
            'image' => 'image',
        ]);

        $path = $request->file("image")->storePublicly("productsImg", "public");

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image' => $path,
            'price' => $validated['price'],
            'artisan_id' => $validated['artisan_id'],
            'created_at' => now(),
            'modified_at' => now()
        ]);     

        //rafraichissement de la liste de produits  
        $query = Product::with('artisan')->where('artisan_id', $validated['artisan_id'])    
                    ->orderBy('name', 'asc')
                    ->get();
            
        return redirect()->route('products.index')->with('success', 'Produit créé avec succès.');

    }
    
    public function edit(Product $product)
    {
        if (!Auth::user()->isArtisan()) {abort(403, 'Accès interdit');}

        return view('products.edit', compact('product'));
    }

    public function updProduct(Request $request,Product $product)
    {
        if (!Auth::user()->isArtisan()) {abort(403, 'Accès interdit');}

        // récupère le produit saisi et le modifie dans la base
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'required|string',
            'artisan_id'=>'required',
            'price' => 'required|numeric|min:0',
            'image' => 'image',
        ]);

        $path = $request->file("image")->storePublicly("productsImg", "public");

        $product->update([
            'name' => $validated['name'] ?? $product->name,
            'description' => $validated['description'] ?? $product->description,
            'image' => $path,
            'artisan_id'=>  $validated['artisan_id'], 
            'price' => $validated['price'],
            'modified_at' => now()
        ]);

        //rafraichissement de la liste de produits  
        $query = Product::with('artisan')->where('artisan_id', $validated['artisan_id'])    
                    ->orderBy('name', 'asc')
                    ->get();

        return redirect()->route('products.index')->with('success', 'Produit mis à jour avec succès.');
    }
    
    public function delProduct(Product $product)
    {
        if (!Auth::user()->isArtisan()) {abort(403, 'Accès interdit');}

        try {
            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Impossible de supprimer ce produit.');
        }   

    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Price;
use App\Models\Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
 
    public function index1()
    {

        // Charger les 10 premiers produits + le prix pour cet artisan
        $products = Product::with(['prices' => function ($query) {
            $query->latest('created_at')
              ->whereNull('deleted_at')
              ->with('artisan')
              ->limit(1);
        }])->whereNull('deleted_at') 
            ->limit(10)
            ->get();
        

        // Mock data for now
            // $products = [
            //     (object) [
            //         'id' => 1,
            //         'name' => 'Pain Artisan',
            //         'description' => 'Pain au levain fraîchement cuit',
            //         'price' => 5.99,
            //         'image' => '/images/bread.jpg'
            //     ],
            //     (object) [
            //         'id' => 2,
            //         'name' => 'Poterie Main',
            //         'description' => 'Magnifique vase en céramique',
            //         'price' => 45.00,
            //         'image' => '/images/pottery.jpg'
            //     ],
            //     (object) [
            //         'id' => 3,
            //         'name' => 'Écharpe Laine',
            //         'description' => 'Écharpe tricotée confortable',
            //         'price' => 25.50,
            //         'image' => '/images/scarf.jpg'
            //     ],
            //     (object) [
            //         'id' => 4,
            //         'name' => 'Fromage Artisan',
            //         'description' => 'Cheddar affiné de la ferme locale',
            //         'price' => 12.99,
            //         'image' => '/images/cheese.jpg'
            //     ],
            //     (object) [
            //         'id' => 5,
            //         'name' => 'Bol Bois',
            //         'description' => 'Bol à salade sculpté à la main',
            //         'price' => 35.00,
            //         'image' => '/images/bowl.jpg'
            //     ],
            //     (object) [
            //         'id' => 6,
            //         'name' => 'Pot Miel',
            //         'description' => 'Miel pur local',
            //         'price' => 8.50,
            //         'image' => '/images/honey.jpg'
            //     ],
            // ];

        return view('products.index', compact('products'));
    }

    
    public function index(Request $request)
    {
        $artisanId = $request->query('artisan'); //1 artisan a t'il été sélectionné?

        // Affiche tous les produits avec leur prix
        // Liste de base des produits
        $query = Product::with(['prices' => function ($query) {
            $query->whereNull('deleted_at')
                  ->with('artisan:id,name');
        }])->whereNull('deleted_at');

        // Si un artisan est sélectionné dans la requête
        if ($artisanId) {
            $query->whereHas('prices', function ($q) use ($artisanId) {
                $q->where('id_artisan', $artisanId);
            });
        }    

        // $products = $query::orderBy('name')->get();
        $products = $query->get();

        //vérification si le user est l'artisan sélectionné
        $user = Auth::user();
        $userArtisanId = $user && $user->artisan ? $user->artisan->id : null;
        $isOwner = $userArtisanId && $userArtisanId == $artisanId;

        return view('products.index', compact('products','artisanId','isOwner'));
    }

     /**
     * Afficher un produit spécifique
     */
    public function show($id)
    {
        $product = Product::with(['prices' => function($query) {
            $query->whereNull('deleted_at')
                  ->with('artisan:id,name,address');
        }])
        ->whereNull('deleted_at')
        ->findOrFail($id);
        
        return response()->json($product);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->artisan) {
            abort(403, 'Accès interdit');
        }
        return view('products.create');
    }

    public function addProduct(Request $request)
    {
        //récupération code artisan
        $user = Auth::user();
        if (!$user || !$user->artisan) {
            abort(403, 'Accès interdit');
        }
        
        $artisanId = $request->query('artisan'); 

        //sinon si ajouté fonction EnsureUserIsArtisan dans le kernel:
        //$artisan = $request->artisan;

        // récupère le produit saisi et le charge dans la base
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'qty_available' => 'nullable|numeric|min:0',
            'image' => 'image',
        ]);

        $path = $request->file("image")->storePublicly("productsImg", "public");

        
        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $path,
                'created_at' => now(),
                'modified_at' => now()
            ]);

            //$productId = Product::latest()->first();
            // foreach ($request->input("category_id") as $key => $value) {
            $price = Price::create([
                'price' => $validated['price'],
                'qty_available' => $validated['qty_available'] ?? null,
                'id_artisan' => $artisanId,
                'id_product' => $product->id,
                'created_at' => now(),
                'modified_at' => now()
            ]);     

            DB::commit();

            //récupération de la liste de tous les produits de l'utilisateur 
            $products = Product::with(['prices' => function ($query) use ($artisanId) {
                $query->where('id_artisan', $artisanId);
            }])->get();
            return redirect()->route('products.index', compact('products'),$artisanId, true)
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
    
  public function edit(Product $product)
    {
        $user = Auth::user();
        if (!$user || !$user->artisan) {
            abort(403, 'Accès interdit');
        }

        // Vérifier que l’artisan connecté possède au moins un prix pour ce produit
        if (!$product->prices->contains('id_artisan', $user->artisan->id)) {
            abort(403, 'Accès interdit');
        }

        // On récupère le prix de l’artisan connecté
        $price = $product->prices->firstWhere('id_artisan', $user->artisan->id);

        return view('products.edit', compact('product', 'price'));
    }

    public function updProduct(Request $request,Product $product)
    {
        //récupération code artisan
        $user = Auth::user();
        if (!$user || !$user->artisan) {
            abort(403, 'Accès interdit');
        }
        $artisanId = $request->query('artisan'); 

        //sinon si ajouté fonction EnsureUserIsArtisan dans le kernel:
        //$artisan = $request->artisan;

       // $product = Product::whereNull('deleted_at')->findOrFail($id);
       $id = $product-> id;

        // récupère le produit saisi et le modifie dans la base
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'required|string',
            'qty_available'=> 'nullable|numeric|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'image',
        ]);

        $path = $request->file("image")->storePublicly("productsImg", "public");
        DB::beginTransaction();
        try {
            // Mettre à jour le produit
            if (isset($validated['name']) || isset($validated['description']) || isset($validated['image'])) {
                $product->update([
                    'name' => $validated['name'] ?? $product->name,
                    'description' => $validated['description'] ?? $product->description,
                    'image' => $path,
                    'modified_at' => now()
                ]);
            }

            // Mettre à jour le prix de l'artisan créateur
            if (isset($validated['price'])) {
                $price = Price::where('id_product', $product->id)
                    ->where('id_artisan', $artisanId)
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($price) {
                    $price->update([
                        'price' => $validated['price'],
                        'qty_available' => $validated['qty_available'] ?? $price->qty_available,
                        'modified_at' => now()
                    ]);
                } else {
                    // Créer un nouveau prix si inexistant
                    Price::create([
                        'price' => $validated['price'],
                        'qty_available' => $validated['qty_available'] ?? null,
                        'id_artisan' => $artisanId,
                        'id_product' => $product->id,
                        'created_at' => now(),
                        'modified_at' => now()
                    ]);
                }  
            }    
            DB::commit();

            //récupération de la liste de tous les produits de l'utilisateur 
            $products = Product::with(['prices' => function ($query) use ($artisanId) {
                $query->where('id_artisan', $artisanId);
            }])->get();
            return redirect()->route('products.index', compact('products'),$artisanId, true)
                ->with('success', 'Produit mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }
    
/**
     * Supprimer un produit (soft delete)
     * Seul le créateur peut supprimer
     */
    public function delProduct(Request $request, $id)
    {
         //récupération code artisan
        $user = Auth::user();
        if (!$user || !$user->artisan) abort(403, 'Accès interdit');

        $product = Product::whereNull('deleted_at')->findOrFail($id);
        $price = $product->prices->firstWhere('id_artisan', $user->artisan->id);
        if (!$price) abort(403, 'Accès interdit');
        $artisanId = $user->artisan->id;

        $price->delete(); // Supprime seulement le prix de cet artisan

        // Optionnel : si plus aucun prix n’existe pour ce produit, supprimer le produit
        if ($product->prices()->count() == 0) {
            $product->delete();
        }        
    
        //récupération de la liste de tous les produits de l'utilisateur 
        $products = Product::with(['prix' => function ($query) use ($artisanId) {
            $query->where('id_artisan', $artisanId);
        }])->get();
        return redirect()->route('products.index', compact('products'),$artisanId, true)
            ->with('success', 'Produit supprimé avec succès');       

    }

}

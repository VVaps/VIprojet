<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Price;
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

    
    public function index()
    {
        // Affiche tous les produits avec leur prix
        $products = Product::with(['prices' => function ($query) {
            $query->whereNull('deleted_at')
                  ->with('artisan:id,name');
        }])->whereNull('deleted_at') 
            ->get();

        return view('products.index', compact('products'));
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

    public function addProduct(Request $request)
    {
        //récupération code artisan
        $user = Auth::user();
        $artisanId = optional($user->artisan)->id; // Sécurité si l’utilisateur n’a pas d’artisan lié
        //ne devrait pas être car seuls les artisans ont accès au crud
        if (!$artisanId) {
            return response()->json(['error' => 'Aucun artisan lié à cet utilisateur'], 403);
        }

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
            return redirect()->route('products.create', compact('products'))
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
    

    public function updProduct(Request $request, $id)
    {
        //récupération code artisan
        $user = Auth::user();
        $artisanId = optional($user->artisan)->id; // Sécurité si l’utilisateur n’a pas d’artisan lié
        if (!$artisanId) {
            return response()->json(['error' => 'Aucun artisan lié à cet utilisateur'], 403);
        }

        //sinon si ajouté fonction EnsureUserIsArtisan dans le kernel:
        //$artisan = $request->artisan;

        $product = Product::whereNull('deleted_at')->findOrFail($id);

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
            return redirect()->route('products.update')
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
        $artisanId = optional($user->artisan)->id; // Sécurité si l’utilisateur n’a pas d’artisan lié
        if (!$artisanId) {
            return response()->json(['error' => 'Aucun artisan lié à cet utilisateur'], 403);
        }

        //sinon si ajouté fonction EnsureUserIsArtisan dans le kernel:
        //$artisan = $request->artisan;
        
        $product = Product::whereNull('deleted_at')->findOrFail($id);
        
        // Vérifier que l'artisan est le créateur - ne devrait pas arriver car que produits de l'artisan
        if ($product->id_artisan_creator !== $artisanId) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce produit'
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Soft delete du produit
            $product->update([
                'deleted_at' => now()
            ]);
            
            // Soft delete de tous les prix associés
            Price::where('id_product', $product->id)
                ->where('id_artisan', $artisanId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now()
                ]);
            
            DB::commit();
            
            //récupération de la liste de tous les produits de l'utilisateur 
            $products = Product::with(['prix' => function ($query) use ($artisanId) {
                $query->where('id_artisan', $artisanId);
            }])->get();
            return redirect()->route('products.update')
                ->with('success', 'Produit supprimé avec succès');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // public function updValid(String $id,UpdateProductRequest $request): RedirectResponse
    // {
    //     $validate = $request->validated();

    //     if (!empty($validate["image"]) && $validate["image"] != "undefinrd") {
    //         $path = $request->file("image")->storePublicly("productsImg", "public");
    //         $product->image = $path;
    //     }

    //     $product->name = $validate["name"];
    //     $product->description = $validate["description"];
    //     $price->price = $validate["price"];
    //     $price->artisant_id = $validate->user()->id;

    //     $product->save();
    //     // rend la main à l'écran d'origine?    
    //     return redirect('/artisan/dashboard');
    // }
}

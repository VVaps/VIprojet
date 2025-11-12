<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Artisan;
<<<<<<< HEAD
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
=======

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FruitImgService;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{   
    //API recherche d'images de fruits et légumes
    protected $fruitImgService;
    public function __construct(FruitImgService $fruitImgService)
    {
        $this->fruitImgService = $fruitImgService;
    }


    public function index(?Artisan $artisan = null)
    {
    
        if ($artisan) {
            $query = $artisan->products();

            // $selectedArtisan = $artisan;
        } 
        else {
            $query = Product::with('artisan');

            // $selectedArtisan = null;
        }   

        $products = $query
                    ->orderBy('name', 'asc')
                    -> get();

        
        // Vérifie si l'utilisateur connecté est propriétaire de l'artisan sélectionné
        $isOwner = false;
        if (Auth::check() && $artisan) {
            $isOwner = $artisan->id_user === Auth::id();
        }

    
        return view('products.index', compact('products', 'artisan', 'isOwner'));
    }

     /**
     * Afficher un produit spécifique
     */
    public function show($product)
    {
        return view('products.index', compact('product', $product->artisan));    
    }


    public function create(Artisan $artisan)
    {
        $pexelsPhotos = $this->fruitImgService->searchPhotos('product');        
        return view('products.create', compact('artisan', 'pexelsPhotos'));
    }
    //contrôle saisie et chargement base
    public function addProduct(Request $request)
    {
        
        // récupère le produit saisi et le charge dans la base
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'artisan_id' => 'required|exists:artisans,id',
            // 'image_source' => 'nullable|in:upload,pexels',
            // 'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'pexels_url' => 'nullable|url',      
        ]);

        $artisan = Artisan::findOrFail($validated['artisan_id']);
        if ((!Auth::user()->isArtisan()) || ($artisan->id_user !== Auth::id())){abort(403, 'Accès interdit');}

        $product = new Product($validated);
        $imagePath = null;

        // if ($request->image_source === 'upload') {
        //     // Upload depuis le poste de l'utilisateur
        //     $imagePath = $request->file('image_upload')->store('products', 'public');
        // } else {
        //     // Télécharger l'image depuis Pexels
        //     $imagePath = $this->fruitImgService->downloadAndStore($request->pexels_url, $validated['name']);
            
        //     if (!$imagePath) {
        //         return back()->withErrors(['pexels_url' => 'Erreur lors du téléchargement de l\'image'])
        //                     ->withInput();
        //     }
        // }
        
        $product['image'] = $imagePath;
        $product->save();

        // // Si l'utilisateur veut récupérer l'image automatiquement
        // if ($request->boolean('auto_fetch_image')) {
        //     $imageUrl = $this->fruitImgService->searchImage($validated['name']);
            
        //     if ($imageUrl) {
        //         $storedPath = $this->fruitImgService->downloadAndStore(
        //             $imageUrl, 
        //             $validated['name']
        //         );
                
        //         if ($storedPath) {
        //             $product->image_path = $storedPath;
        //         }
        //     }
        // }

        

        // $path = $request->file("image")->storePublicly("productsImg", "public");

        // $product = Product::create([
        //     'name' => $validated['name'],
        //     'description' => $validated['description'] ?? null,
        //     'price' => $validated['price'],
        //     'artisan_id' => $validated['artisan_id'],
        //     'created_at' => now(),
        //     'modified_at' => now()
        // ]);     

        //rafraichissement de la liste de produits  
        $query = Product::with('artisan')->where('artisan_id', $validated['artisan_id'])    
                    ->orderBy('name', 'asc')
                    ->get();
            
        return redirect()->route('products.index', $artisan)->with('success', 'Produit créé avec succès.');

    }
    
    // formulaire de maj
    public function edit(Product $product)
    {
        if ((!Auth::user()->isArtisan()) || ($product->artisan->id_user !== Auth::id())){abort(403, 'Accès interdit');}

        $pexelsPhotos = $this->fruitImgService->searchPhotos('product');   
        return view('products.edit', compact('product', 'pexelsPhotos'));
    }
    // contrôle saisie et chargement base
    public function updProduct(Request $request,Product $product)
    {
        if ((!Auth::user()->isArtisan()) || ($product->artisan->id_user !== Auth::id())) {abort(403, 'Accès interdit');}

        
        // récupère le produit saisi et le modifie dans la base
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'required|string',
            'artisan_id'=>'required',
            'price' => 'required|numeric|min:0',
            // 'image_source' => 'nullable|in:upload,pexels',
            // 'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'pexels_url' => 'nullable|url',
        ]);

        // $path = $request->file("image")->storePublicly("productsImg", "public");
        $product->fill($validated);

        // // Si une nouvelle image est fournie
        // if ($request->image_source) {
        //     // Supprimer l'ancienne image si elle existe
        //     if ($product->image && Storage::disk('public')->exists($product->image)) {
        //         Storage::disk('public')->delete($product->image);
        //     }

        //     if ($request->image_source === 'upload') {
        //         $data['image'] = $request->file('image_upload')->store('products', 'public');
        //     } else {
        //         $imagePath = $this->fruitImgService->downloadAndStore($request->pexels_url, $validated['name']);
                
        //         if (!$imagePath) {
        //             return back()->withErrors(['pexels_url' => 'Erreur lors du téléchargement de l\'image Pexels'])
        //                         ->withInput();
        //         }
                
        //         $data['image'] = $imagePath;
        //     }
        // }


        $product->save();

        // $product->update([
        //     'name' => $validated['name'] ?? $product->name,
        //     'description' => $validated['description'] ?? $product->description,
        //     'image' => $path,
        //     'artisan_id'=>  $validated['artisan_id'], 
        //     'price' => $validated['price'],
        //     'modified_at' => now()
        // ]);

        //rafraichissement de la liste de produits  
        $query = Product::with('artisan')->where('artisan_id', $validated['artisan_id'])    
                    ->orderBy('name', 'asc')
                    ->get();

        return redirect()->route('products.index',$product->artisan)->with('success', 'Produit mis à jour avec succès.');
    }
    

    public function delProduct(Product $product)
    {
        if ((!Auth::user()->isArtisan()) || ($product->artisan->id_user !== Auth::id())) {abort(403, 'Accès interdit');}
            
        $artisan = $product->artisan;
        try {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();
            return redirect()->route('products.index', $artisan)
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('products.index', $artisan)
                ->with('error', 'Impossible de supprimer ce produit.');
        }   

    }


    public function searchPexels(Request $request)
    {
        $query = $request->input('query', 'product');
        $photos = $this->fruitImgService->searchPhotos($query, 12);
        
        return response()->json($photos);
    }


>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{

    public function index()
    {
            // Mock data for now
            $products = [
                (object) [
                    'id' => 1,
                    'name' => 'Pain Artisan',
                    'description' => 'Pain au levain fraîchement cuit',
                    'price' => 5.99,
                    'image' => '/images/bread.jpg'
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Poterie Main',
                    'description' => 'Magnifique vase en céramique',
                    'price' => 45.00,
                    'image' => '/images/pottery.jpg'
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Écharpe Laine',
                    'description' => 'Écharpe tricotée confortable',
                    'price' => 25.50,
                    'image' => '/images/scarf.jpg'
                ],
                (object) [
                    'id' => 4,
                    'name' => 'Fromage Artisan',
                    'description' => 'Cheddar affiné de la ferme locale',
                    'price' => 12.99,
                    'image' => '/images/cheese.jpg'
                ],
                (object) [
                    'id' => 5,
                    'name' => 'Bol Bois',
                    'description' => 'Bol à salade sculpté à la main',
                    'price' => 35.00,
                    'image' => '/images/bowl.jpg'
                ],
                (object) [
                    'id' => 6,
                    'name' => 'Pot Miel',
                    'description' => 'Miel pur local',
                    'price' => 8.50,
                    'image' => '/images/honey.jpg'
                ],
            ];

        return view('products.index', compact('products'));
    }

    public function addProduct(Request $request)
    {
        // récupère le produit saisi et le charge dans la base
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'image',
        ]);

        $path = $request->file("image")->storePublicly("productsImg", "public");

        try {
            DB::beginTransaction();

            $product = Product::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'image' => $path,
            ]);

            $productId = Product::latest()->first();
            // foreach ($request->input("category_id") as $key => $value) {
            $price = Price::create([
                'price' => $request->input('price'),
                'qty_available' => $request->qty_available,
                'id_artisan' => $request->user()->id,
                'id_product' => $productId->id,
            ]);    
            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
    
    +
    public function updValid(String $id,UpdateProductRequest $request): RedirectResponse
    {
        $validate = $request->validated();

        if (!empty($validate["image"]) && $validate["image"] != "undefinrd") {
            $path = $request->file("image")->storePublicly("productsImg", "public");
            $product->image = $path;
        }

        $product->name = $validate["name"];
        $product->description = $validate["description"];
        $price->price = $validate["price"];
        $price->artisant_id = $validate->user()->id;

        $product->save();
        // rend la main à l'écran d'origine?    
        return redirect('/artisan/dashboard');
    }
}

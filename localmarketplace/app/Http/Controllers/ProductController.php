<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Artisan Bread',
            'description' => 'Freshly baked sourdough bread made with traditional methods',
            'price' => 5.99,
            'image' => '/images/bread.jpg',
        ]);

        Product::create([
            'name' => 'Handmade Pottery',
            'description' => 'Beautiful ceramic vase crafted by local artisans',
            'price' => 45.00,
            'image' => '/images/pottery.jpg',
        ]);

        Product::create([
            'name' => 'Wool Scarf',
            'description' => 'Cozy knitted scarf made from natural wool',
            'price' => 25.50,
            'image' => '/images/scarf.jpg',
        ]);
    }
}

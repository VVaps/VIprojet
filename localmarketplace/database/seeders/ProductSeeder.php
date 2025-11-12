<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pain Artisan',
                'description' => 'Pain au levain fraîchement cuit dans notre boulangerie artisanale. Fait avec des ingrédients locaux et des techniques traditionnelles.',
                'price' => 5.99,
                'image' => '/images/bread.svg'
            ],
            [
                'name' => 'Poterie Main',
                'description' => 'Magnifique vase en céramique façonné à la main par notre artisan potier. Chaque pièce est unique.',
                'price' => 45.00,
                'image' => '/images/pottery.svg'
            ],
            [
                'name' => 'Écharpe Laine',
                'description' => 'Écharpe tricotée confortable en laine locale. Parfaite pour l\'hiver, douce et chaleureuse.',
                'price' => 25.50,
                'image' => '/images/scarf.svg'
            ],
            [
                'name' => 'Fromage Artisan',
                'description' => 'Cheddar affiné de la ferme locale. Fabriqué avec du lait cru selon les traditions ancestrales.',
                'price' => 12.99,
                'image' => '/images/cheese.svg'
            ],
            [
                'name' => 'Bol Bois',
                'description' => 'Bol à salade sculpté à la main dans du bois local. Finition naturelle et écologique.',
                'price' => 35.00,
                'image' => '/images/bowl.svg'
            ],
            [
                'name' => 'Pot Miel',
                'description' => 'Miel pur local direct des ruches de nos apiculteurs. Saveurs naturelles authentiques.',
                'price' => 8.50,
                'image' => '/images/honey.svg'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

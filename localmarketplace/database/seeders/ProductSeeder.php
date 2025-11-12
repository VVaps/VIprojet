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
        $artisans = \App\Models\Artisan::all();
        
        if ($artisans->isEmpty()) {
            echo "No artisans found. Please run ArtisanSeeder first.\n";
            return;
        }

        $products = [
            [
                'name' => 'Pain au Levain',
                'description' => 'Pain au levain fraîchement cuit dans notre boulangerie artisanale. Fait avec des ingrédients locaux et des techniques traditionnelles.',
                'price' => 5.99,
                'image' => '/images/bread.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Vase en Céramique',
                'description' => 'Magnifique vase en céramique façonné à la main par notre artisan potier. Chaque pièce est unique et prend des semaines à réaliser.',
                'price' => 45.00,
                'image' => '/images/pottery.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Écharpe en Laine',
                'description' => 'Écharpe tricotée confortable en laine locale. Parfaite pour l\'hiver, douce et chaude.',
                'price' => 25.50,
                'image' => '/images/scarf.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Cheddar Artisan',
                'description' => 'Cheddar affiné de la ferme locale. Fabriqué avec du lait cru selon les traditions ancestrales.',
                'price' => 12.99,
                'image' => '/images/cheese.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Bol en Bois de Chêne',
                'description' => 'Bol à salade sculpté à la main dans du bois de chêne local. Finition naturelle et écologique.',
                'price' => 35.00,
                'image' => '/images/bowl.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Miel de Fleurs Sauvages',
                'description' => 'Miel pur local direct des ruches de nos apicultrices. Saveurs naturelles authentiques et multiples.',
                'price' => 8.50,
                'image' => '/images/honey.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Terracotta Géante',
                'description' => 'Poterie traditionnelle en terre cuite de grandes dimensions. Parfaite pour les plantes d\'intérieur.',
                'price' => 65.00,
                'image' => '/images/pottery.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Chaussettes Laine',
                'description' => 'Chaussettes chauds tricotés à la main. Matériau 100% laine locale, disponibles en plusieurs tailles.',
                'price' => 18.75,
                'image' => '/images/scarf.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Planche à Découper',
                'description' => 'Planche à découper artisanale en noyer français. Longue durée de vie et design moderne.',
                'price' => 42.00,
                'image' => '/images/bowl.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Brie Fermier',
                'description' => 'Fromage Brie fermier traditionnel. Affinage optimal et goût authentique des fermes françaises.',
                'price' => 15.25,
                'image' => '/images/cheese.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Miel de Lavande',
                'description' => 'Miel parfumé à la lavande, direct de nos champs de Provence. Goût floral unique.',
                'price' => 12.00,
                'image' => '/images/honey.svg',
                'artisan_id' => $artisans->random()->id
            ],
            [
                'name' => 'Baguette Tradition',
                'description' => 'Baguette toujours fraîche cuisson du matin. Recette ancestrale de notre boulangerie artisanale.',
                'price' => 1.20,
                'image' => '/images/bread.svg',
                'artisan_id' => $artisans->random()->id
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
        
        echo "Created " . count($products) . " products with artisan associations.\n";
    }
}

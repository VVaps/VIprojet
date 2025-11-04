<?php

namespace Database\Seeders;

use App\Models\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArtisanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::create([
            'name' => 'Marie Dubois',
            'description' => 'Passionate baker specializing in traditional French breads and pastries.',
            'address' => 'Paris, France',
            'email' => 'marie.dubois@example.com',
            'phone' => '+33 1 23 45 67 89',
        ]);

        Artisan::create([
            'name' => 'Jean-Pierre Martin',
            'description' => 'Master potter creating unique ceramic pieces inspired by nature.',
            'address' => 'Lyon, France',
            'email' => 'jp.martin@example.com',
            'phone' => '+33 4 56 78 90 12',
        ]);
    }
}

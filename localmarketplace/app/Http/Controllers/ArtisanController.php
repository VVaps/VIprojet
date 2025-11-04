<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use Illuminate\Http\Request;

class ArtisanController extends Controller
{
    public function index()
    {
        // Mock data for now
        $artisans = [
            (object) [
                'id' => 1,
                'name' => 'Marie Dubois',
                'description' => 'Boulangère passionnée spécialisée dans les pains et pâtisseries traditionnels français.',
                'address' => 'Paris, France'
            ],
            (object) [
                'id' => 2,
                'name' => 'Jean-Pierre Martin',
                'description' => 'Maître potier créant des pièces uniques en céramique inspirées par la nature.',
                'address' => 'Lyon, France'
            ],
            (object) [
                'id' => 3,
                'name' => 'Sophie Laurent',
                'description' => 'Tricoteuse qualifiée produisant des vêtements et accessoires en laine de haute qualité.',
                'address' => 'Marseille, France'
            ],
        ];

        return view('artisans.index', compact('artisans'));
    }
}

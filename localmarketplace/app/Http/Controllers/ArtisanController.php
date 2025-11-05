<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtisanController extends Controller
{
     public function index1()
    {
        //renvoie seulement 10 artisans pour la page d'accueil
        $artisans = Artisan::with('user')
        ->limit(10)
        ->get();

        // // Mock data for now
        // $artisans = [
        //     (object) [
        //         'id' => 1,
        //         'name' => 'Marie Dubois',
        //         'description' => 'Boulangère passionnée spécialisée dans les pains et pâtisseries traditionnels français.',
        //         'address' => 'Paris, France'
        //     ],
        //     (object) [
        //         'id' => 2,
        //         'name' => 'Jean-Pierre Martin',
        //         'description' => 'Maître potier créant des pièces uniques en céramique inspirées par la nature.',
        //         'address' => 'Lyon, France'
        //     ],
        //     (object) [
        //         'id' => 3,
        //         'name' => 'Sophie Laurent',
        //         'description' => 'Tricoteuse qualifiée produisant des vêtements et accessoires en laine de haute qualité.',
        //         'address' => 'Marseille, France'
        //     ],
        // ];

        return view('artisans.index', compact('artisans'));
    }
    
    public function index()
    {
        $artisans = Artisan::with('user')->get();

        return view('artisans.index', compact('artisans'));
    }

    public function show(Artisan $artisan)
    {
        return view('artisans.index', compact('artisan'));
    }
    
    public function addArtisan(Artisan $artisan){
        
        if ($artisan->id_user !== Auth::id()) { abort(403, 'Accès non autorisé.');}
       
        $validated = $artisan->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'rib' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        $validated['id_user'] = Auth::id();

        Artisan::create($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan créé avec succès.');

    }

    public function updArtisan(Artisan $artisan)
    {
        if ($artisan->id_user !== Auth::id()) { abort(403, 'Accès non autorisé.'); }
        $validated = $artisan->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'rib' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        $validated['id_user'] = Auth::id();

        $artisan->update($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan mis à jour.');
    }


    public function delArtisant(Artisan $artisan)
    {
        $artisan->delete();
        return redirect()->route('artisans.index')->with('success', 'Artisan supprimé.');
    }
}

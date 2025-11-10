<?php

namespace App\Http\Controllers;

use App\Models\Artisan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ArtisanController extends Controller
{
   
    public function index()
    {
        if (Auth::check() && Auth::user()->isArtisan()) {
            // Si l'utilisateur est un artisan, montrer uniquement ses comptes
            $artisans = Auth::user()->artisans()->get();
        } else {
            // Sinon, montrer tous les artisans
            $artisans = Artisan::all();
        }

        return view('artisans.index', compact('artisans'));
    }

    public function show(Artisan $artisan)
    {
        return view('artisans.index', compact('artisan'));
    }
    
    // Annulation lors de la création (uniquement si c'est le premier)
    public function cancel()
    {
        return redirect()->route('dashboard')
            ->with('info', 'Création du compte artisan annulée');
    }

    // Formulaire création (auth)
    public function create()
    {
        return view('artisans.create');
    }


    public function addArtisan(Request $request){
        
        if (!Auth::user()->isArtisan()){ abort(403, 'Accès non autorisé.');}

       
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'lowercase', 'email', 'max:50'],
            'phone' => ['required', 'string', 'lowercase', 'max:20'], 
            'rib' => ['required','string', 'max:20'], 
            'description' => ['string', 'max:1000'], 
            'address' => ['required', 'string', 'max:255'], 
        ]);
        $validated['id_user'] = Auth::id();

        Artisan::create($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan créé avec succès.');

    }

    // Formulaire mise à jour (auth, propriétaire)
    public function edit(Artisan $artisan)
    {
         if (!Auth::user()->isArtisan())  { abort(403, 'Accès non autorisé.'); }
        return view('artisans.edit', compact('artisan'));
    }

    //mise à jour
    public function updArtisan(Request $request, Artisan $artisan)
    {

        if (!Auth::user()->isArtisan()) { abort(403, 'Accès non autorisé.'); }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'lowercase', 'email', 'max:50'],
            'phone' => ['required', 'string', 'lowercase', 'max:20'], 
            'rib' => ['required','string', 'max:20'], 
            'description' => ['string', 'max:1000'], 
            'address' => ['required', 'string', 'max:255'], 
        ]);
        $validated['id_user'] = Auth::id();

        $artisan->update($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan mis à jour.');
    }


    public function delArtisan(Artisan $artisan)
    {
          if (!Auth::user()->isArtisan())  { abort(403, 'Accès non autorisé.'); }
         
        
        try {
            $artisan->delete();
            return redirect()->route('artisans.index')
                ->with('success', 'Artisan supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('artisans.index')
                ->with('error', 'Impossible de supprimer cet artisan : il est lié à des produits ou d\'autres données.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtisanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check() && Auth::user()->isArtisan()) {
            // If user is an artisan, show only their accounts
            $artisans = Auth::user()->artisans()->get();
        } else {
            // Otherwise, show all artisans
            $artisans = Artisan::with('user')->get();
        }

        return view('artisans.index', compact('artisans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::check()) {
            abort(403, 'Access denied. You must be logged in to create an artisan profile.');
        }
        
        // Check if user already has an artisan profile
        if (Auth::user()->artisans()->exists()) {
            return redirect()->route('artisans.index')
                ->with('error', 'Vous avez déjà un profil d\'artisan.');
        }
        
        return view('artisans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            abort(403, 'Access denied. You must be logged in to create an artisan profile.');
        }

        // Check if user already has an artisan profile
        if (Auth::user()->artisans()->exists()) {
            return redirect()->route('artisans.index')
                ->with('error', 'Vous avez déjà un profil d\'artisan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'], 
            'rib' => ['required', 'string', 'max:20'], 
            'description' => ['nullable', 'string', 'max:1000'], 
            'address' => ['required', 'string', 'max:255'], 
        ]);

        $validated['user_id'] = Auth::id();
        
        // Update user's type to artisan when creating first artisan profile
        $user = Auth::user();
        if ($user->user_type !== 'artisan') {
            User::where('id', Auth::id())->update(['user_type' => 'artisan']);
        }

        Artisan::create($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Artisan $artisan)
    {
        $artisan->load('user', 'products');
        return view('artisans.show', compact('artisan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artisan $artisan)
    {
        if (!Auth::check() || $artisan->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }
        
        return view('artisans.edit', compact('artisan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artisan $artisan)
    {
        if (!Auth::check() || $artisan->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'], 
            'rib' => ['required', 'string', 'max:20'], 
            'description' => ['nullable', 'string', 'max:1000'], 
            'address' => ['required', 'string', 'max:255'], 
        ]);

        $artisan->update($validated);

        return redirect()->route('artisans.index')->with('success', 'Artisan mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artisan $artisan)
    {
        if (!Auth::check() || $artisan->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }
        
        try {
            $artisan->delete();
            return redirect()->route('artisans.index')
                ->with('success', 'Artisan supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('artisans.index')
                ->with('error', 'Impossible de supprimer cet artisan : il est lié à des produits ou d\'autres données.');
        }
    }

    /**
     * Cancel creation (redirect only)
     */
    public function cancel()
    {
        return redirect()->route('dashboard')
            ->with('info', 'Artisan creation cancelled');
    }
}

<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Artisan;

class EnsureUserIsArtisan
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }
        
        $artisan = Artisan::where('id_user', $user->id)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$artisan) {
            return response()->json(['message' => 'Accès réservé aux artisans'], 403);
        }
        
        // Ajouter l'artisan à la requête pour y accéder facilement
        $request->merge(['artisan' => $artisan]);
        
        return $next($request);
    }
}
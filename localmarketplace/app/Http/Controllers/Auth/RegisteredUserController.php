<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artisan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_artisan' => 'boolean',
        ]);

        if ($request->has('is_artisan') && $request->is_artisan) {
        // if (isset($data['is_artisan']) && $data['is_artisan']) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['string', 'lowercase', 'email', 'max:50'],
                'phone' => ['required', 'string', 'lowercase', 'max:20'], 
                'rib' => ['required','string', 'max:20'], 
                'description' => ['string', 'max:1000'], 
                'address' => ['required', 'string', 'max:255'], 
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Si la case "Je suis artisan" est cochée, créer le premier compte artisan
        if ($request->has('is_artisan') && $request->is_artisan) {
            Artisan::create([
                'name' => $request->artisan_name,
                'email' => $request->artisan_email,
                'phone' => $request->phone,
                'address' =>$request->address,
                'rib' => $request->rib,
                'description' => $request->description,
                'id_user' => $user->id,
            ]);
        }

        return redirect(route('dashboard', absolute: false));
    }
}

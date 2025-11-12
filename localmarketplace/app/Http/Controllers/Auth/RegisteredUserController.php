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
            'user_type' => ['required', 'in:customer,artisan'],
        ]);

        // Additional validation for artisan users
        if ($request->user_type === 'artisan') {
            $request->validate([
                'artisan_name' => ['required', 'string', 'max:255'],
                'artisan_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        event(new Registered($user));

        // If artisan, create artisan record
        if ($request->user_type === 'artisan') {
            Artisan::create([
                'name' => $request->artisan_name,
                'email' => $request->artisan_email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'user_id' => $user->id,
            ]);
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

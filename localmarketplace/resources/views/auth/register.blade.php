<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
    
            <!-- User Type Selection -->
            <div class="mt-4">
                <x-input-label for="user_type" value="Type de compte" />
                <select id="user_type" name="user_type" class="block mt-1 w-full" required>
                    <option value="">Sélectionnez votre type de compte</option>
                    <option value="customer" {{ old('user_type') == 'customer' ? 'selected' : '' }}>
                        Client - Je veux acheter des produits artisanaux
                    </option>
                    <option value="artisan" {{ old('user_type') == 'artisan' ? 'selected' : '' }}>
                        Artisan - Je veux vendre mes produits artisanaux
                    </option>
                </select>
                <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                <p class="mt-1 text-sm text-gray-600">
                    Cette sélection peut être modifiée plus tard depuis votre profil.
                </p>
            </div>
    
            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    Déjà inscrit ?
                </a>
    
                <x-primary-button class="ms-4">
                    S'inscrire
                </x-primary-button>
            </div>
        </form>
    </x-guest-layout>

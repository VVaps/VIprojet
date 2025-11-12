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
<<<<<<< HEAD
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
=======
        </div>

<!-- Gestion de l'utilisateur en temps qu'artisan -->
<div class="mt-4 form-check">        
    <input type="checkbox" class="form-check-input" id="is_artisan" name="is_artisan" 
                value="1" onchange="toggleArtisanFields()" {{ old('is_artisan') ? 'checked' : '' }}>
    <label class="form-check-label" for="is_artisan">             
        Je suis un artisan
    </label>
</div>

<div id="artisan-fields" style="display: none;">
    <hr>
    <h5>Informations artisan</h5>
    <div class="mb-3">
        <label for="artisan_name" class="form-label">Nom de l'entreprise *</label>
        <input type="text" class="form-control @error('artisan_name') is-invalid @enderror" 
               id="artisan_name" name="artisan_name" value="{{ old('artisan_name') }}">
        @error('artisan_name')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description de l'activité</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
        @error('description')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Téléphone</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
               id="phone" name="phone" value="{{ old('phone') }}">
        @error('phone')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="artisan_email" class="form-label">Email de l'entreprise</label>
        <input type="email" class="form-control @error('artisan_email') is-invalid @enderror" 
               id="artisan_email" name="artisan_email" value="{{ old('artisan_email') }}">
        @error('artisan_email')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Adresse *</label>
        <input type="text" class="form-control @error('address') is-invalid @enderror" 
               id="address" name="address" value="{{ old('address') }}">
        @error('address')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="rib" class="form-label">RIB *</label>
        <input type="text" class="form-control @error('rib') is-invalid @enderror" 
               id="rib" name="rib" value="{{ old('rib') }}">
        @error('rib')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<script>
    function toggleArtisanFields() {
        const checkbox = document.getElementById('is_artisan');
        const fields = document.getElementById('artisan-fields');
        fields.style.display = checkbox.checked ? 'block' : 'none';
        
        // Rendre les champs obligatoires requis ou non selon la case cochée
        const artisanName = document.getElementById('artisan_name');
        const address = document.getElementById('address');
        const rib = document.getElementById('rib');
        
        if (checkbox.checked) {
            artisanName.setAttribute('required', 'required');
            address.setAttribute('required', 'required');
            rib.setAttribute('required', 'required');
        } else {
            artisanName.removeAttribute('required');
            address.removeAttribute('required');
            rib.removeAttribute('required');
        }
    }

    // Afficher les champs si la case était cochée après une erreur de validation
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('is_artisan');
        if (checkbox && checkbox.checked) {
            toggleArtisanFields();
        }
    });
</script>

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
>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362

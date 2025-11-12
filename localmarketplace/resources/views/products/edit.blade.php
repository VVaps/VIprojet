@extends('layouts.app')

@section('title', 'Modifier le produit')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Modifier le produit</h1>
            <p class="mt-2 text-gray-600">Modifiez les informations du produit ci-dessous.</p>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="space-y-6">
                    <!-- Nom du produit -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du produit *
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $product->name) }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description *
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Prix (€) *
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               value="{{ old('price', $product->price) }}" 
                               step="0.01" 
                               min="0"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Artisan -->
                    <div>
                        <label for="artisan_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Artisan *
                        </label>
                        <select name="artisan_id" 
                                id="artisan_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('artisan_id') border-red-500 @enderror">
                            <option value="">Sélectionnez un artisan</option>
                            @foreach($artisans as $artisan)
                                <option value="{{ $artisan->id }}" {{ old('artisan_id', $product->artisan_id) == $artisan->id ? 'selected' : '' }}>
                                    {{ $artisan->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('artisan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image actuelle -->
                    @if($product->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Image actuelle
                        </label>
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-32 h-32 object-cover rounded-lg border">
                    </div>
                    @endif

                    <!-- Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $product->image ? 'Remplacer l\'image' : 'Image du produit' }}
                        </label>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('image') border-red-500 @enderror">
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <div class="flex space-x-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sauvegarder les modifications
                        </button>
                        <a href="{{ route('products.show', $product) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                    </div>
                    
                    <form action="{{ route('products.destroy', $product) }}" 
                          method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Supprimer le produit
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
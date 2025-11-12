<?php use Illuminate\Support\Str; ?>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(isset($artisan) && $artisan)
                        <div class="mb-4">
                            <a href="{{ route('artisans.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Retour aux artisans
                            </a>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Produits de {{ $artisan->name }}</h1>
                        <p class="text-gray-600 mb-6">Découvrez tous les produits créés par cet artisan</p>
                    @else
                        <h1 class="text-3xl font-bold text-gray-900 mb-6">Les Produits</h1>
                    @endif

                    @auth
                    {{-- Only show create button and filters when not viewing a specific artisan --}}
                    @if(!isset($artisan) || !$artisan)
                    <div class="mb-6 flex items-center space-x-4">
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Créer un produit
                        </a>
                        
                        {{-- Only show filter for artisan users --}}
                        @if(Auth::user()->user_type === 'artisan')
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="showOnlyMyProducts"
                                   name="show_only_my_products"
                                   value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ (isset($showOnlyMyProducts) && $showOnlyMyProducts) ? 'checked' : '' }}>
                            <label for="showOnlyMyProducts" class="ml-2 block text-sm text-gray-900">
                                Voir uniquement mes produits
                            </label>
                        </div>
                        @endif
                    </div>
                    @else
                    {{-- Show "Voir tous les produits" when viewing a specific artisan --}}
                    <div class="mb-6">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            Voir tous les produits
                        </a>
                    </div>
                    @endif
                    @endauth

                    @if($products->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-4v2a2 2 0 01-2 2H8a2 2 0 01-2-2V9m8 0H8"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                @if(isset($artisan) && $artisan)
                                    Aucun produit disponible
                                @else
                                    Aucun produit disponible
                                @endif
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(isset($artisan) && $artisan)
                                    {{ $artisan->name }} n'a pas encore créé de produits.
                                @else
                                    Commencez par créer votre premier produit.
                                @endif
                            </p>
                            @auth
                                @if(!isset($artisan) || !$artisan)
                                <div class="mt-6">
                                    <a href="{{ route('products.create') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Créer un produit
                                    </a>
                                </div>
                                @endif
                            @endauth
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                                        @if($product->image)
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-500">Aucune image</span>
                                        @endif
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                        <p class="text-gray-600 mb-2">{{ Str::limit($product->description, 80) }}</p>
                                        
                                        {{-- Hide artisan info when viewing specific artisan's products --}}
                                        @if(!isset($artisan) || !$artisan)
                                            @if($product->artisan)
                                                <p class="text-sm text-gray-500 mb-4">Par: {{ $product->artisan->name }}</p>
                                            @else
                                                <p class="text-sm text-gray-500 mb-4">Artisan non renseigné</p>
                                            @endif
                                        @endif
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <a href="{{ route('products.show', $product->id) }}"
                                               class="flex-1 bg-gray-600 text-white px-4 py-2 rounded text-center hover:bg-gray-700 transition duration-300">
                                                Voir les détails
                                            </a>
                                            
                                            @auth
                                            <button onclick="addToCart({{ $product->id }})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                                Ajouter au panier
                                            </button>
                                            @else
                                            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                                Se connecter
                                            </a>
                                            @endauth
                                        </div>
                                        
                                        @auth
                                            @if(auth()->user()->isArtisan() && auth()->user()->artisans->contains($product->artisan_id))
                                            <div class="mt-3 flex space-x-2 border-t pt-3">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                   class="flex-1 bg-yellow-600 text-white px-3 py-1 rounded text-sm text-center hover:bg-yellow-700 transition duration-300">
                                                    Modifier
                                                </a>
                                                <form action="{{ route('products.destroy', $product->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');"
                                                      class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition duration-300">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter products functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('showOnlyMyProducts');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    const url = new URL(window.location);
                    if (this.checked) {
                        url.searchParams.set('show_only_my_products', '1');
                    } else {
                        url.searchParams.delete('show_only_my_products');
                    }
                    window.location.href = url.toString();
                });
            }
        });

        // Add to cart function
        function addToCart(productId) {
            @auth
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Update cart count in navigation if available
                    if (window.Alpine && document.querySelector('[x-data]')) {
                        const navData = document.querySelector('[x-data]')._x_dataStack[0];
                        if (navData && navData.cartCount !== undefined) {
                            navData.cartCount = data.cart_count;
                        }
                    }
                } else {
                    showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showNotification('Erreur lors de l\'ajout au panier', 'error');
            });
            @else
            showNotification('Vous devez être connecté pour ajouter des produits au panier', 'error');
            @endauth
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>

<?php use Illuminate\Support\Str; ?>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Nos Produits</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                                <div class="h-48 bg-gray-200 flex items-center justify-center">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ Str::limit($product->description, 80) }}</p>
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.show', $product->id) }}"
                                           class="flex-1 bg-gray-600 text-white px-4 py-2 rounded text-center hover:bg-gray-700 transition duration-300">
                                            Voir les détails
                                        </a>
                                        <button onclick="addToCart({{ $product->id }})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                            Ajouter au panier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

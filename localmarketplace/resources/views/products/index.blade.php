<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Les Produits</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- utilisateur connecté --}}
                    @if (Auth::user()) 
                        {{-- artisan ou pas encore de catalogue || ($products->isEmpty())  --}}
                        @if($isOwner)
                            <a href="{{ route('products.create', $artisan) }}" class="btn btn-primary mb-3">Créer un produit</a>
                        @endif

                        @foreach($products as $product)
                            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                                <div class="h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Image temporaire</span>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                                    <p class="text-gray-600 mb-4">{{ $product->artisan->name }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                        {{-- artisan et propriétaire du catalogue --}}
                                        @if($isOwner)
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Modifier</a>
                                            <form action="{{ route('products.index', $product) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"> 
                                                    Supprimer
                                                </button>
                                            </form>
                                        @else
                                            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                                Ajouter au panier
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- utilisateur non connecté --}}
                        @foreach($products as $product)
                                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Image temporaire</span>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                        <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                                        <p class="text-gray-600 mb-4">{{ $product->artisan->name }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                                Ajouter au panier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    @endif    
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
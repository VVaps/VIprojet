<x-app-layout>
    <div class="bg-white">
        <!-- Hero Carousel -->
        <div class="relative overflow-hidden bg-gray-900">
            <div class="relative h-96 sm:h-[500px]">
                <!-- Slide 1 -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h1 class="text-4xl sm:text-6xl font-bold mb-4">Bienvenue sur ArtisLoca</h1>
                        <p class="text-xl sm:text-2xl mb-8">Découvrez l'artisanat local de qualité</p>
                        <a href="{{ route('products.index') }}" class="bg-white text-gray-900 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                            Explorer nos produits
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos Produits</h2>
                    <p class="text-lg text-gray-600">Découvrez notre sélection d'artisanat local</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products ?? [] as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Image temporaire</span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                        Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Voir tous les produits
                    </a>
                </div>
            </div>
        </div>

        <!-- Artisans Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos Artisans</h2>
                    <p class="text-lg text-gray-600">Rencontrez les talents derrière nos créations</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($artisans ?? [] as $artisan)
                        <div class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition duration-300">
                            <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <span class="text-gray-600 text-2xl">{{ substr($artisan->name, 0, 1) }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $artisan->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $artisan->description }}</p>
                            <p class="text-sm text-gray-500">{{ $artisan->address }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('artisans.index') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                        Découvrir tous les artisans
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
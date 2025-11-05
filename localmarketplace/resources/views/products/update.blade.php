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
                                    <span class="text-gray-500">Image temporaire</span>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-green-600">{{ number_format($product->price, 2) }} €</span>
                                        <span class="text-2xl font-bold text-green-600">{{ number_format($price->qty_available, 2)?? 'Stock non précisé' }} €</span>
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                                            Modifier
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
</x-app-layout>
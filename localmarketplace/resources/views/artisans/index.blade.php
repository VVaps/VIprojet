<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Nos Artisans</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($artisans as $artisan)
                            <div class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition duration-300">
                                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <span class="text-gray-600 text-2xl">{{ substr($artisan->name, 0, 1) }}</span>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $artisan->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ $artisan->description }}</p>
                                <p class="text-sm text-gray-500">{{ $artisan->address }}</p>
                                <p class="text-sm text-gray-500">{{ $artisan->email }}?? pas d'email</p>
                                <p class="text-sm text-gray-500">{{ $artisan->phone }}?? pas de téléphone</p>
                                <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
                                    Voir les produits
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
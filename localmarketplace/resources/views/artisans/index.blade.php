<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">
                        @auth
                            @if(Auth::user()->isArtisan())
                                Mes comptes artisan
                            @else
                                Nos artisans
                            @endif
                        @else
                            Nos artisans
                        @endauth
                    </h1>

                    @auth
                        @if(!Auth::user()->isArtisan())
                            <div class="mb-6">
                                <a href="{{ route('artisans.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Créer un profil artisan
                                </a>
                            </div>
                        @endif
                    @endauth

                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($artisans->isEmpty())
                        <div class="alert alert-info">
                            @auth
                                @if(Auth::user()->isArtisan())
                                    Vous n'avez pas encore de compte artisan.
                                @else
                                    Aucun artisan enregistré pour le moment.
                                @endif
                            @else
                                Aucun artisan enregistré pour le moment.
                            @endauth
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($artisans as $artisan)
                                <div class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition duration-300">
                                    <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                        <span class="text-gray-600 text-2xl">{{ substr($artisan->name, 0, 1) }}</span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $artisan->name }}</h3>
                                    @if($artisan->description)
                                        <p class="text-gray-600 mb-4">{{ Str::limit($artisan->description, 100) }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">{{ $artisan->address }}</p>
                                    @if($artisan->email)
                                        <p class="text-sm text-gray-500">{{ $artisan->email }}</p>
                                    @endif
                                    @if($artisan->phone)
                                        <p class="text-sm text-gray-500">{{ $artisan->phone }}</p>
                                    @endif
                                    <a href="{{ route('products.index', ['artisan' => $artisan->id]) }}" 
                                       class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
                                        Voir les produits
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
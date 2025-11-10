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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @auth
                            @if(Auth::user()->isArtisan())
                                <a href="{{ route('artisans.create') }}" class="btn btn-primary mb-3">Créer un compte artisan</a>
                            @endif
                        @endauth

                        @if(session('success'))
                            <div class="alert alert-success">
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
                            @foreach($artisans as $artisan)
                                <div class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition duration-300">
                                    <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                        <span class="text-gray-600 text-2xl">{{ substr($artisan->name, 0, 1) }}</span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $artisan->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $artisan->description }}</p>
                                    <p class="text-sm text-gray-500">{{ $artisan->address }}</p>
                                    <p class="text-sm text-gray-500">{{ $artisan->email ?? 'pas d\'email'}}</p>
                                    <p class="text-sm text-gray-500">{{ $artisan->phone }}</p>
                                    <a href="{{ route('products.index', compact('artisan')) }}" class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
                                        Voir les produits
                                    </a>
                                    @auth
                                        @if($artisan->id_user === Auth::id())
                                            <a href="{{ route('artisans.edit', $artisan) }}" class="btn btn-sm btn-warning">
                                                Modifier
                                            </a>
                                            <form action="{{ route('artisans.delete', $artisan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte artisan ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
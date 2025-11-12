<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'localMarketPlace') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">ArtisLoca</h3>
                            <p class="text-gray-600">Connecter les artisans locaux avec les clients qui apprécient l'artisanat de qualité.</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Liens Rapides</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Accueil</a></li>
                                <li><a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">Produits</a></li>
                                <li><a href="{{ route('artisans.index') }}" class="text-gray-600 hover:text-gray-900">Artisans</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Contact</h4>
                            <p class="text-gray-600">Email: contact@artisloca.com</p>
                            <p class="text-gray-600">Téléphone: +33 1 23 45 67 89</p>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-gray-200 pt-8 text-center">
                        <p class="text-gray-600">&copy; 2024 ArtisLoca. Tous droits réservés.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900">Finaliser la commande</h1>
                        <p class="mt-2 text-gray-600">Vérifiez votre commande et confirmez pour procéder au paiement.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erreur lors du traitement de la commande :</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($cartItems->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Votre panier est vide</h3>
                                    <p class="mt-1 text-sm text-yellow-700">Ajoutez des produits à votre panier avant de finaliser une commande.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('products.index') }}" class="text-sm text-yellow-800 hover:text-yellow-900 font-medium">
                                            Continuer les achats →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif de votre commande</h3>
                            
                            <div class="space-y-4">
                                @foreach($cartItems as $item)
                                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                        <div class="flex items-center space-x-4">
                                            @if($item->product->image)
                                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-gray-500 text-sm">Pas d'image</span>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $item->product->name }}</h4>
                                                @if($item->product->artisan)
                                                    <p class="text-sm text-gray-500">Par {{ $item->product->artisan->name }}</p>
                                                @endif
                                                <p class="text-sm text-gray-600">{{ number_format($item->product->price, 2) }} € x {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">{{ number_format($item->total_price, 2) }} €</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ number_format($totalAmount, 2) }} €</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('cart.index') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Retour au panier
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Confirmer la commande
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
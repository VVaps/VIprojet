<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Mes Commandes</h1>
                <p class="mt-2 text-gray-600">Consultez l'historique de vos commandes.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande</h3>
                            <p class="text-gray-500 mb-6">Vous n'avez pas encore passé de commande.</p>
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Voir les produits
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-6 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">
                                                Commande #{{ $order->id }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                Passée le {{ $order->created_at->format('d/m/Y à H:i') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Total</dt>
                                            <dd class="text-sm text-gray-900">{{ number_format($order->total_amount, 2) }} €</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Statut paiement</dt>
                                            <dd class="text-sm text-gray-900">{{ ucfirst($order->payment_status) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Articles</dt>
                                            <dd class="text-sm text-gray-900">{{ $order->orderItems->sum('quantity') }} articles</dd>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Voir les détails
                                        </a>
                                        
                                        @if($order->status === 'pending')
                                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                                    Annuler
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
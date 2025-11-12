<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Commande #{{ $order->id }}</h1>
                        <p class="mt-2 text-gray-600">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Articles commandés</h3>
                        <div class="space-y-4">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center space-x-4 p-4 border rounded-lg">
                                    @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">Pas d'image</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ Str::limit($item->product->description, 100) }}</p>
                                        <p class="text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} € x {{ $item->quantity }}</p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->total_price, 2) }} €
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex justify-between text-base font-medium text-gray-900">
                            <span>Total</span>
                            <span>{{ number_format($order->total_amount, 2) }} €</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Statut de paiement: {{ ucfirst($order->payment_status) }}</p>
                            @if($order->shipping_address)
                                <p class="mt-1">Adresse de livraison: {{ $order->shipping_address }}</p>
                            @endif
                            @if($order->notes)
                                <p class="mt-1">Notes: {{ $order->notes }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Retour aux commandes
                        </a>
                        
                        @if($order->status === 'pending')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                    Annuler la commande
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
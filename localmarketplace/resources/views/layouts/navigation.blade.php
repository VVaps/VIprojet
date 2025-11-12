<!-- Fixed navigation with proper Alpine.js scope and no conflicts -->
<script>
// Fixed navigation component with proper Alpine.js scope
function navigationComponent() {
  return {
    open: false,
    cartOpen: false,
    cartItems: [],
    cartCount: 0,
    loading: false,
    error: null,
    _fetchController: null,
    logoutLoading: false,
    logoutError: null,

    // Helper: read CSRF token reliably
    getCsrf() {
      const m = document.querySelector('meta[name="csrf-token"]');
      return m ? m.getAttribute('content') : null;
    },

    // Enhanced logout functionality with comprehensive error handling
    async confirmLogout() {
      if (!confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        return;
      }

      this.logoutLoading = true;
      this.logoutError = null;

      try {
        const csrf = this.getCsrf();
        if (!csrf) {
          throw new Error('CSRF token not found. Please refresh the page and try again.');
        }

        console.log('Starting logout process...');
        const res = await fetch('/logout', {
          method: 'POST',
          headers: { 
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
          }
        });

        console.log('Logout response status:', res.status);
        console.log('Logout response headers:', Object.fromEntries(res.headers.entries()));

        // Try to parse JSON response
        let data;
        try {
          data = await res.json();
        } catch (e) {
          // If JSON parsing fails, try to get text
          const text = await res.text();
          console.error('Response is not JSON:', text);
          throw new Error('Server returned an invalid response');
        }

        console.log('Logout response data:', data);

        if (res.ok && data.success) {
          console.log('Logout successful');
          // Clear cart data
          this.cartItems = [];
          this.cartCount = 0;
          
          // Redirect if specified
          if (data.redirect) {
            console.log('Redirecting to:', data.redirect);
            window.location.href = data.redirect;
          } else {
            console.log('Reloading page...');
            // Fallback: reload page to update UI
            window.location.reload();
          }
        } else {
          throw new Error(data.message || 'Erreur lors de la déconnexion');
        }
      } catch (err) {
        console.error('Logout error:', err);
        this.logoutError = 'Erreur lors de la déconnexion: ' + err.message;
        
        // Show detailed error to user
        alert('Erreur lors de la déconnexion: ' + err.message + '\n\nDétails techniques: ' + err.toString());
        
        // Fallback: try traditional form submission
        console.log('Attempting fallback logout via form submission...');
        await this.fallbackLogout();
      } finally {
        this.logoutLoading = false;
      }
    },

    // Fallback logout using traditional form submission
    async fallbackLogout() {
      try {
        // Create a temporary form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';
        form.style.display = 'none';

        // Add CSRF token
        const csrfToken = this.getCsrf();
        if (csrfToken) {
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = csrfToken;
          form.appendChild(csrfInput);
        }

        // Add the form to body and submit
        document.body.appendChild(form);
        form.submit();
        
        console.log('Fallback logout form submitted');
      } catch (err) {
        console.error('Fallback logout failed:', err);
        alert('Both logout methods failed. Please refresh the page and try again, or contact support.');
      }
    },

    // Open cart and fetch items only when needed (or when forceReload true)
    async openCart({ forceReload = false } = {}) {
      this.cartOpen = true;

      // Cancel any stale fetch before starting a new one
      if (this._fetchController) {
        this._fetchController.abort();
        this._fetchController = null;
      }

      // Only load if local items empty or explicit refresh requested
      if (this.cartItems.length === 0 || forceReload) {
        await this.loadCart();
      }
    },

    // Close cart (cancel any inflight fetch)
    closeCart() {
      this.cartOpen = false;
      if (this._fetchController) {
        this._fetchController.abort();
        this._fetchController = null;
      }
    },

    // Primary loader: fetch cart items from server with abort control
    async loadCart() {
      const csrf = this.getCsrf();
      if (!csrf) {
        console.warn('CSRF token not found — skipping cart load.');
        return;
      }

      // AbortController to prevent race conditions if loadCart called multiple times
      this._fetchController = new AbortController();
      const signal = this._fetchController.signal;

      this.loading = true;
      this.error = null;

      try {
        const res = await fetch('/cart', {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
          signal
        });

        if (!res.ok) {
          throw new Error('Network response was not ok: ' + res.status);
        }

        const data = await res.json();

        // Validate expected shape
        if (data && data.success && Array.isArray(data.items)) {
          // Normalize numeric values (ensure total_price present)
          this.cartItems = data.items.map(item => {
            // ensure item.total_price exists and is a number
            const total_price = parseFloat(item.total_price ?? (item.quantity * (item.product?.price ?? 0))) || 0;
            // copy safe product fields
            const product = item.product || {};
            return {
              ...item,
              quantity: Number(item.quantity || 0),
              total_price,
              product: {
                name: product.name ?? '',
                description: product.description ?? '',
                price: Number(product.price ?? 0),
                image: product.image ?? '/images/placeholder.svg'
              }
            };
          });

          // Prefer `total_count` if provided, else sum quantities
          this.cartCount = Number(data.total_count ?? this.cartItems.reduce((s, i) => s + (Number(i.quantity) || 0), 0));
        } else {
          // Unexpected response; clear cart safely
          console.warn('Unexpected /cart response shape, clearing local cart.');
          this.cartItems = [];
          this.cartCount = 0;
        }
      } catch (err) {
        if (err.name === 'AbortError') {
          // expected when we cancel previous requests
          console.info('Cart load aborted.');
        } else {
          console.error('Error loading cart:', err);
          this.error = 'Unable to load cart';
        }
      } finally {
        this.loading = false;
        // clear controller after completion
        this._fetchController = null;
      }
    },

    // Load only the cart count (badge). Lightweight.
    async loadCartCount() {
      const csrf = this.getCsrf();
      if (!csrf) return;

      try {
        const res = await fetch('/cart/count', {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
        });
        const data = await res.json();
        if (data && data.success) {
          this.cartCount = Number(data.count ?? 0);
        }
      } catch (err) {
        console.error('Error loading cart count:', err);
      }
    },

    // Optimistic quantity update: update local state immediately, send request in background
    async updateQuantity(itemId, newQuantity) {
      if (newQuantity < 1) return;

      // Find index and backup
      const idx = this.cartItems.findIndex(i => i.id == itemId);
      if (idx === -1) return;

      // Backup for rollback
      const backup = { ...this.cartItems[idx] };

      // Optimistically update local item
      const item = this.cartItems[idx];
      item.quantity = Number(newQuantity);
      item.total_price = Number((item.product?.price ?? 0) * item.quantity);
      this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);

      const csrf = this.getCsrf();
      if (!csrf) {
        console.warn('CSRF missing — skipping server update.');
        return;
      }

      try {
        const res = await fetch(`/cart/${itemId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({ quantity: item.quantity })
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
          // Rollback on failure
          console.warn('Update failed, rolling back:', data);
          this.cartItems.splice(idx, 1, backup);
          this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);
          // Optionally re-sync full cart if provided by response
          if (data && data.items) {
            await this.loadCart();
          }
        } else {
          // If server returned authoritative totals, merge them
          if (data.item) {
            this.cartItems.splice(idx, 1, {
              ...this.cartItems[idx],
              ...data.item,
              quantity: Number(data.item.quantity ?? this.cartItems[idx].quantity),
              total_price: Number(data.item.total_price ?? this.cartItems[idx].total_price)
            });
            this.cartCount = Number(data.total_count ?? this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0));
          }
        }
      } catch (err) {
        console.error('Error updating quantity:', err);
        // rollback
        this.cartItems.splice(idx, 1, backup);
        this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);
      }
    },

    // Optimistic remove: remove local item, notify server; rollback on failure
    async removeItem(itemId) {
      const idx = this.cartItems.findIndex(i => i.id == itemId);
      if (idx === -1) return;

      const backup = this.cartItems[idx];
      // Remove locally
      this.cartItems.splice(idx, 1);
      this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);

      const csrf = this.getCsrf();
      if (!csrf) {
        console.warn('CSRF missing — skipping server remove.');
        return;
      }

      try {
        const res = await fetch(`/cart/${itemId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          }
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
          console.warn('Remove failed, rolling back.', data);
          // rollback
          this.cartItems.splice(idx, 0, backup);
          this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);
        } else {
          // server may provide updated total_count
          if (data.total_count !== undefined) {
            this.cartCount = Number(data.total_count);
          }
        }
      } catch (err) {
        console.error('Error removing item:', err);
        // rollback on error
        this.cartItems.splice(idx, 0, backup);
        this.cartCount = this.cartItems.reduce((s, it) => s + (Number(it.quantity) || 0), 0);
      }
    },

    // Clear cart: empties local state after server confirmation
    async clearCart() {
      if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) return;

      const csrf = this.getCsrf();
      if (!csrf) {
        console.warn('CSRF missing — skipping clear.');
        return;
      }

      try {
        const res = await fetch('/cart/clear', {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
        });

        const data = await res.json();
        if (res.ok && data.success) {
          this.cartItems = [];
          this.cartCount = 0;
        } else {
          console.warn('Clear cart failed:', data);
        }
      } catch (err) {
        console.error('Error clearing cart:', err);
      }
    },

    getTotal() {
      return this.cartItems.reduce((total, item) => total + Number(item.total_price || 0), 0);
    },

    // init called by Alpine
    init() {
      // load badge count once on init
      this.loadCartCount();
      
      // Log initialization for debugging
      console.log('Navigation component initialized');
      console.log('CSRF Token available:', !!this.getCsrf());
    }
  };
}

// Initialize after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing components...');
  
  // Wait a bit for Alpine to initialize
  setTimeout(() => {
    const navComponent = document.querySelector('nav[x-data]');
    if (navComponent && navComponent._x_dataStack) {
      const data = navComponent._x_dataStack[0];
      if (data) {
        console.log('Navigation component found and initialized');
        if (typeof data.loadCartCount === 'function') {
          data.loadCartCount();
        }
      }
    } else {
      console.warn('Navigation component not found or not initialized');
    }
  }, 200);
});
</script>

<nav x-data="navigationComponent()" class="bg-white border-b border-gray-100">
    <!-- FIXED: Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                        ArtisLoca
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Nos Produits') }}
                    </x-nav-link>
                    <x-nav-link :href="route('artisans.index')" :active="request()->routeIs('artisans.*')">
                        {{ __('Nos Artisans') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                            {{ __('Mes Commandes') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- FIXED: Authentication Links and Cart (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Cart Button -->
                <button @click="openCart()" class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 mr-4">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
                    </svg>
                    <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                </button>

                @auth
                    <!-- User Dropdown for Authenticated Users -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            

                            <!-- Logout Option -->
                            <button @click="confirmLogout"
                                    :disabled="logoutLoading"
                                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50">
                                <span x-show="!logoutLoading">Déconnexion</span>
                                <span x-show="logoutLoading">Déconnexion...</span>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Login/Register Links for Guests -->
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium">Register</a>
                    </div>
                @endauth
            </div>

            <!-- FIXED: Hamburger + Mobile Basket + Authentication -->
            <div class="-me-2 flex items-center sm:hidden space-x-2">
                <!-- FIXED: Mobile Basket - Using openCart() method -->
                <button @click="openCart()" class="relative inline-flex items-center px-2 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
                    </svg>
                    <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"></span>
                </button>
                
                <!-- Hamburger -->
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Nos Produits') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('artisans.index')" :active="request()->routeIs('artisans.*')">
                {{ __('Nos Artisans') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    {{ __('Mes Commandes') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    
                    
                    
                    <!-- Mobile Logout Button -->
                    <button @click="confirmLogout"
                            :disabled="logoutLoading"
                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50">
                        <span x-show="!logoutLoading">Déconnexion</span>
                        <span x-show="logoutLoading">Déconnexion...</span>
                    </button>
                </div>
            @else
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}" class="block text-gray-600 hover:text-gray-900">Login</a>
                    <a href="{{ route('register') }}" class="block bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-center">Register</a>
                </div>
            @endauth
        </div>
    </div>

    <!-- FIXED: Cart Side Menu Overlay - Using closeCart() method -->
    <div 
        x-show="cartOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        x-cloak>
        
        <!-- Background Overlay -->
        <div @click="closeCart()" class="absolute inset-0"></div>
    </div>

    <!-- FIXED: Cart Sidebar - Removed translate-x-full, using x-transition only -->
    <div 
        x-show="cartOpen"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl z-50"
        x-cloak>
        
        <!-- Cart Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Mon Panier</h2>
            <!-- FIXED: Close button using closeCart() method -->
            <button @click="closeCart()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex-1 overflow-y-auto" style="max-height: calc(100vh - 140px);">
            <div x-show="cartItems.length === 0" class="text-center py-8 px-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3" />
                </svg>
                <p class="mt-2 text-gray-500">Votre panier est vide</p>
            </div>

            <div x-show="cartItems.length > 0" class="p-4 space-y-4">
                <template x-for="item in cartItems" :key="item.id">
                    <div class="flex items-start space-x-3 border-b border-gray-100 pb-4">
                        <img :src="item.product.image" :alt="item.product.name" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate" x-text="item.product.name"></h3>
                            <p class="text-sm text-gray-500 line-clamp-2" x-text="item.product.description.substring(0, 50) + (item.product.description.length > 50 ? '...' : '')"></p>
                            <p class="text-sm font-semibold text-gray-900 mt-1" x-text="item.product.price.toFixed(2) + ' €'"></p>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-2 mt-2">
                                <button @click="updateQuantity(item.id, item.quantity - 1)"
                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 disabled:opacity-50"
                                        :disabled="item.quantity <= 1">
                                    <span class="text-sm">-</span>
                                </button>
                                <span class="w-8 text-center text-sm font-medium" x-text="item.quantity"></span>
                                <button @click="updateQuantity(item.id, item.quantity + 1)"
                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                    <span class="text-sm">+</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-gray-900" x-text="(item.total_price).toFixed(2) + ' €'"></p>
                            <button @click="removeItem(item.id)" class="text-red-600 hover:text-red-800 text-xs mt-1">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Cart Footer -->
        <div x-show="cartItems.length > 0" class="border-t border-gray-200 p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold">Total:</span>
                <span class="text-lg font-bold text-gray-900" x-text="getTotal().toFixed(2) + ' €'"></span>
            </div>
            <div class="space-y-2">
                <a href="/orders/create" @click="closeCart()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center block text-sm font-medium">
                    Procéder au paiement
                </a>
                <button @click="clearCart()" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                    Vider le panier
                </button>
            </div>
        </div>
    </div>
</nav>

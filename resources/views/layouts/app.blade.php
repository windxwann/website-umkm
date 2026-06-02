<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('restaurant_name', config('app.name', 'Dapoer Cemal Cemil Jiemas')) }} - @yield('title')</title>
    
    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('styles')

    <style>
        /* Modern, Subtle Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(200, 200, 200, 0.5);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(150, 150, 150, 0.7);
        }
        
        /* Animations */
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
        
        /* Cart Count Styling */
        #cartCount, #cartCountMobile {
            transition: all 0.2s ease;
            pointer-events: none;
        }
        
        /* Animasi pop saat item bertambah */
        .cart-pop {
            transform: scale(1.3) !important;
        }
        
        /* Cart button */
        .cart-btn {
            cursor: pointer;
        }
        
        /* Modal styles */
        .cart-modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .cart-modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .cart-modal-content::-webkit-scrollbar {
            width: 4px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 overflow-hidden {{ setting('logo') ? 'bg-slate-50 border border-slate-100' : 'bg-orange-600 shadow-lg shadow-orange-600/20' }}">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="w-full h-full object-contain p-1">
                        @else
                            <i class="fas fa-utensils text-white text-sm"></i>
                        @endif
                    </div>
                    <span class="text-lg font-black text-slate-900 tracking-tight">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-orange-600 hover:bg-orange-50 transition {{ request()->routeIs('home') ? 'text-orange-600 bg-orange-50' : '' }}">Home</a>
                    <a href="{{ route('menu') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-orange-600 hover:bg-orange-50 transition {{ request()->routeIs('menu') ? 'text-orange-600 bg-orange-50' : '' }}">Menu</a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-orange-600 hover:bg-orange-50 transition {{ request()->routeIs('about') ? 'text-orange-600 bg-orange-50' : '' }}">Tentang</a>
                    @if(session('qr_code'))
                    <a href="{{ route('customer.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-orange-600 hover:bg-orange-50 transition {{ request()->routeIs('customer.dashboard') ? 'text-orange-600 bg-orange-50' : '' }}">Dashboard</a>
                    @endif

                    <button id="cartButtonDesktop" class="ml-4 w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cartCount" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black rounded-lg min-w-[16px] h-4 flex items-center justify-center">0</span>
                    </button>
                    </div>

                    <div class="md:hidden flex items-center gap-2">
                    <button id="cartButtonMobile" class="w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cartCountMobile" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black rounded-lg min-w-[16px] h-4 flex items-center justify-center">0</span>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl">
                        <i class="fas fa-bars"></i>
                    </button>
                    </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-6 space-y-2 bg-white border-t border-slate-50"
                 style="display: none;">
                <a href="{{ route('home') }}" class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Home</a>
                <a href="{{ route('menu') }}" class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Menu</a>
                <a href="{{ route('about') }}" class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Tentang</a>
                @if(session('qr_code'))
                <a href="{{ route('customer.dashboard') }}" class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Dashboard</a>
                @endif
            </div>
        </div>
    </nav>
    @hasSection('full_width_content')
        @yield('full_width_content')
    @else
        <main class="container mx-auto px-4 py-8 min-h-screen">
            @yield('content')
        </main>
    @endif

    <footer class="bg-gray-800 text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <!-- About -->
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</h3>
                    <p class="text-gray-300">{{ setting('address', 'Nikmati kelezatan seafood, masakan Sunda, dan aneka gorengan.') }}</p>
                </div>
                <!-- Hours -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Jam Operasional</h3>
                    <ul class="text-gray-300">
                        <li>Senin - Jumat: {{ setting('mon_fri_open', '10:00') }} - {{ setting('mon_fri_close', '22:00') }}</li>
                        <li>Sabtu - Minggu: {{ setting('sat_sun_open', '09:00') }} - {{ setting('sat_sun_close', '23:00') }}</li>
                    </ul>
                </div>
                <!-- Contact -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Kontak</h3>
                    <ul class="text-gray-300 text-sm">
                        <li class="mb-1"><i class="fas fa-phone mr-2"></i> {{ setting('phone', '0812-3456-7890') }}</li>
                        <li class="mb-1"><i class="fas fa-envelope mr-2"></i> {{ setting('email', 'info@dapoercemalcemil.com') }}</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Cemal Cemil Jiemas') }}.</p>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <div id="cartModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-white">
                <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-orange-600"></i>
                    Keranjang
                </h2>
                <button id="closeCartBtn" class="text-slate-400 hover:text-slate-900 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="cartItems" class="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                <div class="text-center text-slate-400 py-12">
                    <i class="fas fa-shopping-cart text-4xl mb-3 opacity-20"></i>
                    <p class="text-xs font-black uppercase tracking-widest">Keranjang kosong</p>
                </div>
            </div>

            <div id="cartFooter" class="p-6 border-t border-slate-50 bg-slate-50/50">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total</span>
                    <span id="cartTotal" class="text-xl font-black text-slate-900">Rp 0</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button id="clearCartBtn" class="py-3 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition font-black text-[10px] uppercase tracking-widest">
                        Kosongkan
                    </button>
                    <button id="checkoutBtn" class="py-3 rounded-xl bg-slate-900 text-white hover:bg-orange-600 transition font-black text-[10px] uppercase tracking-widest shadow-lg">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Cart toggle functions
        function openCart() {
            var modal = document.getElementById('cartModal');
            if (modal) {
                renderCart();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeCart() {
            var modal = document.getElementById('cartModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        // Initialize click handlers for cart
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            var cartButtonDesktop = document.getElementById('cartButtonDesktop');
            var cartButtonMobile = document.getElementById('cartButtonMobile');
            var closeCartBtn = document.getElementById('closeCartBtn');
            var clearCartBtn = document.getElementById('clearCartBtn');
            var checkoutBtn = document.getElementById('checkoutBtn');
            
            if (cartButtonDesktop) cartButtonDesktop.addEventListener('click', openCart);
            if (cartButtonMobile) cartButtonMobile.addEventListener('click', openCart);
            if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
            if (clearCartBtn) clearCartBtn.addEventListener('click', clearCart);
            if (checkoutBtn) checkoutBtn.addEventListener('click', checkout);
            
            // Close modal when clicking outside
            var modal = document.getElementById('cartModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) closeCart();
                });
            }
        });

        // ============================================
        // CART SYSTEM
        // ============================================
        
        var CART_STORAGE_KEY = 'restaurant_cart';
        window.cart = [];
        
        // Load cart from localStorage
        function loadCart() {
            try {
                var cartData = localStorage.getItem(CART_STORAGE_KEY);
                if (cartData) {
                    window.cart = JSON.parse(cartData);
                } else {
                    window.cart = [];
                }
            } catch(e) {
                console.error('Error loading cart:', e);
                window.cart = [];
            }
            updateCartCount();
        }
        
        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(window.cart));
            updateCartCount();
        }
        
        // Update cart count di navbar
        function updateCartCount() {
            var total = 0;
            for (var i = 0; i < window.cart.length; i++) {
                total += window.cart[i].quantity || 0;
            }
            
            var cartCountDesktop = document.getElementById('cartCount');
            var cartCountMobile = document.getElementById('cartCountMobile');
            
            if (cartCountDesktop) {
                cartCountDesktop.textContent = total || 0;
                cartCountDesktop.classList.add('cart-pop');
                setTimeout(function() { 
                    if (cartCountDesktop) cartCountDesktop.classList.remove('cart-pop'); 
                }, 200);
            }
            
            if (cartCountMobile) {
                cartCountMobile.textContent = total || 0;
                cartCountMobile.classList.add('cart-pop');
                setTimeout(function() { 
                    if (cartCountMobile) cartCountMobile.classList.remove('cart-pop'); 
                }, 200);
            }
        }
        
        // Format price
        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Render cart modal
        function renderCart() {
            var cartItems = document.getElementById('cartItems');
            var cartTotal = document.getElementById('cartTotal');
            
            if (!cartItems) return;
            
            if (window.cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center text-slate-400 py-12">
                        <i class="fas fa-shopping-cart text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs font-black uppercase tracking-widest">Keranjang kosong</p>
                    </div>
                `;
                if (cartTotal) cartTotal.textContent = 'Rp 0';
            } else {
                var html = '<div class="space-y-4">';
                var total = 0;
                
                for (var i = 0; i < window.cart.length; i++) {
                    var item = window.cart[i];
                    var subtotal = (item.price || 0) * (item.quantity || 0);
                    total += subtotal;
                    
                    html += `
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex-1">
                                <h3 class="text-sm font-black text-slate-900">${escapeHtml(item.name)}</h3>
                                <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Rp ${formatPrice(item.price)}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center bg-white rounded-xl border border-slate-100 p-1">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:text-orange-600">
                                        <i class="fas fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="w-8 text-center text-xs font-black text-slate-900">${item.quantity}</span>
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:text-orange-600">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <button onclick="removeItem(${item.id})" 
                                        class="text-rose-400 hover:text-rose-600 transition ml-2">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
                html += '</div>';
                
                cartItems.innerHTML = html;
                if (cartTotal) cartTotal.textContent = 'Rp ' + formatPrice(total);
            }
        }
        
        // Add item to cart
        window.addToCart = function(id, name, price, button) {
            loadCart();
            
            var existingIndex = -1;
            for (var i = 0; i < window.cart.length; i++) {
                if (window.cart[i].id == id) {
                    existingIndex = i;
                    break;
                }
            }
            
            if (existingIndex !== -1) {
                window.cart[existingIndex].quantity += 1;
            } else {
                window.cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }
            
            saveCart();
            showNotification(name + ' ditambahkan', 'success');
        }
        
        // Update quantity
        window.updateQuantity = function(id, quantity) {
            if (quantity < 1) {
                removeItem(id);
                return;
            }
            
            loadCart();
            for (var i = 0; i < window.cart.length; i++) {
                if (window.cart[i].id == id) {
                    window.cart[i].quantity = quantity;
                    break;
                }
            }
            saveCart();
            renderCart();
        }
        
        // Remove item
        window.removeItem = function(id) {
            loadCart();
            window.cart = window.cart.filter(function(item) { return item.id != id; });
            saveCart();
            renderCart();
        }
        
        // Clear cart
        function clearCart() {
            window.cart = [];
            saveCart();
            renderCart();
        }
        
        // Checkout
        function checkout() {
            if (window.cart.length === 0) {
                showNotification('Keranjang kosong!', 'warning');
                return;
            }
            
            fetch('{{ route("cart.save-to-session") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cart: window.cart })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    window.location.href = '{{ route("order.create") }}';
                } else {
                    showNotification('Gagal checkout', 'error');
                }
            });
        }
        
        // Show notification
        function showNotification(message, type) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: type,
                title: message
            });
        }

        @if(session('qr_code'))
        // Polling status kunci meja secara sangat ringan (setiap 5 detik)
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("qr.check-lock") }}', {
                    headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
                });
                const data = await response.json();
                if (data.locked) {
                    // Sesi diputus, langsung arahkan ke scan
                    window.location.replace('{{ route("scan.qr") }}');
                }
            } catch (e) {
                // Abaikan error jaringan ringan
            }
        }, 5000);
        @endif
    </script>
    @stack('scripts')
</body>
</html>

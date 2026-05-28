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
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
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
            width: 6px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold hover:text-orange-200 transition flex items-center max-w-[60%] truncate">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-8 md:h-10 w-auto mr-2 shrink-0">
                    @else
                        <i class="fas fa-utensils mr-2 shrink-0"></i>
                    @endif
                    <span class="truncate">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-orange-200 transition {{ request()->routeIs('home') ? 'text-orange-200' : '' }}">Home</a>
                    <a href="{{ route('menu') }}" class="hover:text-orange-200 transition {{ request()->routeIs('menu') ? 'text-orange-200 font-semibold' : '' }}">Menu</a>
                    <a href="{{ route('about') }}" class="hover:text-orange-200 transition {{ request()->routeIs('about') ? 'text-orange-200 font-semibold' : '' }}">Tentang Kami</a>
                    @if(session('qr_code'))
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-orange-200 transition">Dashboard</a>
                    @endif
                    
                    <!-- Cart Button Desktop -->
                    <div class="relative inline-block">
                        <button id="cartButtonDesktop" class="cart-btn p-2 hover:text-orange-200 transition focus:outline-none relative">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cartCount" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center border-2 border-orange-600 shadow-sm">
                                0
                            </span>
                        </button>
                    </div>
                </div>
                
                <div class="md:hidden flex items-center space-x-2">
                    <!-- Cart Button Mobile -->
                    <div class="relative inline-block mr-2">
                        <button id="cartButtonMobile" class="cart-btn p-2 focus:outline-none relative">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cartCountMobile" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center border-2 border-orange-600 shadow-sm">
                                0
                            </span>
                        </button>
                    </div>
                    <button id="mobileMenuBtn" class="p-2 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4 space-y-2 border-t border-orange-500 pt-2">
                <a href="{{ route('home') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Home</a>
                <a href="{{ route('menu') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Menu</a>
                <a href="{{ route('about') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Tentang Kami</a>
                @if(session('qr_code'))
                <a href="{{ route('customer.dashboard') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Dashboard</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 min-h-screen">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-600 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slideDown" 
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 px-4 py-3 rounded-lg mb-6 animate-slideDown"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</h3>
                    <p class="text-gray-300">{{ setting('address', 'Nikmati kelezatan seafood, masakan Sunda, dan aneka gorengan.') }}</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Jam Operasional</h3>
                    <ul class="text-gray-300">
                        <li>Senin - Jumat: {{ setting('mon_fri_open', '10:00') }} - {{ setting('mon_fri_close', '22:00') }}</li>
                        <li>Sabtu - Minggu: {{ setting('sat_sun_open', '09:00') }} - {{ setting('sat_sun_close', '23:00') }}</li>
                    </ul>
                </div>
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
    <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 cart-modal-overlay">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 cart-modal-content">
            <div class="p-4 md:p-6 border-b bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl md:text-2xl font-bold flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Keranjang Pesanan
                    </h2>
                    <button id="closeCartBtn" class="text-white hover:text-orange-200 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div id="cartItems" class="p-4 md:p-6 max-h-[60vh] overflow-y-auto">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                    <p>Keranjang kosong</p>
                </div>
            </div>
            <div id="cartFooter" class="p-4 md:p-6 border-t bg-gray-50 rounded-b-xl">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold text-gray-700">Total:</span>
                    <span id="cartTotal" class="text-2xl font-bold text-orange-600">Rp 0</span>
                </div>
                <div class="flex gap-3">
                    <button id="clearCartBtn" class="flex-1 bg-red-500 text-white py-2 md:py-3 rounded-lg hover:bg-red-600 transition font-semibold">
                        <i class="fas fa-trash-alt mr-2"></i>Kosongkan
                    </button>
                    <button id="checkoutBtn" class="flex-1 bg-orange-600 text-white py-2 md:py-3 rounded-lg hover:bg-orange-700 transition font-semibold shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i>Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="notificationToast" class="fixed bottom-4 right-4 left-4 md:left-auto bg-green-600 text-white px-4 md:px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-20 opacity-0 z-50">
        <div class="flex items-center justify-center md:justify-start">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="notificationMessage" class="text-sm md:text-base">Notifikasi</span>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        var mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                var mobileMenu = document.getElementById('mobileMenu');
                mobileMenu.classList.toggle('hidden');
            });
        }

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
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                        <p>Keranjang kosong</p>
                    </div>
                `;
                if (cartTotal) cartTotal.textContent = 'Rp 0';
            } else {
                var html = '<div class="space-y-3">';
                var total = 0;
                
                for (var i = 0; i < window.cart.length; i++) {
                    var item = window.cart[i];
                    var subtotal = (item.price || 0) * (item.quantity || 0);
                    total += subtotal;
                    
                    html += `
                        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1 mb-3 md:mb-0">
                                <h3 class="font-semibold text-gray-800">${escapeHtml(item.name)}</h3>
                                <p class="text-sm text-orange-600 font-semibold">Rp ${formatPrice(item.price)}</p>
                            </div>
                            <div class="flex items-center justify-between md:justify-end space-x-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" 
                                            class="w-8 h-8 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="w-12 text-center font-semibold">${item.quantity}</span>
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" 
                                            class="w-8 h-8 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="text-right min-w-[100px]">
                                    <p class="font-bold text-orange-600">Rp ${formatPrice(subtotal)}</p>
                                </div>
                                <button onclick="removeItem(${item.id})" 
                                        class="text-red-500 hover:text-red-700 transition">
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
        
        // Open cart modal
        function openCart() {
            var modal = document.getElementById('cartModal');
            if (modal) {
                renderCart();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        
        // Close cart modal
        function closeCart() {
            var modal = document.getElementById('cartModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
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
            showNotification(name + ' ditambahkan ke keranjang', 'success');
            
            if (button) {
                button.classList.add('scale-105');
                setTimeout(function() { button.classList.remove('scale-105'); }, 200);
            }
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
            showNotification('Item dihapus dari keranjang', 'info');
        }
        
        // Clear cart
        function clearCart() {
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: 'Semua item akan dihapus dari keranjang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kosongkan!',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.cart = [];
                    saveCart();
                    renderCart();
                    showNotification('Keranjang telah dikosongkan', 'info');
                }
            });
        }
        
        // Checkout
        function checkout() {
            if (window.cart.length === 0) {
                showNotification('Keranjang masih kosong!', 'warning');
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
                    showNotification('Mengalihkan ke halaman pemesanan...', 'success');
                    setTimeout(function() {
                        window.location.href = '{{ route("order.create") }}';
                    }, 1000);
                } else {
                    showNotification('Gagal: ' + data.message, 'error');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
            });
        }
        
        // Show notification
        function showNotification(message, type) {
            var toast = document.getElementById('notificationToast');
            var messageEl = document.getElementById('notificationMessage');
            
            if (!toast || !messageEl) return;
            
            toast.className = 'fixed bottom-4 right-4 left-4 md:left-auto px-4 md:px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 z-50 ' + (type === 'success' ? 'bg-green-600' : type === 'warning' ? 'bg-yellow-500' : 'bg-red-600') + ' text-white';
            messageEl.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(function() {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }
        
        // Global functions untuk halaman lain
        window.openCart = openCart;
        window.closeCart = closeCart;
        window.showNotification = showNotification;
        window.globalUpdateCartCount = updateCartCount;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            
            // Setup cart buttons - menggunakan ID
            var cartButtonDesktop = document.getElementById('cartButtonDesktop');
            var cartButtonMobile = document.getElementById('cartButtonMobile');
            var closeCartBtn = document.getElementById('closeCartBtn');
            var clearCartBtn = document.getElementById('clearCartBtn');
            var checkoutBtn = document.getElementById('checkoutBtn');
            
            if (cartButtonDesktop) {
                cartButtonDesktop.addEventListener('click', function(e) {
                    e.preventDefault();
                    openCart();
                });
            }
            
            if (cartButtonMobile) {
                cartButtonMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    openCart();
                });
            }
            
            if (closeCartBtn) {
                closeCartBtn.addEventListener('click', function() {
                    closeCart();
                });
            }
            
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', function() {
                    clearCart();
                });
            }
            
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function() {
                    checkout();
                });
            }
            
            // Close modal when clicking outside
            var modal = document.getElementById('cartModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeCart();
                    }
                });
            }
        });
        
        // Listen for storage events (sync across tabs)
        window.addEventListener('storage', function(e) {
            if (e.key === CART_STORAGE_KEY) {
                loadCart();
                if (document.getElementById('cartModal') && !document.getElementById('cartModal').classList.contains('hidden')) {
                    renderCart();
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
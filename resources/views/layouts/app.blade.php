<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('restaurant_name', config('app.name', 'Dapoer Cemal Cemil Jiemas')) }} - @yield('title')</title>
    
    <!-- Favicon -->
    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-orange-600 text-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold hover:text-orange-200 transition flex items-center max-w-[70%] sm:max-w-none truncate">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-8 md:h-10 w-auto mr-2 shrink-0">
                    @else
                        <i class="fas fa-utensils mr-2 shrink-0"></i>
                    @endif
                    <span class="truncate">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-orange-200 transition {{ request()->routeIs('home') ? 'text-orange-200' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('menu') }}" class="hover:text-orange-200 transition {{ request()->routeIs('menu') ? 'text-orange-200' : '' }}">
                        Menu
                    </a>
                    @if(session('qr_code'))
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-orange-200 transition">
                        Dashboard
                    </a>
                    @endif
                    
                    <!-- Cart Icon with Count -->
                    <div class="relative">
                        <button onclick="window.openCart ? window.openCart() : null" class="relative p-2 hover:text-orange-200 transition focus:outline-none">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button onclick="window.openCart ? window.openCart() : null" class="relative p-2">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cartCountMobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>
                    <button id="mobileMenuBtn" class="p-2 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu Dropdown -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Home</a>
                <a href="{{ route('menu') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Menu</a>
                @if(session('qr_code'))
                <a href="{{ route('customer.dashboard') }}" class="block py-2 px-4 hover:bg-orange-700 rounded transition">Dashboard</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 min-h-screen">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-600 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slideDown" 
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">
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
                    <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</h3>
                    <p class="text-gray-300">{{ setting('address', 'Nikmati kelezatan seafood, masakan Sunda, dan aneka gorengan dengan cita rasa autentik.') }}</p>
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
                    <ul class="text-gray-300">
                        <li><i class="fas fa-phone mr-2"></i> {{ setting('phone', '0812-3456-7890') }}</li>
                        <li><i class="fas fa-envelope mr-2"></i> {{ setting('email', 'info@dapoercemalcemil.com') }}</li>
                        <li><i class="fas fa-globe mr-2"></i> {{ setting('website', 'www.dapoercemalcemil.com') }}</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Cemal Cemil Jiemas') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Cart Modal (will be loaded from menu page) -->
    <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300">
        <!-- Cart modal content will be injected by menu page -->
    </div>

    <!-- Notification Toast -->
    <div id="notificationToast" class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-20 opacity-0 z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="notificationMessage">Notifikasi</span>
        </div>
    </div>

    <style>
        /* Animations */
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
        
        /* Cart count animation */
        #cartCount, #cartCountMobile {
            transition: all 0.2s ease;
        }
        
        #cartCount.scale-125, #cartCountMobile.scale-125 {
            transform: scale(1.25);
        }
    </style>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });

        // Global cart functions - hanya sebagai fallback, akan di-override oleh halaman menu
        window.globalUpdateCartCount = function(count) {
            const cartCount = document.getElementById('cartCount');
            const cartCountMobile = document.getElementById('cartCountMobile');
            
            if (cartCount) {
                cartCount.textContent = count || 0;
                cartCount.classList.add('scale-125');
                setTimeout(() => cartCount.classList.remove('scale-125'), 200);
            }
            
            if (cartCountMobile) {
                cartCountMobile.textContent = count || 0;
                cartCountMobile.classList.add('scale-125');
                setTimeout(() => cartCountMobile.classList.remove('scale-125'), 200);
            }
        };

        // Show notification toast (global)
        window.showNotification = function(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const messageEl = document.getElementById('notificationMessage');
            
            // Set color based on type
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            } text-white`;
            
            messageEl.textContent = message;
            
            // Show toast
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        };

        // Initialize cart count from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const savedCart = localStorage.getItem('cart');
                if (savedCart) {
                    const cart = JSON.parse(savedCart);
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    window.globalUpdateCartCount(totalItems);
                }
            } catch (e) {
                console.error('Error loading cart:', e);
            }
        });

        // Listen for cart updates from other pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'cart') {
                try {
                    const cart = JSON.parse(e.newValue || '[]');
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    window.globalUpdateCartCount(totalItems);
                } catch (error) {
                    console.error('Error parsing cart from storage event:', error);
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
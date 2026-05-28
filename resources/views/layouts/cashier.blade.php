<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Kasir {{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</title>
    
    <!-- Favicon -->
    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navbar dengan Hamburger Menu -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-10 w-auto">
                    @else
                        <i class="fas fa-cash-register text-2xl"></i>
                    @endif
                    <span class="font-bold text-xl hidden sm:inline">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</span>
                    <span class="font-bold text-xl sm:hidden">Kasir</span>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="hover:text-orange-200 transition {{ request()->routeIs('cashier.dashboard') ? 'text-orange-200 border-b-2 border-orange-200' : '' }}">
                        <i class="fas fa-dashboard mr-1"></i>Dashboard
                    </a>
                    <a href="{{ route('cashier.transactions.today') }}" 
                       class="hover:text-orange-200 transition {{ request()->routeIs('cashier.transactions*') ? 'text-orange-200 border-b-2 border-orange-200' : '' }}">
                        <i class="fas fa-money-bill-wave mr-1"></i>Transaksi
                    </a>
                </div>
                
                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <!-- User Info Desktop -->
                    <span class="text-sm hidden md:block">
                        <i class="fas fa-user mr-1"></i>{{ auth()->user()->name }}
                    </span>
                    
                    <!-- Logout Button Desktop -->
                    <form method="POST" action="{{ route('admin.logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="bg-orange-700 hover:bg-orange-800 px-4 py-2 rounded-lg transition text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                    
                    <!-- Hamburger Menu Button (Mobile) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="md:hidden p-2 rounded-lg hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu Dropdown -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 @click.away="mobileMenuOpen = false"
                 class="md:hidden absolute top-16 left-0 right-0 bg-orange-600 shadow-xl z-50">
                
                <div class="flex flex-col py-2">
                    <!-- User Info Mobile -->
                    <div class="px-4 py-3 border-b border-orange-500">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-orange-700 flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-orange-200">{{ auth()->user()->email ?? 'Cashier' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Links Mobile -->
                    <a href="{{ route('cashier.dashboard') }}" 
                       @click="mobileMenuOpen = false"
                       class="px-4 py-3 hover:bg-orange-700 transition flex items-center space-x-3 {{ request()->routeIs('cashier.dashboard') ? 'bg-orange-700' : '' }}">
                        <i class="fas fa-dashboard w-5"></i>
                        <span>Dashboard</span>
                        @if(request()->routeIs('cashier.dashboard'))
                            <i class="fas fa-check ml-auto"></i>
                        @endif
                    </a>
                    
                    <a href="{{ route('cashier.transactions.today') }}" 
                       @click="mobileMenuOpen = false"
                       class="px-4 py-3 hover:bg-orange-700 transition flex items-center space-x-3 {{ request()->routeIs('cashier.transactions*') ? 'bg-orange-700' : '' }}">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span>Transaksi</span>
                        @if(request()->routeIs('cashier.transactions*'))
                            <i class="fas fa-check ml-auto"></i>
                        @endif
                    </a>
                    
                    <!-- Divider -->
                    <div class="border-t border-orange-500 my-2"></div>
                    
                    <!-- Logout Button Mobile -->
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" 
                                @click="mobileMenuOpen = false"
                                class="w-full text-left px-4 py-3 hover:bg-orange-700 transition flex items-center space-x-3 text-red-100">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                            <i class="fas fa-arrow-right ml-auto"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay for mobile menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"
         style="display: none;">
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-600 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8 py-4">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Cemal Cemil Jiemas') }} - Sistem Kasir</p>
        </div>
    </footer>

    <!-- Notification Toast -->
    <div id="notificationToast" class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-20 opacity-0 z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="notificationMessage">Notifikasi</span>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Better touch targets for mobile */
        @media (max-width: 768px) {
            button, a {
                min-height: 44px;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }
    </style>

    <script>
        function showNotification(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const messageEl = document.getElementById('notificationMessage');
            
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            } text-white`;
            
            messageEl.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }
        
        // Close mobile menu on window resize if screen becomes desktop
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 768) {
                    if (window.Alpine && window.Alpine.store) {
                        // If Alpine is available, close the menu
                        const navComponent = document.querySelector('[x-data]');
                        if (navComponent && navComponent.__x) {
                            navComponent.__x.$data.mobileMenuOpen = false;
                        }
                    }
                }
            }, 250);
        });
        
        // Prevent body scroll when mobile menu is open
        function toggleBodyScroll(disable) {
            if (disable) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        // Monitor mobile menu state
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'style') {
                    const menu = document.querySelector('[x-show]');
                    if (menu && menu.style.display !== 'none') {
                        toggleBodyScroll(true);
                    } else {
                        toggleBodyScroll(false);
                    }
                }
            });
        });
        
        const targetNode = document.querySelector('[x-show]');
        if (targetNode) {
            observer.observe(targetNode, { attributes: true });
        }
    </script>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
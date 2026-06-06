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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Modern Navbar -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-slate-100 sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('cashier.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="w-full h-full object-contain p-1">
                        @else
                            <i class="fas fa-cash-register text-orange-600 text-sm"></i>
                        @endif
                    </div>
                    <div>
                        <span class="text-lg font-black text-slate-900 tracking-tight leading-none block">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kasir Panel</span>
                    </div>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('cashier.dashboard') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-orange-600 hover:bg-orange-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('cashier.transactions.today') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('cashier.transactions*') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-orange-600 hover:bg-orange-50' }}">
                        Transaksi
                    </a>
                </div>
                
                <!-- Right Section -->
                <div class="flex items-center gap-4">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Aktif</p>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="w-11 h-11 flex items-center justify-center bg-slate-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                    
                    <!-- Hamburger Menu Button (Mobile) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="md:hidden w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl transition">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu Dropdown -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-6 space-y-2 border-t border-slate-50"
                 style="display: none;">
                
                <a href="{{ route('cashier.dashboard') }}" 
                   class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Dashboard</a>
                
                <a href="{{ route('cashier.transactions.today') }}" 
                   class="block py-3 px-4 rounded-xl font-black text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition">Transaksi</a>
                
                <form method="POST" action="{{ route('admin.logout') }}" class="pt-2 border-t border-slate-50">
                    @csrf
                    <button type="submit" class="w-full text-left py-3 px-4 rounded-xl font-black text-rose-500 hover:bg-rose-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Original Dark Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-8">
        <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
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
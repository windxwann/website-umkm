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
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-10 w-auto">
                    @else
                        <i class="fas fa-cash-register text-2xl"></i>
                    @endif
                    <span class="font-bold text-xl">{{ setting('restaurant_name', 'Dapoer Cemal Cemil') }} - Kasir</span>
                </div>
                
                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="hover:text-orange-200 transition {{ request()->routeIs('cashier.dashboard') ? 'text-orange-200' : '' }}">
                        <i class="fas fa-dashboard mr-1"></i>Dashboard
                    </a>
                    <a href="{{ route('cashier.orders') }}" 
                       class="hover:text-orange-200 transition {{ request()->routeIs('cashier.orders*') ? 'text-orange-200' : '' }}">
                        <i class="fas fa-shopping-cart mr-1"></i>Pesanan
                    </a>
                    <a href="{{ route('cashier.transactions.today') }}" 
                       class="hover:text-orange-200 transition {{ request()->routeIs('cashier.transactions*') ? 'text-orange-200' : '' }}">
                        <i class="fas fa-money-bill-wave mr-1"></i>Transaksi
                    </a>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm hidden md:block">
                        <i class="fas fa-user mr-1"></i>{{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="bg-orange-700 hover:bg-orange-800 px-4 py-2 rounded-lg transition text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-600 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <span>{{ session('error') }}</span>
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
    </script>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
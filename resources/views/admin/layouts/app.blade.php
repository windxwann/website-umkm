<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin {{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}</title>
    
    <!-- Favicon -->
    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Pusher for real-time notifications -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    
    <style>
        /* Sidebar transitions */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, rgba(249,115,22,0.2) 0%, rgba(249,115,22,0) 100%);
            transition: width 0.3s ease;
        }
        
        .sidebar-link:hover::before {
            width: 100%;
        }
        
        .sidebar-link.active {
            background: linear-gradient(90deg, #f97316 0%, #fb923c 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(249,115,22,0.3);
        }
        
        .sidebar-link.active i {
            color: white;
        }
        
        .sidebar-link.active::before {
            display: none;
        }
        
        /* Scrollbar styling */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #f97316;
        }
        
        /* Mobile overlay */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            transition: opacity 0.3s ease;
        }
        
        /* Card hover effect */
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }
        
        /* Notification styles */
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .toast-notification {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024, 
    mobileMenuOpen: false,
    init() {
        this.$watch('sidebarOpen', value => {
            if (window.innerWidth < 1024) {
                this.mobileMenuOpen = value;
            }
        });
        
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = true;
                this.mobileMenuOpen = false;
            } else {
                this.sidebarOpen = false;
            }
        });
    }
}">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <!-- Mobile Menu Overlay -->
        <template x-if="mobileMenuOpen">
            <div class="fixed inset-0 z-40 sidebar-overlay lg:hidden" 
                 @click="mobileMenuOpen = false; sidebarOpen = false">
            </div>
        </template>

        <!-- Sidebar Toggle Button - ONLY VISIBLE ON MOBILE -->
        <button 
            @click="sidebarOpen = !sidebarOpen; mobileMenuOpen = !mobileMenuOpen"
            class="fixed top-4 left-4 z-50 lg:hidden
                w-10 h-10 sm:w-14 sm:h-14
                flex items-center justify-center
                bg-gradient-to-r from-orange-600 to-orange-500
                text-white rounded-xl shadow-lg hover:shadow-xl
                transition-all duration-300 hover:scale-110">
            <i class="fas fa-bars text-lg sm:text-xl"></i>
        </button>

        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 z-50 h-full w-72 sidebar-transition"
               :class="{
                   'w-72': sidebarOpen,
                   '-translate-x-full': !sidebarOpen && window.innerWidth < 1024,
                   'translate-x-0': sidebarOpen || window.innerWidth >= 1024
               }">
            <div class="w-72 h-full bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white overflow-y-auto sidebar-scrollbar shadow-2xl">
                <!-- Logo & Brand -->
                <div class="p-6 border-b border-gray-700/50 bg-gray-900/50 backdrop-blur-sm sticky top-0 z-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg shadow-orange-500/20 flex items-center justify-center overflow-hidden">
                            @if(setting('logo'))
                                <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-utensils text-2xl text-white"></i>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-lg font-bold bg-gradient-to-r from-orange-400 to-orange-300 bg-clip-text text-transparent truncate w-40">
                                {{ setting('restaurant_name', 'Dapoer Cemal') }}
                            </h1>
                            <p class="text-xs text-gray-400 flex items-center">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                Admin Panel
                            </p>
                        </div>
                    </div>
                    
                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false; mobileMenuOpen = false" 
                            class="absolute top-4 right-4 lg:hidden text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- User Info -->
                <div class="p-4 border-b border-gray-700/50">
                    <div class="flex items-center space-x-3 group cursor-pointer">
                        @if(auth()->user()->photo)
                            @php
                                $userAvatarUrl = '';
                                if (Storage::disk('public')->exists(auth()->user()->photo)) {
                                    $userAvatarUrl = asset('storage/' . auth()->user()->photo);
                                }
                            @endphp
                            <img src="{{ $userAvatarUrl }}" 
                                 alt="{{ auth()->user()->name }}"
                                 class="w-12 h-12 rounded-xl object-cover border-2 border-orange-500/50 group-hover:border-orange-500 transition-all duration-300"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random';">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                                    {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Kasir' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <nav class="p-4 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                        class="sidebar-link flex items-center space-x-4 px-4 py-3 rounded-xl
                        {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        <i class="fas fa-chart-pie w-6 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    
                    <!-- Products Menu -->
                    <div x-data="{ open: {{ request()->routeIs('admin.products*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.products*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-box w-6 {{ request()->routeIs('admin.products*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">Produk</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-11 space-y-1">
                            <a href="{{ route('admin.products.index') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.products.index') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-list mr-2 w-4"></i>Semua Produk
                            </a>
                            <a href="{{ route('admin.products.create') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.products.create') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-plus mr-2 w-4"></i>Tambah Produk
                            </a>
                        </div>
                    </div>
                    
                    <!-- Categories Menu -->
                    <div x-data="{ open: {{ request()->routeIs('admin.categories*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.categories*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-tags w-6 {{ request()->routeIs('admin.categories*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">Kategori</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-11 space-y-1">
                            <a href="{{ route('admin.categories.index') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.categories.index') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-list mr-2 w-4"></i>Semua Kategori
                            </a>
                            <a href="{{ route('admin.categories.create') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.categories.create') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-plus mr-2 w-4"></i>Tambah Kategori
                            </a>
                        </div>
                    </div>
                    
                    <!-- Orders Menu -->
                    <div x-data="{ open: {{ request()->routeIs('admin.orders*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.orders*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-shopping-cart w-6 {{ request()->routeIs('admin.orders*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">Pesanan</span>
                                @php
                                    $pendingOrders = \App\Models\Order::where('order_status', 'waiting')->count();
                                @endphp
                                @if($pendingOrders > 0)
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $pendingOrders }}</span>
                                @endif
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-11 space-y-1">
                            <a href="{{ route('admin.orders.index') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders.index') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-list mr-2 w-4"></i>Semua Pesanan
                            </a>
                            <a href="{{ route('admin.orders.index', ['status' => 'waiting']) }}" 
                               class="flex items-center justify-between px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders.index') && request('status') == 'waiting' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <span><i class="fas fa-clock mr-2 w-4"></i>Menunggu</span>
                                @if($pendingOrders > 0)
                                    <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.orders.index', ['status' => 'processed']) }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders.index') && request('status') == 'processed' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-spinner mr-2 w-4"></i>Diproses
                            </a>
                            <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders.index') && request('status') == 'completed' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-check-circle mr-2 w-4"></i>Selesai
                            </a>
                        </div>
                    </div>
                    
                    <!-- QR Codes Menu -->
                    <div x-data="{ open: {{ request()->routeIs('admin.qrcodes.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.qrcodes.*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-qrcode w-6 {{ request()->routeIs('admin.qrcodes.*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">QR Codes</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-11 space-y-1">
                            <a href="{{ route('admin.qrcodes.index') }}" 
                            class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.qrcodes.index') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-list mr-2 w-4"></i>Semua QR
                            </a>
                            <a href="{{ route('admin.qrcodes.create') }}" 
                            class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.qrcodes.create') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-plus mr-2 w-4"></i>Buat QR
                            </a>
                        </div>
                    </div>
                                        
                    <!-- Users Menu (Admin Only) -->
                    @if(auth()->user()->role === 'admin')
                    <div x-data="{ open: {{ request()->routeIs('admin.users*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.users*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-users w-6 {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">Users</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-11 space-y-1">
                            <a href="{{ route('admin.users.index') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.users.index') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-list mr-2 w-4"></i>Semua User
                            </a>
                            <a href="{{ route('admin.users.create') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.users.create') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-user-plus mr-2 w-4"></i>Tambah User
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Reports Menu (Admin Only) -->
                    @if(auth()->user()->role === 'admin')
                    <div x-data="{ open: {{ request()->routeIs('admin.reports*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.reports*') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-chart-bar w-6 {{ request()->routeIs('admin.reports*') ? 'text-white' : 'text-gray-400' }}"></i>
                                <span class="font-medium">Laporan</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-11 space-y-1">
                            <a href="{{ route('admin.reports.sales') }}" 
                               class="block px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.reports.sales') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }} transition-all duration-200">
                                <i class="fas fa-chart-line mr-2 w-4"></i>Penjualan
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Settings (Admin Only) -->
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.settings') }}" 
                       class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.settings') ? 'active' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        <i class="fas fa-cog w-6 {{ request()->routeIs('admin.settings') ? 'text-white' : 'text-gray-400' }}"></i>
                        <span class="font-medium">Pengaturan</span>
                    </a>
                    @endif
                    
                    <!-- Logout -->
                    <div class="pt-4 mt-4 border-t border-gray-700/50">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl text-red-400 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-500 hover:text-white transition-all duration-300 group">
                                <i class="fas fa-sign-out-alt w-6 group-hover:rotate-180 transition-transform duration-500"></i>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>

                <!-- Footer -->
                <div class="p-4 mt-4 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Cemal Cemil') }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">
                        v2.0.0
                    </p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto ml-0 lg:ml-72 sidebar-transition">
            <!-- Top Navigation -->
            <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-30 border-b border-gray-200/50">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 pl-12 lg:pl-0">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0, 
                             fetchNotifications() {
                                 fetch('/api/v1/cashier-notifications')
                                     .then(res => res.json())
                                     .then(data => { 
                                         this.notifications = data; 
                                         this.unreadCount = data.filter(n => !n.is_read).length; 
                                     });
                             }
                         }" 
                             x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)">
                            <button @click="open = !open" 
                                    class="relative p-2 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount"
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge">
                                </span>
                            </button>
                            
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                                <div class="p-3 border-b bg-gradient-to-r from-orange-50 to-transparent font-semibold text-gray-700 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-bell text-orange-600 mr-2"></i>
                                        Notifikasi
                                    </div>
                                    <button x-show="unreadCount > 0" @click="markAllAsRead(); unreadCount = 0" class="text-xs text-orange-600 hover:text-orange-700">
                                        Tandai semua dibaca
                                    </button>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <div class="p-3 hover:bg-gray-50 border-b transition-colors cursor-pointer"
                                             :class="{'bg-orange-50': !notif.is_read}"
                                             @click="markAsRead(notif.id); window.location.href = `/admin/orders/${notif.order_id}`">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                         :class="notif.message.includes('Pesanan baru') ? 'bg-blue-100' : 'bg-green-100'">
                                                        <i class="fas" :class="notif.message.includes('Pesanan baru') ? 'fa-shopping-cart text-blue-600' : 'fa-credit-card text-green-600'"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-800" x-text="notif.message.includes('Pesanan baru') ? 'Pesanan Baru' : 'Konfirmasi Pembayaran'"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="notif.message"></p>
                                                    <p class="text-xs text-gray-400 mt-1" x-text="timeAgo(notif.created_at)"></p>
                                                </div>
                                                <div x-show="!notif.is_read" class="w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="notifications.length === 0" class="p-8 text-center text-gray-500">
                                        <i class="fas fa-bell-slash text-4xl mb-2"></i>
                                        <p class="text-sm">Belum ada notifikasi</p>
                                    </div>
                                </div>
                                <div class="p-2 border-t text-center bg-gray-50/50 rounded-b-xl">
                                    <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 block py-1 transition-colors">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="hidden sm:flex items-center text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-calendar-alt text-orange-600 mr-2"></i>
                            <span class="text-sm">{{ now()->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-600 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slideDown shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-green-600 rounded-full p-1 mr-3">
                                <i class="fas fa-check-circle text-white text-sm"></i>
                            </div>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-600 text-red-700 px-4 py-3 rounded-lg mb-6 animate-slideDown shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-red-600 rounded-full p-1 mr-3">
                                <i class="fas fa-exclamation-circle text-white text-sm"></i>
                            </div>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <style>
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
        
        /* Custom scrollbar for main content */
        main::-webkit-scrollbar {
            width: 8px;
        }
        
        main::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        main::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 4px;
        }
        
        main::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }
    </style>

    @push('scripts')
    <script>
    // Real-time notifications with Pusher
    function initPusher() {
        const pusherKey = '{{ env("PUSHER_APP_KEY") }}';
        if (!pusherKey || pusherKey === 'your-app-key') {
            console.log('Pusher not configured');
            return;
        }
        
        const pusher = new Pusher(pusherKey, {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });
        
        const channel = pusher.subscribe('admin-notifications');
        
        channel.bind('new-order', function(data) {
            console.log('New order received:', data);
            showToast(data.message, 'order');
            // Reload page to update order count if on orders or dashboard
            if (window.location.pathname.includes('/admin/orders') || window.location.pathname.includes('/admin/dashboard')) {
                setTimeout(() => location.reload(), 3000);
            }
        });

        channel.bind('payment-notification', function(data) {
            console.log('Payment notification received:', data);
            showToast(data.message, 'payment');
            // Reload page if needed
            if (window.location.pathname.includes('/admin/orders') || window.location.pathname.includes('/admin/dashboard')) {
                setTimeout(() => location.reload(), 3000);
            }
        });
    }
    
    function showToast(message, type) {
        let toast = document.getElementById('toastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toastNotification';
            toast.className = 'fixed bottom-4 right-4 bg-white rounded-xl shadow-2xl transform transition-all duration-300 translate-x-full opacity-0 z-50 max-w-sm';
            document.body.appendChild(toast);
        }
        
        const bgColor = type === 'order' ? 'bg-blue-500' : 'bg-green-500';
        const icon = type === 'order' ? 'fa-shopping-cart' : 'fa-credit-card';
        
        toast.innerHTML = `
            <div class="flex items-center p-4 gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 ${bgColor} rounded-full flex items-center justify-center">
                        <i class="fas ${icon} text-white"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-sm">Notifikasi Baru</p>
                    <p class="text-xs text-gray-600">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.classList.add('translate-x-full', 'opacity-0')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100', 'toast-notification');
        
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            toast.classList.remove('translate-x-0', 'opacity-100');
        }, 5000);
    }
    
    function timeAgo(date) {
        const seconds = Math.floor((new Date() - new Date(date)) / 1000);
        if (seconds < 60) return 'baru saja';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} menit lalu`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} jam lalu`;
        return `${Math.floor(hours / 24)} hari lalu`;
    }
    
    function markAsRead(id) {
        fetch(`/api/v1/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    }
    
    function markAllAsRead() {
        fetch('/api/v1/notifications/mark-all-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    }
    
    // Initialize Pusher when DOM is ready
    document.addEventListener('DOMContentLoaded', initPusher);
    </script>
    @endpush

    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    
    <!-- Tailwind CSS (V4) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js & Pusher -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Floating Sidebar */
        .sidebar-container { padding: 1rem; height: 100vh; }
        .sidebar-content {
            height: 100%; border-radius: 1.5rem; background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex; flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .sidebar-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Glassmorphism Header */
        .glass-header {
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .main-scrollbar::-webkit-scrollbar { width: 6px; }
        .main-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.05); border-radius: 10px; }
        
        .animate-slideDown { animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="antialiased text-slate-900 overflow-hidden" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024,
    mobileMenuOpen: false
}">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 sidebar-transition lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="sidebar-container">
                <div class="sidebar-content">
                    <div class="p-6 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 overflow-hidden {{ setting('logo') ? 'bg-white' : 'bg-orange-600 shadow-lg shadow-orange-600/20' }}">
                                @if(setting('logo'))
                                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="w-full h-full object-contain p-1">
                                @else
                                    <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <h1 class="text-white font-extrabold text-lg leading-tight truncate">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h1>
                                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">Admin Panel</p>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-8 custom-scrollbar">
                        <div>
                            <p class="px-4 text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] mb-4">Utama</p>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> <span class="font-medium text-sm">Dashboard</span>
                            </a>
                        </div>
                        <div>
                            <p class="px-4 text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] mb-4">Manajemen</p>
                            <div class="space-y-1">
                                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.orders.*') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                    <div class="flex items-center gap-3"><i data-lucide="shopping-cart" class="w-5 h-5"></i> <span class="font-medium text-sm">Pesanan</span></div>
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                                    <i data-lucide="package" class="w-5 h-5"></i> <span class="font-medium text-sm">Produk</span>
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                                    <i data-lucide="tags" class="w-5 h-5"></i> <span class="font-medium text-sm">Kategori</span>
                                </a>
                                <a href="{{ route('admin.qrcodes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                                    <i data-lucide="qr-code" class="w-5 h-5"></i> <span class="font-medium text-sm">QR Codes</span>
                                </a>
                            </div>
                        </div>
                        <div>
                            <p class="px-4 text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] mb-4">Sistem</p>
                            <a href="{{ route('admin.reports.sales') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i> <span class="font-medium text-sm">Laporan</span>
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                                <i data-lucide="settings" class="w-5 h-5"></i> <span class="font-medium text-sm">Pengaturan</span>
                            </a>
                        </div>
                    </nav>

                    <div class="p-4 border-t border-slate-800/50">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-400 hover:text-white hover:bg-rose-500/10 transition-all">
                                <i data-lucide="log-out" class="w-5 h-5"></i> <span class="font-medium text-sm">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative overflow-hidden">
            <header class="glass-header shrink-0 z-30">
                <div class="h-20 px-8 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 lg:hidden shadow-sm">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right"><p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p></div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f97316&color=fff" class="w-10 h-10 rounded-xl">
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 main-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });</script>
    @stack('scripts')
</body>
</html>
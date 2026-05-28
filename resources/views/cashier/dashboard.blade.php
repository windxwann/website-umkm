@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Dashboard Kasir</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-user mr-2"></i>{{ auth()->user()->name }} 
                    <i class="fas fa-calendar ml-2 md:ml-4 mr-2"></i>{{ now()->format('d F Y') }}
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-cash-register text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards - 5 Kolom -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8">
        <!-- Pending Payment -->
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Payment</p>
                    <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['pending_payments'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <!-- Pesanan Baru -->
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pesanan Baru</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['waiting_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <!-- Diproses -->
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Diproses</p>
                    <p class="text-xl md:text-2xl font-bold text-purple-600">{{ $stats['processed_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-spinner text-purple-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <!-- Selesai Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Selesai Hari Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-green-600">{{ $stats['completed_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <!-- Pendapatan Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-base md:text-xl font-bold text-orange-600">
                        Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-money-bill-wave text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MANAJEMEN MEJA - RESPONSIVE -->
    <!-- ============================================ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8">
        <div class="p-4 md:p-6 border-b bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chair text-orange-600 mr-2"></i>
                    Manajemen Meja
                </h2>
                <span class="text-xs md:text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Klik "Reset Meja" setelah pelanggan selesai makan
                </span>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            @if(isset($tables) && $tables->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                @foreach($tables as $table)
                <div class="relative rounded-xl border-2 transition-all duration-300 hover:shadow-lg
                    {{ $table->active_orders_count > 0 
                        ? 'border-orange-300 bg-orange-50' 
                        : ($table->is_locked ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50') }}"
                    id="table-card-{{ $table->qr_code }}">
                    
                    <!-- Status Indicator -->
                    <div class="absolute top-3 right-3">
                        @if($table->active_orders_count > 0)
                            <span class="relative flex h-2.5 w-2.5 md:h-3 md:w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 md:h-3 md:w-3 bg-orange-500"></span>
                            </span>
                        @elseif($table->is_locked)
                            <span class="relative flex h-2.5 w-2.5 md:h-3 md:w-3">
                                <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 md:h-3 md:w-3 bg-blue-500"></span>
                            </span>
                        @else
                            <span class="inline-flex rounded-full h-2.5 w-2.5 md:h-3 md:w-3 bg-green-400"></span>
                        @endif
                    </div>

                    <div class="p-3 md:p-4">
                        <!-- Table Name -->
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg flex items-center justify-center mr-2 md:mr-3
                                {{ $table->active_orders_count > 0 ? 'bg-orange-200' : 'bg-gray-200' }}">
                                <i class="fas fa-utensils text-xs md:text-base {{ $table->active_orders_count > 0 ? 'text-orange-600' : 'text-gray-500' }}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm md:text-base">{{ $table->meja }}</h3>
                                @if($table->nama_tempat)
                                    <p class="text-xs text-gray-500">{{ $table->nama_tempat }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Order Info -->
                        @if($table->active_orders_count > 0)
                            <div class="space-y-2 mb-3 md:mb-4">
                                <div class="flex justify-between items-center text-xs md:text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-shopping-bag mr-1"></i>Pesanan Aktif
                                    </span>
                                    <span class="font-bold text-orange-600">{{ $table->active_orders_count }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs md:text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-money-bill-wave mr-1"></i>Total
                                    </span>
                                    <span class="font-bold text-orange-600 text-xs md:text-sm">
                                        Rp {{ number_format($table->total_active_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($table->has_unpaid)
                                    <div class="flex items-center text-xs text-yellow-700 bg-yellow-100 rounded-lg px-2 py-1">
                                        <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>
                                        <span class="text-xs">Ada pesanan belum dibayar</span>
                                    </div>
                                @endif

                                <!-- Detail Pesanan -->
                                <div class="border-t border-orange-200 pt-2 mt-2">
                                    @foreach($table->active_orders as $activeOrder)
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs py-2 gap-1">
                                            <span class="font-mono text-gray-600">{{ $activeOrder->order_number }}</span>
                                            <div class="flex flex-wrap items-center gap-1">
                                                @if($activeOrder->order_status === 'waiting')
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-[10px]">Menunggu</span>
                                                @elseif($activeOrder->order_status === 'processed')
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px]">Diproses</span>
                                                @endif
                                                @if($activeOrder->payment_status === 'paid')
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px]">Lunas</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-[10px]">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Reset Button -->
                            <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', {{ $table->active_orders_count }}, {{ $table->has_unpaid ? 'true' : 'false' }})"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-3 md:py-2.5 rounded-lg transition-all duration-200 font-semibold text-xs md:text-sm flex items-center justify-center shadow-md hover:shadow-lg"
                                    id="reset-btn-{{ $table->qr_code }}">
                                <i class="fas fa-sync-alt mr-2 text-xs md:text-sm"></i>
                                <span class="hidden sm:inline">Reset Meja</span>
                                <span class="sm:hidden">Reset</span>
                            </button>
                        @else
                            <div class="text-center py-3 md:py-4">
                                @if($table->is_locked)
                                    <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-user-check text-blue-500 text-base md:text-lg"></i>
                                    </div>
                                    <p class="text-xs md:text-sm text-blue-600 font-medium">Terisi</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-3">Memilih menu...</p>
                                    
                                    <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', 0, false)"
                                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-1.5 px-3 rounded-lg transition text-xs font-semibold flex items-center justify-center">
                                        <i class="fas fa-sync-alt mr-1 text-xs"></i>
                                        Reset
                                    </button>
                                @else
                                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-check text-green-500 text-base md:text-lg"></i>
                                    </div>
                                    <p class="text-xs md:text-sm text-green-600 font-medium">Tersedia</p>
                                    @if($table->completed_today > 0)
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $table->completed_today }} selesai
                                        </p>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chair text-4xl text-gray-300 mb-3"></i>
                    <p class="text-lg">Belum ada meja yang terdaftar</p>
                    <p class="text-sm text-gray-400">Tambahkan QR Code di panel admin</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders - Sama seperti halaman cashier.orders -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-orange-600 mr-2"></i>
                Pesanan Terbaru
            </h2>
            <a href="{{ route('cashier.orders') }}" class="bg-orange-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg hover:bg-orange-700 transition text-sm flex items-center">
                Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-orange-600 text-xs"></i>
                                </div>
                                <div>
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    <div class="text-xs text-gray-500">Meja: {{ $order->qr_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-orange-600">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_method === 'cashier')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Kasir</span>
                            @elseif($order->payment_method === 'e_wallet')
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">E-Wallet</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Transfer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->order_status === 'waiting')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Menunggu</span>
                            @elseif($order->order_status === 'processed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Diproses</span>
                            @elseif($order->order_status === 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Batal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg border border-blue-200 hover:bg-blue-200 transition"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                                <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                                        class="bg-green-100 text-green-600 w-8 h-8 flex items-center justify-center rounded-lg border border-green-200 hover:bg-green-200 transition"
                                        title="Proses Pembayaran Tunai">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                @endif
                                
                                @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending')
                                <button onclick="confirmPayment({{ $order->id }})" 
                                        class="bg-yellow-100 text-yellow-600 w-8 h-8 flex items-center justify-center rounded-lg border border-yellow-200 hover:bg-yellow-200 transition"
                                        title="Konfirmasi Pembayaran">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                                
                                <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
                                   class="bg-gray-100 text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-200 transition"
                                   title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Belum ada pesanan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($recentOrders ?? [] as $order)
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-orange-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="font-mono font-bold text-orange-600">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-orange-600 text-sm">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs text-gray-600">
                        <i class="fas fa-chair mr-1"></i>Meja {{ $order->qr_code }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->payment_method === 'cashier') bg-green-100 text-green-700
                        @elseif($order->payment_method === 'e_wallet') bg-purple-100 text-purple-700
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ $order->payment_method === 'cashier' ? 'Kasir' : ($order->payment_method === 'e_wallet' ? 'E-Wallet' : 'Transfer') }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->order_status === 'waiting') bg-yellow-100 text-yellow-700
                        @elseif($order->order_status === 'processed') bg-blue-100 text-blue-700
                        @elseif($order->order_status === 'completed') bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        @if($order->order_status === 'waiting') Menunggu
                        @elseif($order->order_status === 'processed') Diproses
                        @elseif($order->order_status === 'completed') Selesai
                        @else Batal @endif
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->payment_status === 'paid') bg-green-100 text-green-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('cashier.order.show', $order) }}" 
                       class="bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-blue-200 transition">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                    <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                            class="bg-green-100 text-green-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-green-200 transition">
                        <i class="fas fa-money-bill-wave mr-1"></i>Bayar
                    </button>
                    @endif
                    @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending')
                    <button onclick="confirmPayment({{ $order->id }})" 
                            class="bg-yellow-100 text-yellow-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-yellow-200 transition">
                        <i class="fas fa-check-circle mr-1"></i>Konfirmasi
                    </button>
                    @endif
                    <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
                       class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-gray-200 transition">
                        <i class="fas fa-print mr-1"></i>Struk
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                <p class="text-lg">Belum ada pesanan</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Payment Modal - Sama seperti halaman cashier.orders -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
        <div class="p-4 md:p-5 border-b bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-lg md:text-xl font-bold flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Proses Pembayaran
                </h2>
                <button onclick="closePaymentModal()" class="text-white hover:text-orange-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            <div id="paymentDetail" class="mb-4 md:mb-6 p-4 md:p-5 bg-orange-50 rounded-xl border border-orange-200">
                <!-- Payment details will be loaded here -->
            </div>
            
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="orderId" name="order_id">
                
                <div class="mb-4 md:mb-5">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                        <i class="fas fa-money-bill text-green-600 mr-1"></i>
                        Jumlah Dibayar
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 md:left-4 top-2.5 md:top-3 text-gray-500 font-medium text-sm md:text-base">Rp</span>
                        <input type="number" id="amountPaid" name="amount_paid" 
                               class="w-full pl-10 md:pl-12 pr-3 md:pr-4 py-2 md:py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition text-sm md:text-base"
                               placeholder="0"
                               min="0"
                               required>
                    </div>
                </div>
                
                <div class="mb-4 md:mb-6">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                        <i class="fas fa-undo-alt text-blue-600 mr-1"></i>
                        Kembalian
                    </label>
                    <div class="p-3 md:p-4 bg-gray-100 rounded-xl font-bold text-xl md:text-2xl text-gray-600 text-right" id="changeDisplay">
                        Rp 0
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 py-2 md:py-3 rounded-xl hover:bg-gray-300 transition font-semibold text-sm md:text-base">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white py-2 md:py-3 rounded-xl hover:bg-orange-700 transition font-semibold shadow-lg text-sm md:text-base">
                        <i class="fas fa-check-circle mr-2"></i>Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Function to process cash payment
function processPayment(orderId, totalAmount, orderNumber) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('paymentDetail').innerHTML = `
        <div class="space-y-3">
            <div class="flex justify-between items-center pb-2 border-b border-orange-200">
                <span class="text-gray-600 font-medium text-sm">No. Order:</span>
                <span class="font-mono font-bold text-orange-600 bg-orange-100 px-3 py-1 rounded-lg text-sm">${orderNumber}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold text-base">Total Bayar:</span>
                <span class="font-bold text-orange-600 text-xl md:text-2xl">Rp ${formatPrice(totalAmount)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    document.getElementById('changeDisplay').classList.add('text-gray-600');
    document.getElementById('changeDisplay').classList.remove('text-green-600', 'text-red-600');
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        document.getElementById('amountPaid').focus();
    }, 100);
}

// Calculate change
document.getElementById('amountPaid')?.addEventListener('input', function() {
    const totalText = document.getElementById('paymentDetail').innerHTML;
    const match = totalText.match(/Rp ([\d.]+)/g);
    
    if (match && match.length >= 2) {
        const totalStr = match[1].replace(/\./g, '');
        const total = parseFloat(totalStr);
        const paid = parseFloat(this.value) || 0;
        const change = paid - total;
        
        const changeDisplay = document.getElementById('changeDisplay');
        if (paid < total) {
            changeDisplay.textContent = 'Rp ' + formatPrice(change * -1) + ' (Kurang)';
            changeDisplay.classList.add('text-red-600');
            changeDisplay.classList.remove('text-green-600', 'text-gray-600');
        } else {
            changeDisplay.textContent = 'Rp ' + formatPrice(change);
            changeDisplay.classList.add(change > 0 ? 'text-green-600' : 'text-gray-600');
            changeDisplay.classList.remove(change > 0 ? 'text-red-600' : 'text-green-600');
        }
    }
});

// Submit payment form
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('orderId').value;
    const formData = new FormData(this);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    fetch(`/cashier/orders/${orderId}/process-cash-payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                html: `
                    <div class="text-left">
                        <p class="mb-2">${data.message}</p>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <p class="font-bold text-green-700">Kembalian: Rp ${formatPrice(data.change)}</p>
                        </div>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
            closePaymentModal();
            setTimeout(() => location.reload(), 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonColor: '#f97316',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2 rounded-lg'
                }
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses pembayaran',
            confirmButtonColor: '#f97316',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2 rounded-lg'
            }
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Confirm non-cash payment
function confirmPayment(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang mengkonfirmasi pembayaran',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/cashier/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#f97316',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-6 py-2 rounded-lg'
                        }
                    });
                }
            });
        }
    });
}

// Close modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('paymentForm').reset();
    
    const changeDisplay = document.getElementById('changeDisplay');
    changeDisplay.textContent = 'Rp 0';
    changeDisplay.classList.add('text-gray-600');
    changeDisplay.classList.remove('text-green-600', 'text-red-600');
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Close modal when clicking outside
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Handle enter key
document.getElementById('amountPaid')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    }
});

// Reset table function
function resetTable(qrCode, mejaName, activeCount, hasUnpaid) {
    let warningHtml = '';
    
    if (hasUnpaid) {
        warningHtml = `
            <div class="bg-yellow-100 border border-yellow-300 p-3 rounded-lg mb-3">
                <p class="text-yellow-800 text-sm font-semibold flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Perhatian: Ada pesanan yang belum dibayar!
                </p>
                <p class="text-yellow-700 text-xs mt-1">Pesanan yang belum dibayar akan <strong>dibatalkan</strong> dan stok akan dikembalikan.</p>
            </div>
        `;
    }

    Swal.fire({
        title: `Reset ${mejaName}?`,
        html: `
            <div class="text-left">
                <p class="mb-3">Meja <strong>${mejaName}</strong> memiliki <strong>${activeCount} pesanan aktif</strong>.</p>
                ${warningHtml}
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Apa yang akan terjadi:</strong>
                    </p>
                    <ul class="text-blue-700 text-xs mt-2 space-y-1 ml-4 list-disc">
                        <li>Pesanan yang <strong>sudah dibayar</strong> → ditandai <span class="text-green-600 font-bold">Selesai</span></li>
                        <li>Pesanan yang <strong>belum dibayar</strong> → ditandai <span class="text-red-600 font-bold">Dibatalkan</span></li>
                        <li>Session customer di meja ini akan di-reset</li>
                        <li>Meja siap untuk pelanggan baru</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Ya, Reset Meja!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mereset Meja...',
                html: `<p>Sedang memproses reset untuk <strong>${mejaName}</strong></p>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const btn = document.getElementById(`reset-btn-${qrCode}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mereset...';
            }

            fetch('{{ route("cashier.table.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrCode
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Meja Berhasil Direset!',
                        html: `
                            <div class="text-left">
                                <p class="mb-2">${data.message}</p>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                    <p class="text-green-700 text-sm flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Meja <strong>${mejaName}</strong> sekarang tersedia
                                    </p>
                                </div>
                            </div>
                        `,
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    
                    const card = document.getElementById(`table-card-${qrCode}`);
                    if (card) {
                        card.classList.add('opacity-50');
                    }
                    
                    setTimeout(() => location.reload(), 3000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Reset Meja';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Reset Meja';
                }
            });
        }
    });
}
</script>
@endpush
@endsection
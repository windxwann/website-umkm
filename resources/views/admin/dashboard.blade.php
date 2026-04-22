@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards - Improved responsive grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 sm:mb-8">
    <!-- Total Pendapatan Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Pendapatan Hari Ini</p>
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mt-1 sm:mt-2 truncate">
                    Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
                </h3>
                <span class="inline-flex items-center text-xs text-green-600 bg-green-50 px-2 py-0.5 sm:py-1 rounded-full mt-1 sm:mt-2">
                    <i class="fas fa-calendar-day text-xs mr-1"></i>
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center 
                        bg-gradient-to-br from-green-500 to-green-600 
                        rounded-xl shadow-lg shadow-green-500/20 shrink-0">
                <i class="fas fa-money-bill-wave text-white text-base sm:text-lg md:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Pesanan Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Pesanan Hari Ini</p>
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mt-1 sm:mt-2">
                    {{ number_format($stats['today_orders'] ?? 0) }}
                </h3>
                <span class="inline-flex items-center text-xs text-blue-600 bg-blue-50 px-2 py-0.5 sm:py-1 rounded-full mt-1 sm:mt-2">
                    <i class="fas fa-chart-line text-xs mr-1"></i>
                    {{ $stats['pending_orders'] ?? 0 }} pending
                </span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center 
                        bg-gradient-to-br from-blue-500 to-blue-600 
                        rounded-xl shadow-lg shadow-blue-500/20 shrink-0">
                <i class="fas fa-shopping-cart text-white text-base sm:text-lg md:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Menunggu Diproses -->
    <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Menunggu Diproses</p>
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mt-1 sm:mt-2">
                    {{ number_format($stats['pending_orders'] ?? 0) }}
                </h3>
                <span class="inline-flex items-center text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 sm:py-1 rounded-full mt-1 sm:mt-2">
                    <i class="fas fa-clock text-xs mr-1"></i>
                    Perlu diproses
                </span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center 
                        bg-gradient-to-br from-yellow-500 to-yellow-600 
                        rounded-xl shadow-lg shadow-yellow-500/20 shrink-0">
                <i class="fas fa-clock text-white text-base sm:text-lg md:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Produk -->
    <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Total Produk</p>
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mt-1 sm:mt-2">
                    {{ number_format($stats['total_products'] ?? 0) }}
                </h3>
                <span class="inline-flex items-center text-xs text-purple-600 bg-purple-50 px-2 py-0.5 sm:py-1 rounded-full mt-1 sm:mt-2">
                    <i class="fas fa-boxes text-xs mr-1"></i>
                    {{ $stats['active_products'] ?? 0 }} aktif
                </span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center 
                        bg-gradient-to-br from-purple-500 to-purple-600 
                        rounded-xl shadow-lg shadow-purple-500/20 shrink-0">
                <i class="fas fa-box text-white text-base sm:text-lg md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dua Kolom: Grafik & Ringkasan -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Grafik Penjualan 7 Hari -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <h3 class="font-semibold text-gray-800 flex items-center text-sm sm:text-base">
                <div class="bg-orange-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                    <i class="fas fa-chart-line text-orange-600 text-xs sm:text-sm"></i>
                </div>
                <span>Penjualan 7 Hari Terakhir</span>
            </h3>
        </div>
        <div class="h-64 sm:h-80 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Ringkasan Status -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
        <h3 class="font-semibold text-gray-800 flex items-center mb-4 sm:mb-6 text-sm sm:text-base">
            <div class="bg-orange-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                <i class="fas fa-chart-pie text-orange-600 text-xs sm:text-sm"></i>
            </div>
            <span>Ringkasan Status</span>
        </h3>
        
        <div class="space-y-4 sm:space-y-5">
            @php
                $total = max(($stats['pending_orders'] ?? 0) + ($stats['processed_orders'] ?? 0) + ($stats['completed_orders'] ?? 0) + ($stats['cancelled_orders'] ?? 0), 1);
            @endphp
            
            <div>
                <div class="flex justify-between items-center mb-1 sm:mb-2">
                    <span class="text-xs sm:text-sm text-gray-600 font-medium">Menunggu</span>
                    <span class="text-xs sm:text-sm font-semibold text-yellow-600">{{ number_format($stats['pending_orders'] ?? 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 sm:h-2.5">
                    <div class="bg-yellow-500 h-2 sm:h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['pending_orders'] ?? 0) / $total) * 100, 100) }}%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1 sm:mb-2">
                    <span class="text-xs sm:text-sm text-gray-600 font-medium">Diproses</span>
                    <span class="text-xs sm:text-sm font-semibold text-blue-600">{{ number_format($stats['processed_orders'] ?? 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 sm:h-2.5">
                    <div class="bg-blue-500 h-2 sm:h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['processed_orders'] ?? 0) / $total) * 100, 100) }}%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1 sm:mb-2">
                    <span class="text-xs sm:text-sm text-gray-600 font-medium">Selesai</span>
                    <span class="text-xs sm:text-sm font-semibold text-green-600">{{ number_format($stats['completed_orders'] ?? 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 sm:h-2.5">
                    <div class="bg-green-500 h-2 sm:h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['completed_orders'] ?? 0) / $total) * 100, 100) }}%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1 sm:mb-2">
                    <span class="text-xs sm:text-sm text-gray-600 font-medium">Dibatalkan</span>
                    <span class="text-xs sm:text-sm font-semibold text-red-600">{{ number_format($stats['cancelled_orders'] ?? 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 sm:h-2.5">
                    <div class="bg-red-500 h-2 sm:h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['cancelled_orders'] ?? 0) / $total) * 100, 100) }}%"></div>
                </div>
            </div>

            <div class="pt-3 sm:pt-4 border-t border-gray-100 mt-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm sm:text-base font-semibold text-gray-700">Total Pesanan</span>
                    <span class="text-lg sm:text-xl md:text-2xl font-bold text-orange-600">{{ number_format($stats['total_orders'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dua Kolom: Pesanan Terbaru & Produk Terlaris -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 flex items-center text-sm sm:text-base">
                <div class="bg-blue-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                    <i class="fas fa-history text-blue-600 text-xs sm:text-sm"></i>
                </div>
                <span>Pesanan Terbaru</span>
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-orange-600 hover:text-orange-700 text-xs sm:text-sm font-medium flex items-center group">
                Lihat Semua
                <i class="fas fa-chevron-right ml-1 text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
            @forelse($recentOrders ?? [] as $order)
            <div class="p-3 sm:p-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-receipt text-gray-600 text-sm sm:text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $order->order_number }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $order->customer_name }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-semibold text-orange-600 text-xs sm:text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        @if($order->order_status == 'waiting')
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-yellow-500 rounded-full mr-1"></span>
                                Menunggu
                            </span>
                        @elseif($order->order_status == 'processed')
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-blue-500 rounded-full mr-1"></span>
                                Diproses
                            </span>
                        @elseif($order->order_status == 'completed')
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-green-500 rounded-full mr-1"></span>
                                Selesai
                            </span>
                        @elseif($order->order_status == 'cancelled')
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-red-500 rounded-full mr-1"></span>
                                Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 sm:py-12">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shopping-bag text-gray-400 text-xl sm:text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm sm:text-base">Belum ada pesanan</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center text-sm sm:text-base">
                <div class="bg-red-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                    <i class="fas fa-crown text-red-600 text-xs sm:text-sm"></i>
                </div>
                <span>Produk Terlaris</span>
            </h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
            @forelse($topProducts ?? [] as $index => $product)
            <div class="p-3 sm:p-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 {{ $index == 0 ? 'bg-yellow-100 text-yellow-600' : ($index == 1 ? 'bg-gray-100 text-gray-600' : ($index == 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500')) }} rounded-lg flex items-center justify-center font-bold text-xs sm:text-sm flex-shrink-0">
                        #{{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1 gap-2">
                            <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $product->name }}</p>
                            <p class="text-xs sm:text-sm font-semibold text-orange-600 flex-shrink-0">{{ number_format($product->total_sold) }}x</p>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 sm:h-2">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-1.5 sm:h-2 rounded-full transition-all duration-500" style="width: {{ min($product->percentage ?? 0, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 sm:py-12">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-chart-simple text-gray-400 text-xl sm:text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm sm:text-base">Belum ada data penjualan</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions - Improved responsive grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
    <a href="{{ route('admin.products.create') }}" 
       class="group bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg shadow-orange-500/20 p-3 sm:p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex flex-col items-center text-center">
            <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center mb-1 sm:mb-2 md:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-white text-xs sm:text-sm md:text-xl"></i>
            </div>
            <p class="font-semibold text-white text-xs sm:text-sm">Tambah Produk</p>
            <p class="text-[10px] sm:text-xs text-white/80 mt-0.5 sm:mt-1 hidden xs:block">Buat produk baru</p>
        </div>
    </a>

    <a href="{{ route('admin.qrcodes.create') }}" 
       class="group bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg shadow-green-500/20 p-3 sm:p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex flex-col items-center text-center">
            <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center mb-1 sm:mb-2 md:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-qrcode text-white text-xs sm:text-sm md:text-xl"></i>
            </div>
            <p class="font-semibold text-white text-xs sm:text-sm">Generate QR</p>
            <p class="text-[10px] sm:text-xs text-white/80 mt-0.5 sm:mt-1 hidden xs:block">Buat kode QR baru</p>
        </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" 
       class="group bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg shadow-blue-500/20 p-3 sm:p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex flex-col items-center text-center">
            <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center mb-1 sm:mb-2 md:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-shopping-cart text-white text-xs sm:text-sm md:text-xl"></i>
            </div>
            <p class="font-semibold text-white text-xs sm:text-sm">Lihat Pesanan</p>
            <p class="text-[10px] sm:text-xs text-white/80 mt-0.5 sm:mt-1 hidden xs:block">Kelola pesanan masuk</p>
        </div>
    </a>

    <a href="{{ route('admin.settings') }}" 
       class="group bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl shadow-lg shadow-gray-500/20 p-3 sm:p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex flex-col items-center text-center">
            <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center mb-1 sm:mb-2 md:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-cog text-white text-xs sm:text-sm md:text-xl"></i>
            </div>
            <p class="font-semibold text-white text-xs sm:text-sm">Pengaturan</p>
            <p class="text-[10px] sm:text-xs text-white/80 mt-0.5 sm:mt-1 hidden xs:block">Konfigurasi sistem</p>
        </div>
    </a>
</div>

@push('styles')
<style>
    /* Custom scrollbar untuk list pesanan */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsive text truncate */
    @media (max-width: 640px) {
        .xs\:block {
            display: block;
        }
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
    
    /* Hover effects */
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
    
    /* Chart container responsiveness */
    canvas {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    
    if (ctx) {
        const chartLabels = {!! json_encode($chartLabels ?? []) !!};
        const chartData = {!! json_encode($chartData ?? []) !!};
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: window.innerWidth < 640 ? 3 : 4,
                    pointHoverRadius: window.innerWidth < 640 ? 5 : 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        },
                        titleFont: {
                            size: window.innerWidth < 640 ? 11 : 12
                        },
                        bodyFont: {
                            size: window.innerWidth < 640 ? 10 : 12
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'Jt';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'rb';
                                }
                                return value;
                            },
                            font: {
                                size: window.innerWidth < 640 ? 10 : 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: window.innerWidth < 640 ? 10 : 11
                            },
                            maxRotation: window.innerWidth < 640 ? 45 : 0,
                            minRotation: window.innerWidth < 640 ? 45 : 0
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10,
                        left: window.innerWidth < 640 ? 5 : 10,
                        right: window.innerWidth < 640 ? 5 : 10
                    }
                }
            }
        });
    }
    
    // Handle window resize untuk update chart
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            location.reload();
        }, 250);
    });
});
</script>
@endpush
@endsection
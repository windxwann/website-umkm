@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')
<!-- Welcome Section - Compact -->
<div class="mb-6">
    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Ringkasan Restoran Hari Ini</p>
</div>

<!-- Stats Bento Grid - Compact -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <!-- Revenue -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-gray-100 shadow-sm hover-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 shadow-sm shadow-orange-500/10">
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </div>
            <span class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md uppercase">
                +12%
            </span>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pendapatan</p>
        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-0.5 truncate">
            Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
        </h3>
    </div>

    <!-- Orders -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-gray-100 shadow-sm hover-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shadow-sm shadow-blue-500/10">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            </div>
            <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-md uppercase">
                {{ $stats['pending_orders'] ?? 0 }} Baru
            </span>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pesanan</p>
        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-0.5">
            {{ number_format($stats['today_orders'] ?? 0) }}
        </h3>
    </div>

    <!-- Perlu Diproses -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-gray-100 shadow-sm hover-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shadow-sm shadow-amber-500/10">
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Diproses</p>
        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-0.5">
            {{ number_format($stats['pending_orders'] ?? 0) }}
        </h3>
    </div>

    <!-- Produk Aktif -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-gray-100 shadow-sm hover-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 shadow-sm shadow-purple-500/10">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Produk</p>
        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-0.5">
            {{ number_format($stats['active_products'] ?? 0) }}
        </h3>
    </div>
</div>

<!-- Main Content Grid - Compact -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 mb-8">
    <!-- Chart Card - Compact -->
    <div class="lg:col-span-2 bg-white rounded-[2rem] border border-gray-100 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Performa Penjualan</h3>
                <p class="text-[10px] text-slate-400 font-bold mt-0.5 uppercase tracking-tighter">7 Hari Terakhir</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex flex-col items-end mr-1">
                    <span class="text-[8px] font-black text-slate-400 uppercase">Total</span>
                    <span class="text-xs font-black text-orange-600 leading-none">Rp {{ number_format(array_sum($chartData ?? []), 0, ',', '.') }}</span>
                </div>
                <button class="px-3 py-2 bg-slate-50 text-slate-600 rounded-xl text-[9px] font-black hover:bg-slate-100 transition-all uppercase tracking-widest flex items-center gap-1.5 border border-slate-100">
                    <i data-lucide="download" class="w-3 h-3"></i>
                    Export
                </button>
            </div>
        </div>
        <div class="relative h-[200px] sm:h-[280px]">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Status Pesanan - Compact -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-5 sm:p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Status</h3>
            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[8px] font-black uppercase tracking-tighter">Live</span>
        </div>
        
        <div class="space-y-5">
            @php
                $total = max(($stats['pending_orders'] ?? 0) + ($stats['processed_orders'] ?? 0) + ($stats['completed_orders'] ?? 0) + ($stats['cancelled_orders'] ?? 0), 1);
            @endphp
            
            @foreach([
                ['label' => 'Menunggu', 'count' => $stats['pending_orders'] ?? 0, 'color' => 'bg-amber-500', 'text' => 'text-amber-600'],
                ['label' => 'Diproses', 'count' => $stats['processed_orders'] ?? 0, 'color' => 'bg-blue-500', 'text' => 'text-blue-600'],
                ['label' => 'Selesai', 'count' => $stats['completed_orders'] ?? 0, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-600'],
                ['label' => 'Batal', 'count' => $stats['cancelled_orders'] ?? 0, 'color' => 'bg-rose-500', 'text' => 'text-rose-600']
            ] as $item)
            @php $percentage = ($item['count'] / $total) * 100; @endphp
            <div>
                <div class="flex justify-between items-end mb-1.5">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">{{ $item['label'] }}</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-black text-slate-900 leading-none">{{ number_format($item['count']) }}</span>
                        <span class="text-[8px] font-bold {{ $item['text'] }} bg-opacity-10 rounded">
                            {{ round($percentage) }}%
                        </span>
                    </div>
                </div>
                <div class="w-full bg-slate-50 rounded-full h-1.5 overflow-hidden">
                    <div class="{{ $item['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ min($percentage, 100) }}%"></div>
                </div>
            </div>
            @endforeach

            <div class="pt-4 mt-1 border-t border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total Pesanan</p>
                    <p class="text-lg font-black text-slate-900 leading-none mt-0.5">{{ number_format($stats['total_orders'] ?? 0) }}</p>
                </div>
                <div class="w-9 h-9 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section - Compact -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 mb-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/20">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-orange-600"></i>
                Pesanan Baru
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-orange-600 hover:text-orange-700 text-[9px] font-black uppercase tracking-widest flex items-center gap-1 group">
                Semua
                <i data-lucide="chevron-right" class="w-3 h-3 transition-transform group-hover:translate-x-0.5"></i>
            </a>
        </div>
        <div class="divide-y divide-gray-50 max-h-[350px] overflow-auto custom-scrollbar">
            @forelse($recentOrders ?? [] as $order)
            <div class="px-5 py-4 hover:bg-slate-50 transition-colors group cursor-pointer">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:shadow-sm transition-all shrink-0">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-900 truncate tracking-tight">{{ $order->order_number }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase truncate mt-0.5">{{ $order->customer_name }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-black text-slate-900 leading-none">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter mt-1.5
                            @if($order->order_status == 'waiting') bg-amber-50 text-amber-600
                            @elseif($order->order_status == 'processed') bg-blue-50 text-blue-600
                            @elseif($order->order_status == 'completed') bg-emerald-50 text-emerald-600
                            @else bg-rose-50 text-rose-600 @endif">
                            {{ $order->order_status }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-slate-300">
                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                <p class="text-[9px] font-black uppercase tracking-widest">Kosong</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2 bg-gray-50/20">
            <i data-lucide="award" class="w-4 h-4 text-orange-600"></i>
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Terlaris</h3>
        </div>
        <div class="divide-y divide-gray-50 max-h-[350px] overflow-auto custom-scrollbar">
            @forelse($topProducts ?? [] as $index => $product)
            <div class="px-5 py-4 hover:bg-slate-50 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="text-xs font-black text-slate-300 w-4">#{{ $index + 1 }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-xs font-black text-slate-800 truncate">{{ $product->name }}</p>
                            <span class="text-[10px] font-black text-orange-600">{{ number_format($product->total_sold) }} <small class="text-[8px] text-slate-400">Porsi</small></span>
                        </div>
                        <div class="w-full bg-slate-50 rounded-full h-1">
                            <div class="bg-orange-500 h-1 rounded-full" style="width: {{ min($product->percentage ?? 0, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-slate-300">
                <i data-lucide="bar-chart" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                <p class="text-[9px] font-black uppercase tracking-widest">Kosong</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions - Compact -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach([
        ['label' => 'Produk', 'route' => 'admin.products.create', 'icon' => 'plus-circle', 'color' => 'bg-orange-600'],
        ['label' => 'QR Code', 'route' => 'admin.qrcodes.create', 'icon' => 'qr-code', 'color' => 'bg-emerald-600'],
        ['label' => 'Pesanan', 'route' => 'admin.orders.index', 'icon' => 'shopping-cart', 'color' => 'bg-blue-600'],
        ['label' => 'Setting', 'route' => 'admin.settings', 'icon' => 'settings', 'color' => 'bg-slate-800']
    ] as $action)
    <a href="{{ route($action['route']) }}" 
       class="group {{ $action['color'] }} rounded-3xl p-4 shadow-lg hover:translate-y-[-2px] transition-all duration-200">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                <i data-lucide="{{ $action['icon'] }}" class="text-white w-4 h-4"></i>
            </div>
            <p class="font-black text-white text-[10px] uppercase tracking-widest leading-tight">{{ $action['label'] }}</p>
        </div>
    </a>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const chartLabels = {!! json_encode($chartLabels ?? []) !!};
        const chartData = {!! json_encode($chartData ?? []) !!};
        
        // Gradient for chart
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(249, 115, 22, 0.4)');
        gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: chartData,
                    borderColor: '#f97316',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f97316',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: (context) => 'Rp ' + context.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 9 },
                            callback: (value) => {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'Jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 9 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

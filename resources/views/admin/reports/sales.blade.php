@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-orange-500 mr-2"></i>
            Filter Laporan
        </h3>
        
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-sync-alt mr-2"></i>Reset
                </a>
            </div>
            <div class="flex items-end justify-end gap-2">
                <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </a>
                <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-sm">Total Pesanan</p>
                    <p class="text-xl font-bold mt-1">{{ number_format($summary['total_orders'] ?? 0) }}</p>
                </div>
                <div class="bg-blue-400/30 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-green-100 text-sm">Total Pendapatan</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-400/30 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-purple-100 text-sm">Rata-rata per Order</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($summary['average_order'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-400/30 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-orange-100 text-sm">Total Item Terjual</p>
                    <p class="text-xl font-bold mt-1">{{ number_format($summary['total_items'] ?? 0) }}</p>
                </div>
                <div class="bg-orange-400/30 p-3 rounded-lg">
                    <i class="fas fa-box text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
            Grafik Penjualan Harian
        </h3>
        <div class="h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Two Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Sales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-orange-500 mr-2"></i>
                Penjualan per Kategori
            </h3>
            <div class="space-y-4">
                @forelse($categorySales ?? [] as $category)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $category->category_name }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ number_format($category->total_sold) }} item</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($category->total_sold / max($categorySales->sum('total_sold'), 1)) * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Revenue: Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-credit-card text-orange-500 mr-2"></i>
                Metode Pembayaran
            </h3>
            <div class="space-y-4">
                @forelse($paymentMethods ?? [] as $method)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">
                            @if($method->payment_method == 'cashier')
                                <i class="fas fa-money-bill-wave text-green-500 mr-1"></i> Tunai
                            @elseif($method->payment_method == 'e_wallet')
                                <i class="fas fa-mobile-alt text-purple-500 mr-1"></i> E-Wallet
                            @else
                                <i class="fas fa-university text-blue-500 mr-1"></i> Transfer Bank
                            @endif
                        </span>
                        <span class="text-sm font-semibold text-gray-800">{{ number_format($method->total) }} transaksi</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($method->total / max($paymentMethods->sum('total'), 1)) * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Revenue: Rp {{ number_format($method->total_revenue, 0, ',', '.') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-crown text-orange-500 mr-2"></i>
            Produk Terlaris
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Terjual</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($topProducts ?? [] as $index => $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                                {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : ($index == 1 ? 'bg-gray-100 text-gray-600' : ($index == 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500')) }}">
                                #{{ $index + 1 }}
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $product->product_name }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($product->total_sold) }}x</td>
                        <td class="px-4 py-3 text-right text-orange-600 font-semibold">
                            Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>Belum ada data penjualan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Sales Table -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>
            Detail Penjualan Harian
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Pesanan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($dailySales ?? [] as $day)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ \Carbon\Carbon::parse($day->date)->format('d F Y') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($day->total_orders) }}</td>
                        <td class="px-4 py-3 text-right text-orange-600 font-semibold">
                            Rp {{ number_format($day->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>Belum ada data penjualan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const labels = {!! json_encode($dailySales->pluck('date')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d/m');
    })) !!};
    
    const data = {!! json_encode($dailySales->pluck('total_revenue')) !!};
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data,
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f97316',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
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
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan')

@section('content')
<!-- Filter Section - Compact -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 mb-8">
    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
        <i data-lucide="filter" class="w-4 h-4 text-orange-600"></i>
        Filter Laporan
    </h3>
    
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" 
                   class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
        </div>
        <div>
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" 
                   class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-orange-600 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-[1.02] transition-all">
                <i data-lucide="search" class="w-3.5 h-3.5 inline mr-1.5"></i>Filter
            </button>
            <a href="{{ route('admin.reports.sales') }}" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-slate-200 transition-all">
                Reset
            </a>
        </div>
        <div class="flex items-end gap-2">
            <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="flex-1 bg-rose-600 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center shadow-lg shadow-rose-600/20 hover:scale-[1.02] transition-all">
                <i data-lucide="file-text" class="w-3.5 h-3.5 inline mr-1.5"></i>PDF
            </a>
            <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                <i data-lucide="table" class="w-3.5 h-3.5 inline mr-1.5"></i>Excel
            </a>
        </div>
    </form>
</div>

<!-- Stats Bar - Compact -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
    @foreach([
        ['label' => 'Total Pesanan', 'value' => number_format($summary['total_orders'] ?? 0), 'icon' => 'shopping-cart', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($summary['total_revenue'] ?? 0, 0, ',', '.'), 'icon' => 'wallet', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
        ['label' => 'Rata-rata/Order', 'value' => 'Rp ' . number_format($summary['average_order'] ?? 0, 0, ',', '.'), 'icon' => 'trending-up', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
        ['label' => 'Total Terjual', 'value' => number_format($summary['total_items'] ?? 0), 'icon' => 'package', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50']
    ] as $stat)
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 {{ $stat['bg'] }} rounded-lg flex items-center justify-center {{ $stat['color'] }}">
            <i data-lucide="{{ $stat['icon'] }}" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stat['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Chart & Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Chart Card - Compact -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-orange-600"></i>
            Grafik Penjualan
        </h3>
        <div class="relative h-[250px]">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Category Sales -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 overflow-hidden">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i data-lucide="grid" class="w-4 h-4 text-orange-600"></i>
            Kategori Terlaris
        </h3>
        <div class="space-y-5">
            @forelse($categorySales ?? [] as $category)
            <div>
                <div class="flex justify-between items-end mb-1.5">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider truncate max-w-[150px]">{{ $category->category_name }}</span>
                    <span class="text-xs font-black text-slate-900 leading-none">{{ number_format($category->total_sold) }} item</span>
                </div>
                <div class="w-full bg-slate-50 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-orange-500 h-full rounded-full transition-all duration-700" style="width: {{ ($category->total_sold / max($categorySales->sum('total_sold'), 1)) * 100 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-[10px] text-center text-slate-300 uppercase font-black">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Product Table -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/20">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
            <i data-lucide="award" class="w-4 h-4 text-orange-600"></i>
            Produk Terlaris
        </h3>
    </div>
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Rank</th>
                    <th class="px-6 py-4">Produk</th>
                    <th class="px-6 py-4 text-right">Terjual</th>
                    <th class="px-6 py-4 text-right">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($topProducts ?? [] as $index => $product)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4"><span class="w-6 h-6 rounded-lg flex items-center justify-center bg-slate-100 text-[10px] font-black text-slate-500">#{{ $index + 1 }}</span></td>
                    <td class="px-6 py-4 text-xs font-black text-slate-900">{{ $product->product_name }}</td>
                    <td class="px-6 py-4 text-right text-xs font-black text-slate-900">{{ number_format($product->total_sold) }}</td>
                    <td class="px-6 py-4 text-right text-xs font-black text-orange-600">Rp{{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    const labels = {!! json_encode($dailySales->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!};
    const data = {!! json_encode($dailySales->pluck('total_revenue')) !!};
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: '#f97316',
                borderRadius: 8,
                barThickness: 20
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { size: 9, weight: 'bold' } } },
                x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
            }
        }
    });
});
</script>
@endpush

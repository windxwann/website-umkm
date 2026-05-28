@extends('layouts.cashier')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-4 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Riwayat Transaksi</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-history mr-2"></i>Semua riwayat transaksi
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-archive text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Filter Section - Responsive -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-orange-600 mr-2"></i>
            Filter Pencarian
        </h3>
        
        <form method="GET" action="{{ route('cashier.transactions.history') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    </div>
                </div>
                
                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        <select name="payment_method" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            <option value="">Semua Metode</option>
                            <option value="cashier" {{ request('payment_method') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                            <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        </select>
                    </div>
                </div>
                
                <!-- Payment Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <select name="payment_status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                
                <!-- Order Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pesanan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-clipboard-list"></i>
                        </span>
                        <select name="order_status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            <option value="">Semua Status</option>
                            <option value="waiting" {{ request('order_status') == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processed" {{ request('order_status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari No. Order</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ORD-2025-0001"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap justify-end gap-2">
                <button type="submit" class="flex-1 sm:flex-none bg-orange-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-orange-700 transition flex items-center justify-center text-sm">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('cashier.transactions.history') }}" class="flex-1 sm:flex-none bg-gray-500 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition flex items-center justify-center text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Reset
                </a>
                <button onclick="exportToExcel()" class="flex-1 sm:flex-none bg-green-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center justify-center text-sm">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <button onclick="exportToPDF()" class="flex-1 sm:flex-none bg-red-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-red-700 transition flex items-center justify-center text-sm">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards - Sama dengan Dashboard -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-600 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ number_format($orders->total() ?? 0) }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-600 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-base md:text-xl font-bold text-green-600">
                        Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-600 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Rata-rata</p>
                    <p class="text-base md:text-xl font-bold text-orange-600">
                        Rp {{ number_format($orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-chart-line text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-600 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Transaksi Lunas</p>
                    <p class="text-xl md:text-2xl font-bold text-purple-600">
                        {{ $orders->where('payment_status', 'paid')->count() }}
                    </p>
                </div>
                <div class="bg-purple-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-check-circle text-purple-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table - SAMA PERSIS DENGAN DASHBOARD -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-orange-600 mr-2"></i>
                Daftar Riwayat Transaksi
            </h2>
        </div>
        
        <!-- Desktop Table View - SAMA PERSIS DENGAN DASHBOARD -->
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
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-orange-600 text-xs"></i>
                                </div>
                                <div>
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    <div class="text-xs text-gray-500">Meja: {{ $order->qr_code ?? '-' }}</div>
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
                            {{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg border border-blue-200 hover:bg-blue-200 transition"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
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
                            <i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Tidak ada transaksi yang ditemukan</p>
                            <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($orders as $order)
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
                        <div class="text-xs text-gray-500">{{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs text-gray-600">
                        <i class="fas fa-chair mr-1"></i>Meja {{ $order->qr_code ?? '-' }}
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
                    <a href="{{ route('cashier.receipt', $order) }}" target="_blank"
                       class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-gray-200 transition">
                        <i class="fas fa-print mr-1"></i>Struk
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i>
                <p class="text-lg">Tidak ada transaksi yang ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="px-4 md:px-6 py-4 border-t">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-xs sm:text-sm text-gray-500">
                    Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() ?? 0 }} transaksi
                </div>
                <div>
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(window.location.search).toString();
    window.location.href = "{{ route('cashier.transactions.export-excel') }}?" + params;
}

function exportToPDF() {
    const params = new URLSearchParams(window.location.search).toString();
    window.location.href = "{{ route('cashier.transactions.export-pdf') }}?" + params;
}
</script>
@endpush
@endsection
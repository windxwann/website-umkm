@extends('layouts.cashier')

@section('title', 'Transaksi Hari Ini')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-4 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Transaksi Hari Ini</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-calendar mr-2"></i>{{ now()->format('d F Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cashier.daily-summary') }}" 
                   class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i>
                    <span class="hidden md:inline">Ringkasan Harian</span>
                    <span class="md:hidden">Ringkasan</span>
                </a>
                <a href="{{ route('cashier.history') }}" 
                   class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    <span class="hidden md:inline">Riwayat</span>
                    <span class="md:hidden">Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $summary['total'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-base md:text-xl font-bold text-green-600">
                        Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pembayaran Tunai</p>
                    <p class="text-base md:text-xl font-bold text-orange-600">
                        Rp {{ number_format($summary['cash'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-cash-register text-orange-600 text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Non-Tunai</p>
                    <p class="text-base md:text-xl font-bold text-purple-600">
                        Rp {{ number_format(($summary['ewallet'] ?? 0) + ($summary['transfer'] ?? 0), 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-purple-100 w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <form method="GET" action="{{ route('cashier.transactions.today') }}" class="flex flex-col md:flex-row flex-wrap gap-3 md:gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    <option value="">Semua Metode</option>
                    <option value="cashier" {{ request('payment_method') == 'cashier' ? 'selected' : '' }}>💵 Tunai (Kasir)</option>
                    <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>📱 E-Wallet</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                </select>
            </div>
            
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-orange-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-orange-700 transition text-sm flex-1 md:flex-none">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cashier.transactions.today') }}" class="bg-gray-500 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition text-sm flex-1 md:flex-none">
                    <i class="fas fa-redo-alt mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Section - Card View (Mobile & Desktop) -->
    <div class="space-y-3 md:space-y-4">
        @forelse($transactions as $transaction)
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Header Card dengan Order Number dan Waktu -->
            <div class="bg-gradient-to-r from-gray-50 to-white px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-receipt text-orange-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-mono font-bold text-orange-600 text-sm md:text-base">{{ $transaction->order_number }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($transaction->order_status === 'waiting')
                            <span class="px-2 md:px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-clock text-xs"></i> Menunggu
                            </span>
                        @elseif($transaction->order_status === 'processed')
                            <span class="px-2 md:px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-cog text-xs"></i> Diproses
                            </span>
                        @elseif($transaction->order_status === 'completed')
                            <span class="px-2 md:px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle text-xs"></i> Selesai
                            </span>
                        @elseif($transaction->order_status === 'cancelled')
                            <span class="px-2 md:px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-times-circle text-xs"></i> Dibatalkan
                            </span>
                        @endif
                        
                        @if($transaction->payment_status === 'paid')
                            <span class="px-2 md:px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle text-xs"></i> Lunas
                            </span>
                        @else
                            <span class="px-2 md:px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium flex items-center gap-1">
                                <i class="fas fa-hourglass-half text-xs"></i> Pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body Card - Informasi Customer -->
            <div class="px-4 md:px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Informasi Customer -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Customer</p>
                            <p class="font-semibold text-gray-800">{{ $transaction->customer_name }}</p>
                        </div>
                    </div>

                    <!-- Informasi Meja / Lokasi dengan detail Indoor/Outdoor -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chair text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Meja / Lokasi</p>
                            <div class="flex items-center gap-2">
                                @if($transaction->table_number)
                                    <span class="font-semibold text-gray-800">{{ $transaction->table_number }}</span>
                                    @if($transaction->table_location)
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            @if($transaction->table_location == 'indoor') bg-green-100 text-green-700
                                            @elseif($transaction->table_location == 'outdoor') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            <i class="fas @if($transaction->table_location == 'indoor') fa-building @elseif($transaction->table_location == 'outdoor') fa-tree @else fa-map-marker-alt @endif mr-1"></i>
                                            {{ ucfirst($transaction->table_location) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="font-semibold text-gray-800">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Metode Pembayaran -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            @if($transaction->payment_method === 'cashier')
                                <i class="fas fa-cash-register text-purple-600"></i>
                            @elseif($transaction->payment_method === 'e_wallet')
                                <i class="fas fa-mobile-alt text-purple-600"></i>
                            @else
                                <i class="fas fa-university text-purple-600"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-800">
                                @if($transaction->payment_method === 'cashier')
                                    Kasir
                                @elseif($transaction->payment_method === 'e_wallet')
                                    E-Wallet
                                @else
                                    Transfer Bank
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Total Transaksi</p>
                        <p class="text-xl md:text-2xl font-bold text-orange-600">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <a href="{{ route('cashier.order.show', $transaction) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 md:px-5 py-2 rounded-lg transition text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            <span class="hidden md:inline">Detail</span>
                        </a>
                        <a href="{{ route('cashier.receipt', $transaction) }}" target="_blank"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 md:px-5 py-2 rounded-lg transition text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-print"></i>
                            <span class="hidden md:inline">Struk</span>
                        </a>
                        @if($transaction->order_status !== 'completed' && $transaction->order_status !== 'cancelled')
                        <a href="{{ route('cashier.order.edit', $transaction) }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 md:px-5 py-2 rounded-lg transition text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            <span class="hidden md:inline">Edit</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
            <i class="fas fa-receipt text-5xl md:text-6xl mb-4 text-gray-300"></i>
            <p class="text-gray-500 text-lg">Belum ada transaksi hari ini</p>
            <p class="text-gray-400 text-sm mt-2">Transaksi akan muncul di sini setelah pelanggan melakukan pembayaran</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($transactions) && method_exists($transactions, 'hasPages') && $transactions->hasPages())
    <div class="mt-6 md:mt-8">
        <div class="bg-white rounded-xl shadow-lg px-4 py-3">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    /* Custom scrollbar untuk body */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #ea580c;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #c2410c;
    }
    
    * {
        scrollbar-width: thin;
        scrollbar-color: #ea580c #f1f1f1;
    }
    
    /* Animasi hover untuk card */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush

@endsection
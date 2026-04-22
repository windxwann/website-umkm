@extends('layouts.cashier')

@section('title', 'Transaksi Hari Ini')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Transaksi Hari Ini</h1>
                <p class="text-orange-100">
                    <i class="fas fa-calendar mr-2"></i>{{ now()->format('d F Y') }}
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Transaksi</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $summary['total'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pembayaran Cash -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Pembayaran Tunai</p>
                    <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($summary['cash'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-100 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-cash-register text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pembayaran Non-Tunai -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Non-Tunai</p>
                    <p class="text-2xl font-bold text-purple-600">Rp {{ number_format(($summary['ewallet'] ?? 0) + ($summary['transfer'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ route('cashier.transactions.today') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                    <option value="">Semua Metode</option>
                    <option value="cashier" {{ request('payment_method') == 'cashier' ? 'selected' : '' }}>Tunai (Kasir)</option>
                    <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cashier.transactions.today') }}" class="ml-2 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-orange-600 mr-2"></i>
                Daftar Transaksi Hari Ini
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $transaction->order_number }}</td>
                        <td class="px-6 py-4">{{ $transaction->customer_name }}</td>
                        <td class="px-6 py-4 font-bold text-orange-600">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->payment_method === 'cashier')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Tunai</span>
                            @elseif($transaction->payment_method === 'e_wallet')
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">E-Wallet</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Transfer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->order_status === 'waiting')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Menunggu</span>
                            @elseif($transaction->order_status === 'processed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Diproses</span>
                            @elseif($transaction->order_status === 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                            @elseif($transaction->order_status === 'cancelled')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Batal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('cashier.order.show', $transaction) }}" 
                                   class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-200 transition"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cashier.receipt', $transaction) }}" target="_blank"
                                   class="bg-gray-100 text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition"
                                   title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-receipt text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Belum ada transaksi hari ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination - Hanya tampil jika ada pagination -->
        @if(method_exists($transactions, 'links') && $transactions->hasPages())
        <div class="p-4 border-t">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
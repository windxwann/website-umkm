@extends('layouts.cashier')

@section('title', 'Rekap Harian')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-4 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Rekap Harian</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-chart-bar mr-2"></i>Ringkasan transaksi per hari
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-calendar-alt text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Date Picker - Responsive -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <form method="GET" action="{{ route('cashier.transactions.daily-summary') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 min-w-[200px] w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                </div>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-orange-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-orange-700 transition flex items-center justify-center text-sm">
                    <i class="fas fa-search mr-2"></i>Tampilkan
                </button>
                <button type="button" onclick="window.location.href='{{ route('cashier.transactions.daily-summary', ['date' => date('Y-m-d')]) }}'" 
                        class="flex-1 md:flex-none bg-gray-500 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition flex items-center justify-center text-sm">
                    <i class="fas fa-calendar-day mr-2"></i>Hari Ini
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards - Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="bg-blue-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-lg md:text-2xl"></i>
                </div>
                <span class="text-xs text-gray-400">Total Transaksi</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $summary['total_transactions'] ?? 0 }}</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">transaksi hari ini</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="bg-green-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-600 text-lg md:text-2xl"></i>
                </div>
                <span class="text-xs text-gray-400">Total Pendapatan</span>
            </div>
            <p class="text-lg md:text-2xl lg:text-3xl font-bold text-gray-800">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">pendapatan kotor</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 border-l-4 border-purple-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="bg-purple-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-cash-register text-purple-600 text-lg md:text-2xl"></i>
                </div>
                <span class="text-xs text-gray-400">Pembayaran Tunai</span>
            </div>
            <p class="text-lg md:text-2xl lg:text-3xl font-bold text-gray-800">Rp {{ number_format($summary['cash_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">{{ $summary['cash_count'] ?? 0 }} transaksi</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 border-l-4 border-orange-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="bg-orange-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-credit-card text-orange-600 text-lg md:text-2xl"></i>
                </div>
                <span class="text-xs text-gray-400">Non-Tunai</span>
            </div>
            <p class="text-lg md:text-2xl lg:text-3xl font-bold text-gray-800">Rp {{ number_format(($summary['ewallet_revenue'] ?? 0) + ($summary['transfer_revenue'] ?? 0), 0, ',', '.') }}</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">{{ ($summary['ewallet_count'] ?? 0) + ($summary['transfer_count'] ?? 0) }} transaksi</p>
        </div>
    </div>

    <!-- Detail Summary - Responsive Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- Payment Method Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-orange-600 mr-2"></i>
                Rincian Metode Pembayaran
            </h3>
            
            <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-money-bill-wave text-white text-xs md:text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700 text-sm md:text-base">Tunai</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600 text-sm md:text-base">Rp {{ number_format($summary['cash_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['cash_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-mobile-alt text-white text-xs md:text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700 text-sm md:text-base">E-Wallet</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-purple-600 text-sm md:text-base">Rp {{ number_format($summary['ewallet_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['ewallet_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-university text-white text-xs md:text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700 text-sm md:text-base">Transfer Bank</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-blue-600 text-sm md:text-base">Rp {{ number_format($summary['transfer_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['transfer_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>
                Status Pesanan
            </h3>
            
            <div class="space-y-3 md:space-y-4">
                <div>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Menunggu</span>
                        <span class="font-semibold">{{ $summary['waiting_count'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ min($summary['waiting_percentage'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Diproses</span>
                        <span class="font-semibold">{{ $summary['processed_count'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min($summary['processed_percentage'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Selesai</span>
                        <span class="font-semibold">{{ $summary['completed_count'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($summary['completed_percentage'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Dibatalkan</span>
                        <span class="font-semibold">{{ $summary['cancelled_count'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ min($summary['cancelled_percentage'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jam Sibuk - Mobile Friendly -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clock text-orange-600 mr-2"></i>
                Jam Sibuk
            </h3>
            
            <div class="space-y-3">
                @forelse($peakHours ?? [] as $hour)
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 w-16">{{ $hour['hour'] ?? '-' }}</span>
                    <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ min($hour['percentage'] ?? 0, 100) }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 w-12 text-right">{{ $hour['count'] ?? 0 }}x</span>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-chart-line text-3xl mb-2 text-gray-300"></i>
                    <p class="text-sm">Belum ada data jam sibuk</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Daftar Transaksi - Responsive Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-receipt text-orange-600 mr-2"></i>
                Daftar Transaksi <span class="ml-2 text-sm text-gray-500">({{ $transactions->count() }})</span>
            </h2>
            <div class="text-xs text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Klik nomor order untuk melihat detail
            </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $transaction->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-orange-600 text-xs"></i>
                                </div>
                                <span>{{ $transaction->customer_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-orange-600">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->payment_method === 'cashier')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">💵 Tunai</span>
                            @elseif($transaction->payment_method === 'e_wallet')
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">📱 E-Wallet</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">🏦 Transfer</span>
                            @endif
                        </tr>
                        <td class="px-6 py-4">
                            @if($transaction->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✅ Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>
                                Kasir
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('cashier.order.show', $transaction) }}" 
                               class="bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-200 transition inline-flex items-center">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Tidak ada transaksi pada tanggal ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($transactions as $transaction)
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-mono font-bold text-orange-600 text-sm">{{ $transaction->order_number }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-orange-600 text-sm">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="flex items-center mb-3 p-2 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-user text-orange-600 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-sm">{{ $transaction->customer_name }}</div>
                        <div class="text-xs text-gray-500">Customer</div>
                    </div>
                    <div>
                        @if($transaction->payment_method === 'cashier')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">💵 Tunai</span>
                        @elseif($transaction->payment_method === 'e_wallet')
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">📱 E-Wallet</span>
                        @else
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">🏦 Transfer</span>
                        @endif
                    </div>
                </div>

                <!-- Status Badges -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($transaction->payment_status === 'paid')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">✅ Lunas</span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">⏳ Pending</span>
                    @endif
                    <span class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-user-circle mr-1"></i>Kasir
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('cashier.order.show', $transaction) }}" 
                       class="flex-1 bg-blue-100 text-blue-600 px-3 py-2 rounded-lg text-xs font-medium text-center hover:bg-blue-200 transition">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-calendar-times text-5xl mb-3 text-gray-300"></i>
                <p class="text-lg">Tidak ada transaksi pada tanggal ini</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(isset($transactions) && method_exists($transactions, 'hasPages') && $transactions->hasPages())
        <div class="px-4 md:px-6 py-4 border-t">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
@extends('layouts.cashier')

@section('title', 'Rekap Harian')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Rekap Harian</h1>
                <p class="text-orange-100">
                    <i class="fas fa-chart-bar mr-2"></i>Ringkasan transaksi per hari
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-calendar-alt text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Date Picker -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ route('cashier.transactions.daily-summary') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition flex items-center">
                    <i class="fas fa-search mr-2"></i>Tampilkan
                </button>
                <button type="button" onclick="window.location.href='{{ route('cashier.transactions.daily-summary', ['date' => date('Y-m-d')]) }}'" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                    <i class="fas fa-calendar-day mr-2"></i>Hari Ini
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
                <span class="text-sm text-gray-400">Total Transaksi</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $summary['total_transactions'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-2">transaksi hari ini</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
                <span class="text-sm text-gray-400">Total Pendapatan</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-2">pendapatan kotor</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-cash-register text-purple-600 text-2xl"></i>
                </div>
                <span class="text-sm text-gray-400">Pembayaran Tunai</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($summary['cash_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $summary['cash_count'] ?? 0 }} transaksi</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-credit-card text-orange-600 text-2xl"></i>
                </div>
                <span class="text-sm text-gray-400">Non-Tunai</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp {{ number_format(($summary['ewallet_revenue'] ?? 0) + ($summary['transfer_revenue'] ?? 0), 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ ($summary['ewallet_count'] ?? 0) + ($summary['transfer_count'] ?? 0) }} transaksi</p>
        </div>
    </div>

    <!-- Detail Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Payment Method Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-orange-600 mr-2"></i>
                Rincian Metode Pembayaran
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-money-bill-wave text-white text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700">Tunai</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600">Rp {{ number_format($summary['cash_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['cash_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-mobile-alt text-white text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700">E-Wallet</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-purple-600">Rp {{ number_format($summary['ewallet_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['ewallet_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-university text-white text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700">Transfer Bank</span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-blue-600">Rp {{ number_format($summary['transfer_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $summary['transfer_count'] ?? 0 }} transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>
                Status Pesanan
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Menunggu</span>
                    <span class="font-semibold">{{ $summary['waiting_count'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $summary['waiting_percentage'] ?? 0 }}%"></div>
                </div>
                
                <div class="flex justify-between items-center mt-3">
                    <span class="text-gray-600">Diproses</span>
                    <span class="font-semibold">{{ $summary['processed_count'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $summary['processed_percentage'] ?? 0 }}%"></div>
                </div>
                
                <div class="flex justify-between items-center mt-3">
                    <span class="text-gray-600">Selesai</span>
                    <span class="font-semibold">{{ $summary['completed_count'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $summary['completed_percentage'] ?? 0 }}%"></div>
                </div>
                
                <div class="flex justify-between items-center mt-3">
                    <span class="text-gray-600">Dibatalkan</span>
                    <span class="font-semibold">{{ $summary['cancelled_count'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $summary['cancelled_percentage'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Jam Sibuk -->
        <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clock text-orange-600 mr-2"></i>
                Jam Sibuk
            </h3>
            
            <div class="space-y-3">
                @foreach($peakHours ?? [] as $hour)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $hour['hour'] }}</span>
                    <div class="flex items-center flex-1 ml-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $hour['percentage'] }}%"></div>
                        </div>
                        <span class="ml-3 text-sm font-semibold text-gray-700">{{ $hour['count'] }}x</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Daftar Transaksi -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-receipt text-orange-600 mr-2"></i>
                Daftar Transaksi ({{ $transactions->count() }})
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kasir</th>
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
                            @if($transaction->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $transaction->user->name ?? 'Admin' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Tidak ada transaksi pada tanggal ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
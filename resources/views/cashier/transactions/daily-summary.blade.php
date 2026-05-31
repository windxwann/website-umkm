@extends('layouts.cashier')

@section('title', 'Rekap Harian')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Rekap Harian</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-chart-bar text-orange-600"></i>
                Ringkasan performa penjualan
            </p>
        </div>
        <div class="w-full md:w-auto">
            <form method="GET" action="{{ route('cashier.transactions.daily-summary') }}" class="flex gap-2">
                <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" 
                       class="flex-1 bg-white border border-slate-100 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
                <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center border border-blue-100">
                    <i class="fas fa-shopping-cart text-blue-600 text-xs"></i>
                </div>
                <span class="text-2xl font-black text-slate-900">{{ $summary['total_transactions'] ?? 0 }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Transaksi</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-emerald-600">Rp{{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pendapatan</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center border border-orange-100">
                    <i class="fas fa-cash-register text-orange-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-orange-600">Rp{{ number_format($summary['cash_revenue'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tunai</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center border border-purple-100">
                    <i class="fas fa-credit-card text-purple-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-purple-600">Rp{{ number_format(($summary['ewallet_revenue'] ?? 0) + ($summary['transfer_revenue'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Non-Tunai</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <!-- Payment Breakdown -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-3">
                <i class="fas fa-chart-pie text-orange-600"></i>
                Metode Pembayaran
            </h3>
            <div class="space-y-6">
                @foreach([
                    ['Tunai', $summary['cash_revenue'] ?? 0, $summary['cash_count'] ?? 0, 'bg-emerald-500', 'fa-money-bill-wave'],
                    ['E-Wallet', $summary['ewallet_revenue'] ?? 0, $summary['ewallet_count'] ?? 0, 'bg-purple-500', 'fa-mobile-alt'],
                    ['Transfer', $summary['transfer_revenue'] ?? 0, $summary['transfer_count'] ?? 0, 'bg-blue-500', 'fa-university']
                ] as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 {{ $item[3] }} rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas {{ $item[4] }} text-xs"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 text-sm leading-none">{{ $item[0] }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1.5">{{ $item[2] }} Transaksi</p>
                        </div>
                    </div>
                    <p class="font-black text-slate-900 text-sm">Rp{{ number_format($item[1], 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-3">
                <i class="fas fa-clipboard-list text-orange-600"></i>
                Status Pesanan
            </h3>
            <div class="space-y-6">
                @foreach([
                    ['Menunggu', $summary['waiting_count'] ?? 0, $summary['waiting_percentage'] ?? 0, 'bg-amber-500'],
                    ['Diproses', $summary['processed_count'] ?? 0, $summary['processed_percentage'] ?? 0, 'bg-blue-500'],
                    ['Selesai', $summary['completed_count'] ?? 0, $summary['completed_percentage'] ?? 0, 'bg-emerald-500'],
                    ['Batal', $summary['cancelled_count'] ?? 0, $summary['cancelled_percentage'] ?? 0, 'bg-rose-500']
                ] as $item)
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $item[0] }}</span>
                        <span class="font-black text-slate-900 text-xs">{{ $item[1] }}</span>
                    </div>
                    <div class="w-full bg-slate-50 rounded-full h-1.5 overflow-hidden">
                        <div class="{{ $item[3] }} h-full transition-all duration-1000" style="width: {{ $item[2] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Peak Hours -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-3">
                <i class="fas fa-clock text-orange-600"></i>
                Jam Sibuk
            </h3>
            <div class="space-y-4">
                @forelse($peakHours ?? [] as $hour)
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black text-slate-400 w-12">{{ $hour['hour'] ?? '-' }}</span>
                    <div class="flex-1 bg-slate-50 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-orange-500 h-full transition-all duration-1000" style="width: {{ $hour['percentage'] ?? 0 }}%"></div>
                    </div>
                    <span class="text-[10px] font-black text-slate-900 w-8 text-right">{{ $hour['count'] ?? 0 }}x</span>
                </div>
                @empty
                <div class="text-center py-10">
                    <i class="fas fa-clock text-slate-200 text-3xl mb-4"></i>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Data belum tersedia</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detailed List -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center">
                <i class="fas fa-list text-orange-600 mr-3"></i>
                Daftar Transaksi Detail
            </h2>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1 rounded-full">{{ $transactions->count() }} Record</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-400">
                    <tr class="text-left">
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Waktu</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Order</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest text-right">Total</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Metode</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-6 text-[10px] font-bold text-slate-400">
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-900 text-sm">#{{ $transaction->order_number }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $transaction->customer_name }}</p>
                        </td>
                        <td class="px-8 py-6 text-right font-black text-orange-600">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td class="px-8 py-6 uppercase text-[9px] font-black text-slate-600">{{ $transaction->payment_method }}</td>
                        <td class="px-8 py-6">
                            <a href="{{ route('cashier.order.show', $transaction) }}" 
                               class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm mx-auto">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-widest">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

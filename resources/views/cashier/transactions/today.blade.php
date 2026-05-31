@extends('layouts.cashier')

@section('title', 'Transaksi Hari Ini')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Transaksi Hari Ini</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-calendar text-orange-600"></i>
                {{ now()->format('d F Y') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('cashier.daily-summary') }}" 
               class="bg-white border border-slate-100 text-slate-600 px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center gap-2">
                <i class="fas fa-chart-bar text-orange-600"></i>
                Ringkasan
            </a>
            <a href="{{ route('cashier.history') }}" 
               class="bg-white border border-slate-100 text-slate-600 px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center gap-2">
                <i class="fas fa-history text-orange-600"></i>
                Riwayat
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center border border-blue-100">
                    <i class="fas fa-shopping-cart text-blue-600 text-xs"></i>
                </div>
                <span class="text-2xl font-black text-slate-900">{{ $summary['total'] ?? 0 }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Transaksi</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-emerald-600">Rp{{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pendapatan</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center border border-orange-100">
                    <i class="fas fa-cash-register text-orange-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-orange-600">Rp{{ number_format($summary['cash'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tunai</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center border border-purple-100">
                    <i class="fas fa-credit-card text-purple-600 text-xs"></i>
                </div>
                <span class="text-xl font-black text-purple-600">Rp{{ number_format(($summary['ewallet'] ?? 0) + ($summary['transfer'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Non-Tunai</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
        <form method="GET" action="{{ route('cashier.transactions.today') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status</label>
                <select name="status" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
            </div>
            
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Metode Bayar</label>
                <select name="payment_method" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                    <option value="">Semua Metode</option>
                    <option value="cashier" {{ request('payment_method') == 'cashier' ? 'selected' : '' }}>💵 Tunai (Kasir)</option>
                    <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>📱 E-Wallet</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cashier.transactions.today') }}" class="w-12 h-12 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-redo-alt text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="space-y-6">
        @forelse($transactions as $transaction)
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
            <div class="p-6 md:p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center border border-orange-100 shadow-sm shadow-orange-600/5">
                        <i class="fas fa-receipt text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-lg leading-none tracking-tight">#{{ $transaction->order_number }}</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1.5 flex items-center gap-2">
                            <i class="fas fa-clock text-[8px]"></i>
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest
                        @if($transaction->order_status === 'completed') bg-emerald-50 text-emerald-600
                        @elseif($transaction->order_status === 'processed') bg-blue-50 text-blue-600
                        @elseif($transaction->order_status === 'waiting') bg-amber-50 text-amber-600
                        @else bg-rose-50 text-rose-600 @endif">
                        {{ $transaction->order_status }}
                    </span>
                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest
                        @if($transaction->payment_status === 'paid') bg-emerald-50 text-emerald-600
                        @else bg-amber-50 text-amber-600 @endif">
                        {{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
            </div>

            <div class="p-6 md:p-8 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pelanggan</p>
                        <p class="font-black text-slate-900 text-sm">{{ $transaction->customer_name }}</p>
                    </div>
                    <div class="p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Meja / Lokasi</p>
                        <p class="font-black text-slate-900 text-sm">
                            {{ $transaction->qrCodeRelation->meja ?? $transaction->table_number ?? $transaction->qr_code ?? '-' }}
                            @if($transaction->table_location)
                            <span class="ml-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest">({{ $transaction->table_location }})</span>
                            @endif
                        </p>
                    </div>
                    <div class="p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Metode Bayar</p>
                        <p class="font-black text-slate-900 text-sm uppercase">{{ $transaction->payment_method === 'cashier' ? 'Kasir' : ($transaction->payment_method === 'e_wallet' ? 'E-Wallet' : 'Transfer') }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                        <p class="text-2xl font-black text-orange-600 tracking-tight">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="flex gap-2 w-full sm:w-auto">
                        <a href="{{ route('cashier.order.show', $transaction) }}" 
                           class="flex-1 sm:flex-none w-11 h-11 flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-slate-900/10">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('cashier.receipt', $transaction) }}" target="_blank"
                           class="flex-1 sm:flex-none w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                            <i class="fas fa-print text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-20 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-receipt text-slate-200 text-3xl"></i>
            </div>
            <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Tidak ada transaksi hari ini</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($transactions) && method_exists($transactions, 'hasPages') && $transactions->hasPages())
    <div class="pt-6">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

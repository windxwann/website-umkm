@extends('layouts.cashier')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Riwayat Transaksi</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-history text-orange-600"></i>
                Telusuri seluruh riwayat pesanan
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportToExcel()" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl hover:bg-emerald-700 transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                Excel
            </button>
            <button onclick="exportToPDF()" class="bg-rose-600 text-white px-6 py-2.5 rounded-xl hover:bg-rose-700 transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-rose-600/20 flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                PDF
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
            <i class="fas fa-filter text-orange-600"></i>
            Filter Pencarian
        </h3>
        
        <form method="GET" action="{{ route('cashier.transactions.history') }}" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Date Range -->
                <div class="space-y-4 sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Metode Bayar</label>
                    <select name="payment_method" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                        <option value="">Semua Metode</option>
                        <option value="cashier" {{ request('payment_method') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status Bayar</label>
                    <select name="payment_status" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                        <option value="">Semua Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Cari No. Order / Pelanggan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: #ORD-001 atau Nama Pelanggan"
                           class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 border-t border-slate-50 pt-6">
                <a href="{{ route('cashier.transactions.history') }}" class="px-8 py-3 bg-slate-100 text-slate-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Reset
                </a>
                <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pesanan</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($orders->total()) }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <p class="text-xl font-black text-emerald-600">Rp{{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Rata-rata Order</p>
            <p class="text-xl font-black text-orange-600">Rp{{ number_format($orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Lunas</p>
            <p class="text-2xl font-black text-slate-900">{{ $orders->where('payment_status', 'paid')->count() }}</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-400">
                    <tr class="text-left">
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">No. Order</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest text-right">Total</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Waktu</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-6 font-black text-slate-900">#{{ $order->order_number }}</td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-900 text-sm tracking-tight">{{ $order->customer_name }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Meja {{ $order->qrCodeRelation->meja ?? $order->qr_code ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right font-black text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest
                                    @if($order->order_status === 'completed') bg-emerald-50 text-emerald-600
                                    @elseif($order->order_status === 'processed') bg-blue-50 text-blue-600
                                    @elseif($order->order_status === 'waiting') bg-amber-50 text-amber-600
                                    @else bg-rose-50 text-rose-600 @endif">
                                    {{ $order->order_status }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest
                                    @if($order->payment_status === 'paid') bg-emerald-50 text-emerald-600
                                    @else bg-amber-50 text-amber-600 @endif">
                                    {{ $order->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-[10px] font-bold text-slate-400">
                            {{ $order->created_at->format('d/m/y H:i') }}
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('cashier.receipt', $order) }}" target="_blank"
                                   class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-widest">Tidak ada transaksi ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="p-8 bg-slate-50/50 border-t border-slate-50">
            {{ $orders->appends(request()->query())->links() }}
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

@extends('layouts.cashier')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Daftar Pesanan</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-list text-orange-600"></i>
                Kelola semua pesanan aktif dan riwayat
            </p>
        </div>
        <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Waktu Sekarang</p>
            <p class="text-sm font-black text-slate-900">{{ now()->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
        <form method="GET" action="{{ route('cashier.orders') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
            <!-- Search -->
            <div class="lg:col-span-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Cari Pesanan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Order / Pelanggan"
                       class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
            </div>

            <!-- Filter Status -->
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status Pesanan</label>
                <select name="status" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                    <option value="">Semua Status</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}> Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}> Diproses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}> Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}> Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Pembayaran -->
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status Bayar</label>
                <select name="payment" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}> Pending</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}> Lunas</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600">
                    Filter
                </button>
                <a href="{{ route('cashier.orders') }}" class="w-12 h-12 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-redo-alt text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        @foreach([
            ['waiting', 'Menunggu', 'bg-amber-50', 'text-amber-600', 'fa-clock'],
            ['processed', 'Diproses', 'bg-blue-50', 'text-blue-600', 'fa-spinner'],
            ['completed_today', 'Selesai Hari Ini', 'bg-emerald-50', 'text-emerald-600', 'fa-check-circle'],
            ['pending_payment', 'Belum Lunas', 'bg-rose-50', 'text-rose-600', 'fa-credit-card']
        ] as $stat)
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 {{ $stat[2] }} rounded-xl flex items-center justify-center border {{ str_replace('text-', 'border-', $stat[3]) }}">
                    <i class="fas {{ $stat[4] }} {{ $stat[3] }} text-xs"></i>
                </div>
                <span class="text-2xl font-black text-slate-900">{{ $stats[$stat[0]] ?? 0 }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $stat[1] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Orders Table -->
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
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    {{ $order->qrCodeRelation->meja ?? $order->table_number ?? $order->qr_code ?? '-' }}
                                </span>
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
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                                <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                                        class="w-10 h-10 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition shadow-sm shadow-emerald-600/5">
                                    <i class="fas fa-money-bill-wave text-xs"></i>
                                </button>
                                @endif
                                <a href="{{ route('cashier.receipt', $order) }}" target="_blank"
                                   class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-widest">Tidak ada pesanan ditemukan</td>
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

<!-- Modern Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md border border-slate-100 overflow-hidden transform transition-all">
        <div class="p-8 border-b border-slate-50">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight flex items-center">
                    <i class="fas fa-money-bill-wave text-emerald-500 mr-3"></i>
                    Pembayaran Tunai
                </h2>
                <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-900 transition"><i class="fas fa-times"></i></button>
            </div>
        </div>
        
        <div class="p-8">
            <div id="paymentDetail" class="mb-8 p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                <!-- Content via JS -->
            </div>
            
            <form id="paymentForm" class="space-y-6">
                @csrf
                <input type="hidden" id="orderId" name="order_id">
                
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Jumlah Dibayar</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xs uppercase tracking-widest">Rp</span>
                        <input type="number" id="amountPaid" name="amount_paid" 
                               class="w-full bg-slate-50 border-none pl-12 pr-5 py-4 rounded-2xl text-lg font-black text-slate-900 focus:ring-4 focus:ring-emerald-500/5 transition-all"
                               placeholder="0" required>
                    </div>
                </div>
                
                <div class="p-6 bg-emerald-50/50 rounded-2xl border border-emerald-100 flex justify-between items-center">
                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Kembalian</span>
                    <span class="text-xl font-black text-emerald-600" id="changeDisplay">Rp 0</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="closePaymentModal()" 
                            class="py-4 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition font-black text-[10px] uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" 
                            class="py-4 rounded-xl bg-slate-900 text-white hover:bg-emerald-600 transition font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-900/10">
                        Proses Lunas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Logic polling, sync, payment dll (Sama dengan Dashboard)
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function processPayment(orderId, totalAmount, orderNumber) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('paymentDetail').innerHTML = `
        <div class="text-center">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesanan #${orderNumber}</p>
            <p class="text-3xl font-black text-slate-900 tracking-tighter">Rp ${formatPrice(totalAmount)}</p>
        </div>
    `;
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
}
</script>
@endpush
@endsection

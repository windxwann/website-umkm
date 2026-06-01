@extends('layouts.cashier')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('cashier.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1">Pesanan #{{ $order->order_number }}</h1>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-clock text-[8px]"></i>
                    {{ $order->created_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>
        <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
           class="bg-white border border-slate-100 text-slate-600 px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center gap-2">
            <i class="fas fa-print text-orange-600"></i>
            Cetak Struk
        </a>
    </div>

    <!-- Status Alerts -->
    @if(session('success'))
        <div class="mb-8 p-5 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-slideDown">
            <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-xs font-black text-emerald-700 uppercase tracking-wide">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Items List -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center">
                        <i class="fas fa-shopping-bag text-orange-600 mr-3"></i>
                        Daftar Menu
                    </h2>
                </div>
                <div class="p-8 space-y-6">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100 shrink-0">
                                <i class="fas fa-utensils text-slate-200"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-900 text-sm tracking-tight">{{ $item->product_name }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <p class="font-black text-slate-900 text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                    
                    <div class="pt-6 border-t border-slate-50 flex justify-between items-center">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Pembayaran</span>
                        <span class="text-2xl font-black text-orange-600 tracking-tight">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes if any -->
            @if($order->notes)
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Catatan Pesanan</h2>
                <p class="text-sm font-medium text-slate-700 leading-relaxed italic bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    "{{ $order->notes }}"
                </p>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Customer & Table Card -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 space-y-6">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Pelanggan</p>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="font-black text-slate-900 text-sm">{{ $order->customer_name }}</p>
                            @if($order->customer_phone)
                                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $order->customer_phone }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Meja / Lokasi</p>
                        <div class="p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
                            <p class="font-black text-orange-600 text-sm">
                                {{ $order->qrCodeRelation->meja ?? $order->table_number ?? $order->qr_code ?? '-' }}
                            </p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                {{ $order->table_location ?? 'Makan di Tempat' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Actions Card -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 space-y-6">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Status Pesanan</p>
                        <select onchange="updateOrderStatus({{ $order->id }}, this.value)" 
                                class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all
                                @if($order->order_status == 'completed') text-emerald-600 @elseif($order->order_status == 'processed') text-blue-600 @else text-amber-600 @endif"
                                {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }}>⏳ Menunggu</option>
                            <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                            <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Pembayaran ({{ $order->payment_method }})</p>
                        @if($order->payment_status === 'paid')
                            <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl border border-emerald-100">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-lg"></i>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Lunas</p>
                                        <p class="text-[9px] font-bold opacity-80 mt-0.5">{{ $order->paid_at ? $order->paid_at->format('d/m/y H:i') : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-amber-50 text-amber-600 p-4 rounded-2xl border border-amber-100">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-clock text-lg"></i>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Pending</p>
                                        <p class="text-[9px] font-bold opacity-80 mt-0.5">Menunggu Pembayaran</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-50 space-y-3">
                        @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                        <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                                class="w-full bg-emerald-600 text-white py-4 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-600/10 hover:bg-emerald-700">
                            Terima Tunai
                        </button>
                        @endif
                        
                        @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending')
                        <button onclick="confirmPayment({{ $order->id }})" 
                                class="w-full bg-amber-500 text-white py-4 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-500/10 hover:bg-amber-600">
                            Konfirmasi Transfer
                        </button>
                        @endif
                        
                        @if($order->order_status !== 'cancelled' && $order->order_status !== 'completed')
                        <button onclick="cancelOrder({{ $order->id }})" 
                                class="w-full bg-white border border-rose-100 text-rose-500 py-4 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest hover:bg-rose-50">
                            Batalkan
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md border border-slate-100 overflow-hidden">
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
            <div id="paymentDetail" class="mb-8 p-6 bg-slate-50 rounded-[2rem] border border-slate-100 text-center">
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
                    <button type="button" onclick="closePaymentModal()" class="py-4 rounded-xl bg-slate-100 text-slate-600 font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="py-4 rounded-xl bg-slate-900 text-white font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition shadow-xl">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function getCsrf() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

function updateOrderStatus(orderId, status) {
    Swal.fire({
        title: 'Ubah Status?',
        text: 'Lanjutkan perubahan status pesanan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cashier/orders/${orderId}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            });
        } else { location.reload(); }
    });
}

let currentOrderTotal = 0;
function formatPrice(price) { return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

function processPayment(id, total, number) {
    currentOrderTotal = total;
    document.getElementById('orderId').value = id;
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').innerText = 'Rp 0';
    document.getElementById('paymentDetail').innerHTML = `
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesanan #${number}</p>
        <p class="text-3xl font-black text-slate-900 tracking-tighter">Rp ${formatPrice(total)}</p>
    `;
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');
}

document.getElementById('amountPaid').addEventListener('input', function() {
    const paid = parseFloat(this.value) || 0;
    const change = paid - currentOrderTotal;
    document.getElementById('changeDisplay').innerText = change > 0 ? 'Rp ' + formatPrice(change) : 'Rp 0';
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const orderId = document.getElementById('orderId').value;
    const amountPaid = document.getElementById('amountPaid').value;
    
    fetch(`/cashier/orders/${orderId}/process-cash-payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrf(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ amount_paid: amountPaid })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
        }
    });
});

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
}

function confirmPayment(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran?',
        text: 'Apakah Anda yakin pesanan ini sudah dibayar melalui transfer?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cashier/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            });
        }
    });
}
</script>
@endpush
@endsection

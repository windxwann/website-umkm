@extends('admin.layouts.app')

@section('title', 'Pesanan #' . $order->order_number)
@section('page-title', 'Detail Pesanan')

@section('content')
<!-- Top Actions - Compact -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" 
           class="p-2.5 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg font-black text-slate-900 tracking-tight">Order #{{ $order->order_number }}</h1>
                @php
                    $statusClasses = [
                        'waiting' => 'bg-amber-50 text-amber-600',
                        'processed' => 'bg-blue-50 text-blue-600',
                        'completed' => 'bg-emerald-50 text-emerald-600',
                        'cancelled' => 'bg-rose-50 text-rose-600'
                    ];
                @endphp
                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $statusClasses[$order->order_status] ?? 'bg-slate-50 text-slate-500' }}">
                    {{ $order->order_status }}
                </span>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
           class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-slate-900/20 hover:scale-105 transition-all">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
            Struk
        </a>
        <button onclick="exportPDF()" 
           class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-105 transition-all">
            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
            PDF
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <!-- Main Info Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items Card -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="shopping-basket" class="w-4 h-4 text-orange-600"></i>
                    Item Pesanan
                </h3>
                <span class="text-[10px] font-black text-slate-400 uppercase">{{ count($order->items ?? []) }} Menu</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Menu</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Harga</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($order->items ?? [] as $item)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-xs font-black text-slate-900 truncate">{{ $item->name ?? $item->product_name ?? $item->menu_name }}</p>
                                @if($item->notes)
                                    <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase leading-relaxed italic"><i data-lucide="message-square" class="w-2.5 h-2.5 inline mr-1"></i>{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-black text-slate-900">{{ $item->quantity }}x</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-[10px] font-bold text-slate-400 leading-none">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-xs font-black text-slate-900 leading-none">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-300 uppercase text-[10px] font-black tracking-widest">Tidak ada item</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50/30">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Belanja</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-slate-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment & Finance Bento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Summary Bill -->
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-orange-600"></i>
                    Rincian Tagihan
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-slate-400">Subtotal</span>
                        <span class="text-slate-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest text-rose-500">
                        <span>Diskon</span>
                        <span>-Rp{{ number_format($order->discount ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-slate-400">Pajak</span>
                        <span class="text-slate-900">Rp{{ number_format($order->tax ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-dashed border-gray-100 flex justify-between items-end">
                        <span class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Grand Total</span>
                        <span class="text-xl font-black text-orange-600 leading-none">Rp{{ number_format($order->grand_total ?? $order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status - Dynamic Gradient -->
            @php
                $isPaid = $order->payment_status == 'paid';
                $cardGradient = $isPaid ? 'from-emerald-600 to-teal-700' : 'from-orange-500 to-amber-600';
                $iconBg = $isPaid ? 'bg-emerald-400/20' : 'bg-orange-400/20';
            @endphp
            <div class="bg-gradient-to-br {{ $cardGradient }} rounded-[2rem] shadow-xl p-6 text-white overflow-hidden relative group">
                <i data-lucide="credit-card" class="w-24 h-24 absolute -right-6 -bottom-6 text-white/10 group-hover:scale-110 transition-transform rotate-12"></i>
                <h3 class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-6">Status Pembayaran</h3>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl {{ $iconBg }} flex items-center justify-center text-white backdrop-blur-sm border border-white/10 shadow-lg">
                            <i data-lucide="{{ $isPaid ? 'shield-check' : 'alert-circle' }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-widest leading-none">{{ $isPaid ? 'Lunas Terverifikasi' : 'Menunggu Bayar' }}</p>
                            <p class="text-[10px] font-bold text-white/70 uppercase tracking-tighter mt-1.5">Metode: {{ strtoupper($order->payment_method ?? 'Cash') }}</p>
                        </div>
                    </div>
                    
                    @if(!$isPaid && $order->order_status != 'cancelled')
                        <button onclick="confirmPayment({{ $order->id }})" 
                                class="w-full py-3 bg-white text-orange-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-50 transition-all shadow-lg active:scale-95">
                            Konfirmasi Pembayaran
                        </button>
                    @elseif($isPaid)
                        <div class="w-full py-2.5 bg-emerald-500/30 border border-white/20 rounded-xl text-center">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white">Transaksi Selesai</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Column -->
    <div class="space-y-6">
        <!-- Customer Card -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-orange-600"></i>
                Data Pelanggan
            </h3>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-black text-slate-900 truncate leading-tight">{{ $order->customer_name ?? 'Walk-in Guest' }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID Pelanggan #{{ $order->user_id ?? 'TEMP' }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="p-3 bg-slate-50 rounded-2xl flex items-center gap-3">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">{{ $order->location_description }}</span>
                </div>
                @if($order->customer_phone)
                <div class="p-3 bg-slate-50 rounded-2xl flex items-center gap-3">
                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">{{ $order->customer_phone }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Manage Status Card -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-orange-600"></i>
                Update Pesanan
            </h3>
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" id="updateStatusForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status Kerja</label>
                    <select name="order_status" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/5 transition-all cursor-pointer" 
                            {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                        <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                        <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }}>Proses</option>
                        <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Catatan Operasional</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Meja 5 minta pedas..." 
                              class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 transition-all resize-none"
                              {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}></textarea>
                </div>
                <button type="submit" 
                        class="w-full py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-105 transition-all disabled:opacity-50 disabled:hover:scale-100"
                        {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- History Timeline - Compact -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-orange-600"></i>
                Log Aktivitas
            </h3>
            <div class="space-y-4">
                @forelse($order->statusHistories ?? [] as $history)
                <div class="flex gap-3">
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-600 mt-1.5"></div>
                        <div class="w-px h-full bg-slate-100 min-h-[20px] mt-1"></div>
                    </div>
                    <div class="min-w-0 pb-2">
                        <p class="text-[10px] font-black text-slate-900 uppercase leading-none">{{ $history->status }}</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">{{ $history->created_at->format('d M, H:i') }}</p>
                        @if($history->notes)
                            <p class="text-[9px] text-slate-500 font-medium mt-1 leading-relaxed italic">"{{ $history->notes }}"</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-[9px] font-black text-slate-300 uppercase tracking-widest py-4 border-2 border-dashed border-slate-50 rounded-2xl">Belum ada log</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function exportPDF() {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">EXPORT PDF</span>',
        text: 'Menyiapkan berkas dokumen...',
        allowOutsideClick: false,
        borderRadius: '1.5rem',
        didOpen: () => {
            Swal.showLoading();
            window.open('{{ route("admin.orders.export.pdf", $order->id) }}', '_blank');
            setTimeout(() => Swal.close(), 1500);
        }
    });
}

function confirmPayment(orderId) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl text-emerald-600">KONFIRMASI BAYAR</span>',
        html: '<p class="text-gray-500 font-medium text-sm">Nyatakan pesanan ini sudah Lunas dan masuk ke sistem keuangan?</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, LUNAS',
        cancelButtonText: 'BATAL',
        padding: '2rem',
        borderRadius: '2rem'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'SUKSES', text: data.message, timer: 1500, showConfirmButton: false, borderRadius: '2rem' });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('GAGAL', data.message, 'error');
                }
            });
        }
    });
}

document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
    const statusSelect = document.querySelector('select[name="order_status"]');
    const status = statusSelect.options[statusSelect.selectedIndex].text;
    
    e.preventDefault();
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">SIMPAN PERUBAHAN?</span>',
        html: `<p class="text-gray-500 font-medium text-sm">Status kerja pesanan akan diubah menjadi <b class="text-orange-600 uppercase">${status}</b>.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, SIMPAN',
        cancelButtonText: 'BATAL',
        borderRadius: '2rem'
    }).then((result) => {
        if (result.isConfirmed) e.target.submit();
    });
});
</script>
@endpush
@endsection

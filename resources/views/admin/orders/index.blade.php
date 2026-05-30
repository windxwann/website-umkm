@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')
@section('page-title', 'Pesanan')

@section('content')
<!-- Filter & Stats Section - Compact -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <!-- Status Tabs - Compact Pill Style -->
    <div class="flex flex-wrap gap-1.5 p-1 bg-white rounded-2xl border border-gray-100 shadow-sm">
        @foreach([
            ['label' => 'Semua', 'status' => null, 'icon' => 'list'],
            ['label' => 'Menunggu', 'status' => 'waiting', 'icon' => 'clock'],
            ['label' => 'Proses', 'status' => 'processed', 'icon' => 'refresh-cw'],
            ['label' => 'Selesai', 'status' => 'completed', 'icon' => 'check-circle'],
            ['label' => 'Batal', 'status' => 'cancelled', 'icon' => 'x-circle']
        ] as $tab)
        <a href="{{ route('admin.orders.index', ['status' => $tab['status'], 'search' => request('search')]) }}" 
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-200
           {{ (request('status') == $tab['status']) ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}">
            <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    <!-- Quick Search & Date Filter - Compact -->
    <form method="GET" class="w-full lg:w-auto flex flex-wrap gap-2">
        <input type="hidden" name="status" value="{{ request('status') }}">
        
        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Cari pesanan..." 
                   value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <!-- Date Filter -->
        <div class="relative w-full sm:w-40">
            <i data-lucide="calendar" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <!-- Reset Button - Only shows when filtering -->
        @if(request('search') || request('date'))
        <a href="{{ route('admin.orders.index', ['status' => request('status')]) }}" 
           class="flex items-center justify-center p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all shrink-0 shadow-sm group"
           title="Hapus Filter">
            <i data-lucide="rotate-ccw" class="w-4 h-4 group-hover:rotate-[-45deg] transition-transform"></i>
        </a>
        @endif
        
        <!-- Submit via Enter is enough, but adding a small subtle button for mobile UX -->
        <button type="submit" class="hidden"></button>
    </form>
</div>

<!-- Orders Table - Compact Design -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Pesanan</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-right">Total</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Metode</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Status Kerja</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Payment</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-white transition-all shrink-0">
                                <i data-lucide="receipt" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-900 tracking-tight leading-none">{{ $order->order_number }}</p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest">{{ $order->location_description }}</span>
                                    <span class="w-0.5 h-0.5 bg-slate-300 rounded-full"></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate max-w-[100px]">{{ $order->customer_name ?? 'Guest' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="text-sm font-black text-slate-900 leading-none">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest
                            {{ $order->payment_method == 'cashier' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                            <i data-lucide="{{ $order->payment_method == 'cashier' ? 'banknote' : 'credit-card' }}" class="w-2.5 h-2.5 mr-1.5"></i>
                            {{ $order->payment_method }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <select onchange="updateOrderStatus({{ $order->id }}, this.value)" 
                                class="bg-white border border-slate-100 pl-2 pr-6 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest focus:outline-none focus:ring-4 focus:ring-orange-500/5 cursor-pointer transition-all
                                @if($order->order_status == 'waiting') text-amber-500 @elseif($order->order_status == 'processed') text-blue-500 @elseif($order->order_status == 'completed') text-emerald-500 @else text-rose-500 @endif"
                                {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }}>Proses</option>
                            <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </td>
                    <td class="px-6 py-4">
                        @if($order->payment_status == 'paid')
                            <div class="flex items-center text-emerald-600 gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">Lunas</span>
                            </div>
                        @elseif($order->order_status == 'cancelled')
                            <div class="flex items-center text-rose-400 gap-1.5 opacity-60">
                                <i data-lucide="minus-circle" class="w-3.5 h-3.5"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">Batal</span>
                            </div>
                        @else
                            <button onclick="confirmPayment({{ $order->id }})"
                                    class="px-3 py-1.5 bg-amber-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-md shadow-amber-500/10 hover:scale-105 transition-all">
                                Konfirmasi
                            </button>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="p-2 bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200 transition-colors shadow-sm" 
                               title="Detail">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                               class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors shadow-sm" 
                               title="Struk">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
                            <i data-lucide="shopping-bag" class="w-8 h-8 text-slate-200"></i>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px]">Belum Ada Pesanan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination - Compact -->
    @if($orders->hasPages())
    <div class="px-6 py-4 bg-slate-50/30 border-t border-gray-50 text-[10px]">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateOrderStatus(orderId, status) {
    let statusText = '';
    let statusIcon = '';
    switch(status) {
        case 'waiting': statusText = 'Menunggu'; statusIcon = '⏳'; break;
        case 'processed': statusText = 'Diproses'; statusIcon = '⚙️'; break;
        case 'completed': statusText = 'Selesai'; statusIcon = '✅'; break;
        case 'cancelled': statusText = 'Dibatalkan'; statusIcon = '❌'; break;
    }
    
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">Konfirmasi</span>',
        html: `<p class="text-gray-500 font-medium text-sm">Ubah status pesanan menjadi <b class="text-orange-600">${statusIcon} ${statusText}</b>?</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'YA, UBAH',
        cancelButtonText: 'BATAL',
        padding: '1.5rem',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        borderRadius: '1.5rem'
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('GAGAL', data.message, 'error');
                }
            })
        } else {
            location.reload();
        }
    });
}

function confirmPayment(orderId) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">Pembayaran</span>',
        html: '<p class="text-gray-500 font-medium text-sm">Konfirmasi pembayaran sudah diterima?</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'YA, LUNAS',
        cancelButtonText: 'BELUM',
        padding: '1.5rem',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SUKSES',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        borderRadius: '1.5rem'
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('GAGAL', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush
@endsection

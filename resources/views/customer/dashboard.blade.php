@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Dashboard</h1>
            <div class="flex items-center gap-4">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">
                    <i class="fas fa-qrcode mr-1 text-orange-500"></i>
                    Meja: <span class="font-bold text-slate-800">{{ session('qr_code', 'Tidak diketahui') }}</span>
                </p>
                <button onclick="finishVisit()" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition flex items-center gap-2 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100">
                    <i class="fas fa-sign-out-alt"></i>
                    Selesaikan Kunjungan
                </button>
            </div>
        </div>
        
        <!-- Status Connection Badge -->
        <div id="connection-status" class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest">
            <i class="fas fa-wifi"></i>
            <span>Terhubung</span>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pesanan</p>
                <p class="text-4xl font-black text-slate-900" id="total-orders-count">{{ $orders->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center">
                <i class="fas fa-shopping-cart text-orange-600"></i>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesanan Aktif</p>
                <p class="text-4xl font-black text-slate-900" id="active-orders-count">
                    {{ $orders->whereIn('order_status', ['waiting', 'processed'])->count() }}
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center">
                <i class="fas fa-spinner text-blue-600"></i>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Belanja</p>
                <p class="text-4xl font-black text-slate-900" id="total-spending">
                    Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-emerald-600"></i>
            </div>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center">
                <i class="fas fa-history text-orange-600 mr-3"></i>
                Riwayat Pesanan
                <span id="last-update-badge" class="ml-3 text-[10px] text-slate-400 font-medium"></span>
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('menu') }}" class="bg-slate-900 text-white px-6 py-3 rounded-xl hover:bg-orange-600 transition font-black text-[10px] uppercase tracking-widest">
                    Pesan Lagi
                </a>
                <button onclick="refreshData()" class="bg-slate-100 text-slate-600 px-6 py-3 rounded-xl hover:bg-slate-200 transition font-black text-[10px] uppercase tracking-widest" id="refresh-btn">
                    <i class="fas fa-sync-alt mr-2" id="refresh-icon"></i>
                    Refresh
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[10px] md:text-sm">
                <thead class="bg-slate-50 text-slate-400">
                    <tr class="text-left">
                        <th class="px-3 md:px-8 py-4 font-black uppercase tracking-widest whitespace-nowrap">Order</th>
                        <th class="px-3 md:px-8 py-4 font-black uppercase tracking-widest whitespace-nowrap">Tanggal</th>
                        <th class="px-3 md:px-8 py-4 font-black uppercase tracking-widest text-right whitespace-nowrap">Total</th>
                        <th class="px-3 md:px-4 py-4 font-black uppercase tracking-widest text-center">Status</th>
                        <th class="px-3 md:px-8 py-4 font-black uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="orders-tbody">
                    @forelse($orders ?? [] as $order)
                    <tr class="hover:bg-slate-50 transition" id="order-row-{{ $order->order_number }}">
                        <td class="px-3 md:px-8 py-4 font-black text-slate-900 whitespace-nowrap">#{{ substr($order->order_number, -4) }}</td>
                        <td class="px-3 md:px-8 py-4 font-medium text-slate-600 whitespace-nowrap">{{ $order->created_at->format('d M, H:i') }}</td>
                        <td class="px-3 md:px-8 py-4 font-black text-orange-600 text-right whitespace-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-3 md:px-4 py-4 text-center">
                            <span class="status-badge-{{ $order->order_number }} px-2 py-1 rounded-full text-[8px] md:text-[10px] font-black uppercase tracking-widest
                                @if($order->order_status === 'waiting') bg-amber-50 text-amber-600
                                @elseif($order->order_status === 'processed') bg-blue-50 text-blue-600
                                @elseif($order->order_status === 'completed') bg-emerald-50 text-emerald-600
                                @else bg-rose-50 text-rose-600
                                @endif">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        <td class="px-3 md:px-8 py-4 text-center">
                            <a href="{{ route('customer.track-order', $order->order_number) }}" class="text-slate-400 hover:text-orange-600 transition">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-16 text-center text-slate-400 font-black uppercase tracking-widest text-[10px]">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .transition { transition: all 0.2s ease; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Logic polling, sync, dll tetap sama. Menggunakan fungsi yang ada di file aslinya.
// Inisialisasi dari data server
let lastKnownStatus = {};
@foreach($orders ?? [] as $order)
lastKnownStatus['{{ $order->order_number }}'] = {
    status: '{{ $order->order_status }}',
    payment: '{{ $order->payment_status }}',
    total: {{ $order->total_amount }}
};
@endforeach

// Update waktu saat ini setiap detik
function updateCurrentTime() {
    const now = new Date();
    const formatted = now.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) timeElement.textContent = formatted;
}
setInterval(updateCurrentTime, 1000);
updateCurrentTime();

// Update last update badge
function updateLastUpdateBadge() {
    const badge = document.getElementById('last-update-badge');
    if (badge) {
        const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        badge.innerHTML = `<i class="fas fa-clock mr-1"></i>Update: ${time}`;
    }
}

// Update cards (realtime)
function updateCards() {
    const orderRows = document.querySelectorAll('#orders-tbody tr');
    let totalOrders = orderRows.length;
    let activeOrders = 0;
    let totalSpending = 0;
    
    orderRows.forEach(row => {
        const statusSpan = row.querySelector('[class*="status-badge-"]');
        const totalSpan = row.querySelector('td:nth-child(3)');
        
        if (statusSpan) {
            const statusText = statusSpan.textContent;
            if (statusText.includes('waiting') || statusText.includes('processed')) {
                activeOrders++;
            }
        }
        
        if (totalSpan) {
            const totalText = totalSpan.textContent;
            const totalMatch = totalText.match(/Rp ([\d.]+)/);
            if (totalMatch) {
                const total = parseInt(totalMatch[1].replace(/\./g, ''));
                totalSpending += total;
            }
        }
    });
    
    const totalOrdersEl = document.getElementById('total-orders-count');
    const activeOrdersEl = document.getElementById('active-orders-count');
    const totalSpendingEl = document.getElementById('total-spending');
    
    if (totalOrdersEl) totalOrdersEl.textContent = totalOrders;
    if (activeOrdersEl) activeOrdersEl.textContent = activeOrders;
    if (totalSpendingEl) totalSpendingEl.textContent = 'Rp ' + formatPrice(totalSpending);
}

// Play notification sound
function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        oscillator.frequency.value = 880;
        gainNode.gain.value = 0.2;
        oscillator.start();
        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.5);
        oscillator.stop(audioContext.currentTime + 0.5);
        audioContext.resume();
    } catch(e) {
        console.debug('Sound not supported');
    }
}

// Update UI untuk status order
function updateOrderUI(orderNumber, newStatus) {
    const desktopStatusSpan = document.querySelector(`#order-row-${orderNumber} [class*="status-badge-"]`);
    if (desktopStatusSpan) {
        const colors = {
            'waiting': 'bg-amber-50 text-amber-600',
            'processed': 'bg-blue-50 text-blue-600',
            'completed': 'bg-emerald-50 text-emerald-600',
            'cancelled': 'bg-rose-50 text-rose-600'
        };
        desktopStatusSpan.className = `status-badge-${orderNumber} px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${colors[newStatus] || colors['cancelled']}`;
        desktopStatusSpan.textContent = newStatus;
    }
    updateCards();
}

// Finish Visit
function finishVisit() {
    Swal.fire({
        title: 'Selesaikan Kunjungan?',
        text: 'Anda akan keluar dari sesi meja ini. Pastikan semua pesanan sudah dibayar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e11d48', // rose-600
        cancelButtonColor: '#64748b', // slate-500
        confirmButtonText: 'Ya, Selesai',
        cancelButtonText: 'Batal',
        customClass: {
            container: 'font-sans',
            popup: 'rounded-[2rem]'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("customer.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('restaurant_cart');
                    window.location.href = '{{ route("scan.qr") }}';
                }
            });
        }
    });
}

// Refresh data
let isRefreshing = false;
function refreshData() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    const refreshBtn = document.getElementById('refresh-btn');
    const refreshIcon = document.getElementById('refresh-icon');
    
    if (refreshIcon) refreshIcon.className = 'fas fa-sync-alt fa-spin';
    if (refreshBtn) refreshBtn.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Polling
function checkOrderStatus() {
    // 1. Cek session validity
    fetch('{{ route("customer.checkSession") }}', {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.valid) {
            window.location.href = '{{ route("scan.qr") }}';
            return;
        }
    });

    // 2. Cek order status
    fetch('{{ route("customer.check-new-completed") }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        }
    })
    .then(res => {
        if (res.status === 401 || res.status === 403) {
            updateConnectionStatus(false);
            showToast('Sesi Berakhir', 'Silakan scan ulang QR Code', 'warning', 5000);
            setTimeout(() => location.reload(), 5000);
            return;
        }
        updateConnectionStatus(true);
        return res.json();
    })
    .then(data => {
        if (!data || !data.success) return;
        
        lastUpdateTime = new Date();
        updateLastUpdateBadge();
        
        // Handle completed orders
        if (data.completed_orders && data.completed_orders.length > 0) {
            data.completed_orders.forEach(order => {
                const prev = lastKnownStatus[order.order_number];
                if (prev && prev.status !== 'completed') {
                    lastKnownStatus[order.order_number] = {
                        status: 'completed',
                        payment: order.payment_status,
                        total: prev.total
                    };
                    
                    updateOrderUI(order.order_number, 'completed', order.payment_status);
                    playNotificationSound();
                    
                    Swal.fire({
                        title: ' Pesanan Selesai!',
                        html: `
                            <div class="text-left">
                                <div class="mb-4 p-4 bg-orange-50 rounded-lg">
                                    <p class="text-lg font-semibold text-orange-600 mb-2">Pesanan #${order.order_number}</p>
                                    <p class="text-sm text-gray-700">Pesanan Anda telah selesai diproses!</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status Pembayaran:</span>
                                        <span class="font-semibold ${order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'}">
                                            ${order.payment_status === 'paid' ? ' LUNAS' : ' PENDING'}
                                        </span>
                                    </div>
                                    ${order.payment_status !== 'paid' ? 
                                        '<div class="mt-3 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800"> Silakan selesaikan pembayaran terlebih dahulu</div>' : 
                                        '<div class="mt-3 p-3 bg-green-50 rounded-lg text-sm text-green-800"> Terima kasih! Silakan ambil pesanan Anda</div>'
                                    }
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#f97316',
                        confirmButtonText: order.payment_status === 'paid' ? 'Ambil Pesanan' : 'Bayar Sekarang',
                        showCancelButton: order.payment_status !== 'paid',
                        cancelButtonText: 'Nanti Saja',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed && order.payment_status !== 'paid') {
                            window.location.href = '{{ url("order/payment") }}/' + order.order_number;
                        }
                    });
                }
            });
        }
    })
    .catch(err => {
        console.debug('[Polling] Error:', err);
        updateConnectionStatus(false);
    });
}
setInterval(checkOrderStatus, 5000);

function updateConnectionStatus(isConnected) {
    const statusBadge = document.getElementById('connection-status');
    if (!statusBadge) return;
    
    if (isConnected) {
        statusBadge.className = 'flex items-center gap-2 px-4 py-2 rounded-2xl bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest';
        statusBadge.innerHTML = '<i class="fas fa-wifi"></i> <span>Terhubung</span>';
    } else {
        statusBadge.className = 'flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest';
        statusBadge.innerHTML = '<i class="fas fa-wifi-slash"></i> <span>Terputus</span>';
    }
}
</script>
@endpush
@endsection
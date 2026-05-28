@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-orange-600 mb-2">Dashboard Pembeli</h1>
            <p class="text-gray-600 text-sm md:text-base">
                <i class="fas fa-qrcode mr-2 text-orange-500"></i>
                Meja: <span class="font-semibold">{{ session('qr_code', 'Tidak diketahui') }}</span>
                <i class="fas fa-clock ml-4 mr-2 text-orange-500"></i>
                <span id="current-time">{{ now()->format('d F Y H:i:s') }}</span>
            </p>
        </div>
        
        <!-- Status Connection Badge -->
        <div class="mt-3 md:mt-0">
            <div id="connection-status" class="flex items-center space-x-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">
                <i class="fas fa-wifi"></i>
                <span>Terhubung</span>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    @if(isset($orders) && count($orders) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Pesanan</p>
                    <p class="text-2xl font-bold" id="total-orders-count">{{ $orders->count() }}</p>
                </div>
                <i class="fas fa-shopping-cart text-3xl opacity-80"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Pesanan Aktif</p>
                    <p class="text-2xl font-bold" id="active-orders-count">
                        {{ $orders->whereIn('order_status', ['waiting', 'processed'])->count() }}
                    </p>
                </div>
                <i class="fas fa-spinner text-3xl opacity-80"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Belanja</p>
                    <p class="text-lg md:text-2xl font-bold" id="total-spending">
                        Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                    </p>
                </div>
                <i class="fas fa-money-bill-wave text-3xl opacity-80"></i>
            </div>
        </div>
    </div>
    @endif

    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-4 md:p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center">
            <h2 class="text-lg font-semibold flex items-center mb-3 md:mb-0">
                <i class="fas fa-history text-orange-600 mr-2"></i>
                Pesanan Terbaru
                <span id="last-update-badge" class="ml-3 text-xs text-gray-500 font-normal"></span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('menu') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center shadow-md text-sm">
                    <i class="fas fa-plus mr-2"></i>Pesan Lagi
                </a>
                <button onclick="refreshData()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition" id="refresh-btn">
                    <i class="fas fa-sync-alt" id="refresh-icon"></i>
                    <span id="refresh-text" class="ml-1 hidden md:inline">Refresh</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50 whitespace-nowrap">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="orders-tbody">
                        @forelse($orders ?? [] as $order)
                        <tr class="hover:bg-gray-50 transition" id="order-row-{{ $order->order_number }}" data-order-number="{{ $order->order_number }}">
                            <td class="px-6 py-4 font-mono text-sm font-semibold">#{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 font-semibold text-orange-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge-{{ $order->order_number }} 
                                    @if($order->order_status === 'waiting') status-waiting
                                    @elseif($order->order_status === 'processed') status-processed
                                    @elseif($order->order_status === 'completed') status-completed
                                    @else status-cancelled
                                    @endif">
                                    @if($order->order_status === 'waiting')
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    @elseif($order->order_status === 'processed')
                                        <i class="fas fa-cog mr-1"></i> Diproses
                                    @elseif($order->order_status === 'completed')
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    @else
                                        <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->payment_status === 'paid')
                                    <span class="payment-paid">
                                        <i class="fas fa-check-circle mr-1"></i> Lunas
                                    </span>
                                @else
                                    <span class="payment-pending">
                                        <i class="fas fa-hourglass-half mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <a href="{{ route('customer.track-order', $order->order_number) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition inline-block" 
                                       title="Lacak Pesanan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->payment_status !== 'paid' && $order->order_status !== 'cancelled')
                                    <a href="{{ route('order.payment', $order->order_number) }}" 
                                       class="text-orange-600 hover:text-orange-800 transition inline-block" 
                                       title="Bayar Sekarang">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                                <p class="text-lg mb-3">Belum ada pesanan</p>
                                <a href="{{ route('menu') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition inline-flex items-center shadow-md">
                                    <i class="fas fa-utensils mr-2"></i>Mulai Belanja
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($orders ?? [] as $order)
                <div class="p-4 hover:bg-gray-50 transition" id="mobile-order-{{ $order->order_number }}" data-order-number="{{ $order->order_number }}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-mono font-bold text-orange-600 text-sm">#{{ $order->order_number }}</div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm text-gray-600">Total:</div>
                        <div class="font-semibold text-orange-600 text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="status-badge-{{ $order->order_number }} 
                            @if($order->order_status === 'waiting') status-waiting
                            @elseif($order->order_status === 'processed') status-processed
                            @elseif($order->order_status === 'completed') status-completed
                            @else status-cancelled
                            @endif">
                            @if($order->order_status === 'waiting')
                                <i class="fas fa-clock mr-1"></i> Menunggu
                            @elseif($order->order_status === 'processed')
                                <i class="fas fa-cog mr-1"></i> Diproses
                            @elseif($order->order_status === 'completed')
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            @else
                                <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                            @endif
                        </span>
                        <div class="flex space-x-3">
                            <a href="{{ route('customer.track-order', $order->order_number) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->payment_status !== 'paid' && $order->order_status !== 'cancelled')
                            <a href="{{ route('order.payment', $order->order_number) }}" class="text-orange-600 hover:text-orange-800">
                                <i class="fas fa-credit-card"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($order->payment_status === 'paid')
                        <span class="text-xs payment-paid inline-block">
                            <i class="fas fa-check-circle mr-1"></i> Lunas
                        </span>
                        @else
                        <span class="text-xs payment-pending inline-block">
                            <i class="fas fa-hourglass-half mr-1"></i> Pending
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg">Belum ada pesanan</p>
                    <a href="{{ route('menu') }}" class="inline-block mt-3 bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition text-sm">
                        <i class="fas fa-utensils mr-2"></i>Mulai Belanja
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination removed because orders is a Collection, not Paginator -->
    </div>
</div>

@push('styles')
<style>
    /* Status Badge Styles */
    .status-waiting {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background-color: #fef3c7;
        color: #92400e;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-processed {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background-color: #dbeafe;
        color: #1e40af;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-completed {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background-color: #d1fae5;
        color: #065f46;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-cancelled {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background-color: #fee2e2;
        color: #991b1b;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Payment Badge Styles */
    .payment-paid {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        background-color: #d1fae5;
        color: #065f46;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .payment-pending {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        background-color: #fef3c7;
        color: #92400e;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Smooth transitions */
    .transition {
        transition: all 0.2s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ============================================
// SISTEM NOTIFIKASI REAL-TIME
// ============================================

// State management
let lastKnownStatus = {};
let pollingInterval = null;
let isRefreshing = false;
let lastUpdateTime = new Date();

// Inisialisasi dari data server
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
        const time = lastUpdateTime.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        badge.innerHTML = `<i class="fas fa-clock mr-1"></i>Update: ${time}`;
    }
}

// Update cards (realtime)
function updateCards() {
    // Hitung ulang semua data dari DOM
    const orderRows = document.querySelectorAll('#orders-tbody tr');
    let totalOrders = orderRows.length;
    let activeOrders = 0;
    let totalSpending = 0;
    
    orderRows.forEach(row => {
        const statusSpan = row.querySelector('[class*="status-"]');
        const totalSpan = row.querySelector('td:nth-child(3)');
        
        if (statusSpan) {
            const statusText = statusSpan.textContent;
            if (statusText.includes('Menunggu') || statusText.includes('Diproses')) {
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
    
    // Update card values
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

// Show toast notification
function showToast(title, message, icon = 'info', duration = 4000) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: duration,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: icon,
        title: title,
        text: message
    });
}

// Update UI untuk status order
function updateOrderUI(orderNumber, newStatus, newPaymentStatus = null) {
    // Update desktop view
    const desktopStatusSpan = document.querySelector(`#order-row-${orderNumber} [class*="status-badge-"]`);
    if (desktopStatusSpan) {
        let statusClass = '';
        let statusIcon = '';
        let statusText = '';
        
        switch(newStatus) {
            case 'waiting':
                statusClass = 'status-waiting';
                statusIcon = '<i class="fas fa-clock mr-1"></i>';
                statusText = 'Menunggu';
                break;
            case 'processed':
                statusClass = 'status-processed';
                statusIcon = '<i class="fas fa-cog mr-1"></i>';
                statusText = 'Diproses';
                break;
            case 'completed':
                statusClass = 'status-completed';
                statusIcon = '<i class="fas fa-check-circle mr-1"></i>';
                statusText = 'Selesai';
                break;
            default:
                statusClass = 'status-cancelled';
                statusIcon = '<i class="fas fa-times-circle mr-1"></i>';
                statusText = 'Dibatalkan';
        }
        
        desktopStatusSpan.className = `status-badge-${orderNumber} ${statusClass}`;
        desktopStatusSpan.innerHTML = `${statusIcon} ${statusText}`;
    }
    
    // Update mobile view
    const mobileStatusSpan = document.querySelector(`#mobile-order-${orderNumber} [class*="status-badge-"]`);
    if (mobileStatusSpan) {
        let statusClass = '';
        let statusIcon = '';
        let statusText = '';
        
        switch(newStatus) {
            case 'waiting':
                statusClass = 'status-waiting';
                statusIcon = '<i class="fas fa-clock mr-1"></i>';
                statusText = 'Menunggu';
                break;
            case 'processed':
                statusClass = 'status-processed';
                statusIcon = '<i class="fas fa-cog mr-1"></i>';
                statusText = 'Diproses';
                break;
            case 'completed':
                statusClass = 'status-completed';
                statusIcon = '<i class="fas fa-check-circle mr-1"></i>';
                statusText = 'Selesai';
                break;
            default:
                statusClass = 'status-cancelled';
                statusIcon = '<i class="fas fa-times-circle mr-1"></i>';
                statusText = 'Dibatalkan';
        }
        
        mobileStatusSpan.className = `status-badge-${orderNumber} ${statusClass}`;
        mobileStatusSpan.innerHTML = `${statusIcon} ${statusText}`;
    }
    
    // Update cards setelah perubahan
    updateCards();
}

// Remove order from UI
function removeOrderFromUI(orderNumber) {
    const desktopRow = document.getElementById(`order-row-${orderNumber}`);
    const mobileCard = document.getElementById(`mobile-order-${orderNumber}`);
    
    if (desktopRow) {
        desktopRow.remove();
    }
    
    if (mobileCard) {
        mobileCard.remove();
    }
    
    // Update cards setelah penghapusan
    updateCards();
    
    setTimeout(() => {
        const tbody = document.getElementById('orders-tbody');
        const mobileContainer = document.querySelector('.md\\:hidden');
        if ((tbody && tbody.children.length === 0) || (mobileContainer && mobileContainer.children.length === 1 && mobileContainer.children[0].classList.contains('p-8'))) {
            setTimeout(() => location.reload(), 500);
        }
    }, 500);
}

// Update connection status
function updateConnectionStatus(isConnected) {
    const statusDiv = document.getElementById('connection-status');
    if (statusDiv) {
        if (isConnected) {
            statusDiv.className = 'flex items-center space-x-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm';
            statusDiv.innerHTML = '<i class="fas fa-wifi"></i><span>Terhubung</span>';
        } else {
            statusDiv.className = 'flex items-center space-x-2 px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm';
            statusDiv.innerHTML = '<i class="fas fa-wifi-slash"></i><span>Terputus</span>';
        }
    }
}

// Refresh data
function refreshData() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    const refreshBtn = document.getElementById('refresh-btn');
    const refreshIcon = document.getElementById('refresh-icon');
    const refreshText = document.getElementById('refresh-text');
    
    if (refreshIcon) refreshIcon.className = 'fas fa-sync-alt fa-spin';
    if (refreshText) refreshText.textContent = 'Memuat...';
    if (refreshBtn) refreshBtn.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Main polling function
function checkOrderStatus() {
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
                        title: '🎉 Pesanan Selesai!',
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
                                            ${order.payment_status === 'paid' ? '✅ LUNAS' : '⏳ PENDING'}
                                        </span>
                                    </div>
                                    ${order.payment_status !== 'paid' ? 
                                        '<div class="mt-3 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">⚠️ Silakan selesaikan pembayaran terlebih dahulu</div>' : 
                                        '<div class="mt-3 p-3 bg-green-50 rounded-lg text-sm text-green-800">✅ Terima kasih! Silakan ambil pesanan Anda</div>'
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
                    
                    setTimeout(() => {
                        removeOrderFromUI(order.order_number);
                    }, 10000);
                }
            });
        }
        
        // Handle processed orders
        if (data.processed_orders && data.processed_orders.length > 0) {
            data.processed_orders.forEach(order => {
                const prev = lastKnownStatus[order.order_number];
                if (prev && prev.status === 'waiting') {
                    lastKnownStatus[order.order_number] = {
                        status: 'processed',
                        payment: order.payment_status,
                        total: prev.total
                    };
                    
                    updateOrderUI(order.order_number, 'processed');
                    playNotificationSound();
                    showToast('Pesanan Diproses', `Pesanan #${order.order_number} sedang dimasak`, 'info', 4000);
                }
            });
        }
    })
    .catch(err => {
        console.debug('[Polling] Error:', err);
        updateConnectionStatus(false);
    });
}

// Start polling
setTimeout(() => {
    checkOrderStatus();
    pollingInterval = setInterval(checkOrderStatus, 5000);
}, 1000);

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});

// Initial cards update
setTimeout(updateCards, 100);

console.log('✅ Dashboard pembeli siap dengan polling real-time');

// ============================================
// SINKRONISASI CART DENGAN LAYOUT
// ============================================

// Update cart count dari localStorage saat load
function syncCartCount() {
    try {
        const cartData = localStorage.getItem('restaurant_cart');
        if (cartData) {
            const cart = JSON.parse(cartData);
            const total = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (typeof window.globalUpdateCartCount === 'function') {
                window.globalUpdateCartCount(total);
            }
        }
    } catch(e) {
        console.debug('Error syncing cart:', e);
    }
}

// Jalankan sync cart saat load
document.addEventListener('DOMContentLoaded', function() {
    syncCartCount();
});

// Listen untuk perubahan cart dari halaman lain
window.addEventListener('storage', function(e) {
    if (e.key === 'restaurant_cart') {
        syncCartCount();
    }
});
</script>
@endpush
@endsection
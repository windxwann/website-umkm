@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-orange-600 mb-2">Dashboard Pembeli</h1>
            <p class="text-gray-600">
                <i class="fas fa-qrcode mr-2 text-orange-500"></i>
                Meja: <span class="font-semibold">{{ session('qr_code', 'Tidak diketahui') }}</span>
                <i class="fas fa-clock ml-4 mr-2 text-orange-500"></i>
                {{ now()->format('d F Y H:i') }}
            </p>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center">
            <h2 class="text-lg font-semibold flex items-center mb-3 md:mb-0">
                <i class="fas fa-history text-orange-600 mr-2"></i>
                Pesanan Terbaru
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('menu') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center">
                    <i class="fas fa-plus mr-2"></i>Pesan Lagi
                </a>
                <button onclick="refreshData()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y whitespace-nowrap">
                    @forelse($orders ?? [] as $order)
                    <tr class="hover:bg-gray-50 transition" id="order-row-{{ $order->order_number }}">
                        <td class="px-6 py-4 font-mono text-sm">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-semibold text-orange-600">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge-{{ $order->order_number }} 
                                @if($order->order_status === 'waiting') px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium
                                @elseif($order->order_status === 'processed') px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium
                                @elseif($order->order_status === 'completed') px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium
                                @else px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium
                                @endif">
                                @if($order->order_status === 'waiting')
                                    ⏳ Menunggu
                                @elseif($order->order_status === 'processed')
                                    ⚙️ Diproses
                                @elseif($order->order_status === 'completed')
                                    ✅ Selesai
                                @else
                                    ❌ Dibatalkan
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('customer.track-order', $order->order_number) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="Lacak Pesanan">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->payment_status !== 'paid' && $order->order_status !== 'cancelled')
                                <a href="{{ route('order.payment', $order->order_number) }}" 
                                   class="text-orange-600 hover:text-orange-800" title="Bayar Sekarang">
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
                            <a href="{{ route('menu') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition inline-flex items-center">
                                <i class="fas fa-utensils mr-2"></i>Mulai Belanja
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($orders) && method_exists($orders, 'links'))
        <div class="px-6 py-4 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ============================================
// SISTEM NOTIFIKASI BERBASIS POLLING
// Cek status pesanan setiap 10 detik
// ============================================

// Simpan status terkini per order_number (di memory, bukan localStorage)
// Sehingga setiap buka halaman, notif bisa muncul lagi jika order masih baru selesai
let lastKnownStatus = {};

// Inisialisasi dari data server (status saat halaman dimuat)
@foreach($orders ?? [] as $order)
lastKnownStatus['{{ $order->order_number }}'] = '{{ $order->order_status }}';
@endforeach

// ============================================
// FUNGSI UPDATE STATUS DI TABEL TANPA RELOAD
// ============================================
function updateOrderStatusInTable(orderNumber, status) {
    const row = document.getElementById(`order-row-${orderNumber}`);
    const statusSpan = document.querySelector(`.status-badge-${orderNumber}`);
    
    if (status === 'completed' || status === 'cancelled') {
        // Jika sudah selesai/batal, hapus baris dari tabel (sesuai permintaan user)
        if (row) {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => {
                row.remove();
                // Jika tabel kosong, tunjukkan pesan "Belum ada pesanan"
                const tbody = document.querySelector('tbody');
                if (tbody && tbody.children.length === 0) {
                    location.reload(); // Reload untuk memunculkan state kosong yang benar
                }
            }, 500);
        }
        return;
    }

    if (statusSpan) {
        if (status === 'processed') {
            statusSpan.innerHTML = '\u2699\uFE0F Diproses';
            statusSpan.className = 'px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium';
        }
    }
}

// ============================================
// FUNGSI PLAY SUARA NOTIFIKASI
// ============================================
function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        oscillator.frequency.value = 880;
        gainNode.gain.value = 0.3;
        oscillator.start();
        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 1);
        oscillator.stop(audioContext.currentTime + 0.5);
    } catch(e) {}
}

// ============================================
// FUNGSI REFRESH DATA
// ============================================
function refreshData() {
    Swal.fire({
        title: 'Memuat ulang...',
        text: 'Mengambil data terbaru',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); },
        timer: 1000,
        showConfirmButton: false
    });
    setTimeout(() => { location.reload(); }, 1000);
}

// ============================================
// FUNGSI UTAMA: POLLING STATUS PESANAN
// ============================================
function checkOrderStatus() {
    fetch('{{ route("customer.check-new-completed") }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 401 || res.status === 403) {
            // Sesi mungkin sudah direset oleh kasir
            location.reload();
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data || !data.success) return;

        // --- CEK ORDER COMPLETED ---
        if (data.completed_orders && data.completed_orders.length > 0) {
            data.completed_orders.forEach(order => {
                const prev = lastKnownStatus[order.order_number];
                if (prev !== 'completed') {
                    lastKnownStatus[order.order_number] = 'completed';
                    updateOrderStatusInTable(order.order_number, 'completed');
                    playNotificationSound();
                    Swal.fire({
                        title: '🎉 Pesanan Selesai!',
                        html: `<div class="text-left"><p class="mb-2 text-lg">Pesanan <b>#${order.order_number}</b> telah selesai!</p><p class="text-sm text-gray-600">Status Pembayaran: <span class="${order.payment_status === 'paid' ? 'text-green-600 font-bold' : 'text-yellow-600 font-bold'}">${order.payment_status === 'paid' ? 'LUNAS' : 'PENDING'}</span></p></div>`,
                        icon: 'success',
                        confirmButtonColor: '#f97316',
                        confirmButtonText: 'Terima Kasih!',
                        timer: 8000,
                        timerProgressBar: true
                    });
                }
            });
        }

        // --- CEK PEMBAYARAN LUNAS (Untuk order yang masih diproses/waiting) ---
        // Kita juga cek processed_orders untuk melihat jika status pembayaran berubah jadi paid
        if (data.processed_orders && data.processed_orders.length > 0) {
            data.processed_orders.forEach(order => {
                const prev = lastKnownStatus[order.order_number];
                
                // Notif jika status berubah jadi processed
                if (prev === 'waiting') {
                    lastKnownStatus[order.order_number] = 'processed';
                    updateOrderStatusInTable(order.order_number, 'processed');
                    Swal.fire({
                        title: '📦 Diproses',
                        text: `Pesanan #${order.order_number} sedang dimasak.`,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
                
                // Tambahan: Cek jika pembayaran lunas (khusus jika sebelumnya belum lunas di memory kita)
                // (Optional jika tracking payment status dibutuhkan di memory)
            });
        }
    })
    .catch(err => console.debug('[Polling] Error:', err));
}

// Jalankan pertama kali setelah 1 detik
setTimeout(checkOrderStatus, 1000);

// Polling setiap 3 detik (Sangat cepat untuk meminimalkan delay)
setInterval(checkOrderStatus, 3000);

console.log('\u2705 Order polling aktif - cek setiap 10 detik');
</script>
@endpush
@endsection
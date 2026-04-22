@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Dashboard Kasir</h1>
                <p class="text-orange-100">
                    <i class="fas fa-user mr-2"></i>{{ auth()->user()->name }} 
                    <i class="fas fa-calendar ml-4 mr-2"></i>{{ now()->format('d F Y') }}
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-cash-register text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Warna disamakan dengan dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- Pending Payment -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Payment</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_payments'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pesanan Baru -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pesanan Baru</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['waiting_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Diproses -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Diproses</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['processed_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Selesai Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Selesai Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pendapatan Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pendapatan Hari Ini</p>
                    <p class="text-xl font-bold text-orange-600">
                        Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-orange-100 w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MANAJEMEN MEJA - RESET TABLE -->
    <!-- ============================================ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 border-b bg-gradient-to-r from-gray-50 to-white">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chair text-orange-600 mr-2"></i>
                    Manajemen Meja
                </h2>
                <span class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Klik "Reset Meja" setelah pelanggan selesai makan
                </span>
            </div>
        </div>
        
        <div class="p-6">
            @if(isset($tables) && $tables->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($tables as $table)
                <div class="relative rounded-xl border-2 transition-all duration-300 hover:shadow-lg
                    {{ $table->active_orders_count > 0 
                        ? 'border-orange-300 bg-orange-50' 
                        : ($table->is_locked ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50') }}"
                    id="table-card-{{ $table->qr_code }}">
                    
                    <!-- Status Indicator -->
                    <div class="absolute top-3 right-3">
                        @if($table->active_orders_count > 0)
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                            </span>
                        @elseif($table->is_locked)
                            <span class="relative flex h-3 w-3">
                                <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                        @else
                            <span class="inline-flex rounded-full h-3 w-3 bg-green-400"></span>
                        @endif
                    </div>

                    <div class="p-4">
                        <!-- Table Name -->
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                {{ $table->active_orders_count > 0 ? 'bg-orange-200' : 'bg-gray-200' }}">
                                <i class="fas fa-utensils {{ $table->active_orders_count > 0 ? 'text-orange-600' : 'text-gray-500' }}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $table->meja }}</h3>
                                @if($table->nama_tempat)
                                    <p class="text-xs text-gray-500">{{ $table->nama_tempat }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Order Info -->
                        @if($table->active_orders_count > 0)
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-shopping-bag mr-1"></i>Pesanan Aktif
                                    </span>
                                    <span class="font-bold text-orange-600">{{ $table->active_orders_count }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-money-bill-wave mr-1"></i>Total
                                    </span>
                                    <span class="font-bold text-orange-600">
                                        Rp {{ number_format($table->total_active_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($table->has_unpaid)
                                    <div class="flex items-center text-xs text-yellow-700 bg-yellow-100 rounded-lg px-2 py-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Ada pesanan belum dibayar
                                    </div>
                                @endif

                                <!-- Detail Pesanan -->
                                <div class="border-t border-orange-200 pt-2 mt-2">
                                    @foreach($table->active_orders as $activeOrder)
                                        <div class="flex justify-between items-center text-xs py-1">
                                            <span class="font-mono text-gray-600">{{ $activeOrder->order_number }}</span>
                                            <div class="flex items-center space-x-1">
                                                @if($activeOrder->order_status === 'waiting')
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu</span>
                                                @elseif($activeOrder->order_status === 'processed')
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs">Diproses</span>
                                                @endif
                                                @if($activeOrder->payment_status === 'paid')
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Lunas</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Reset Button -->
                            <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', {{ $table->active_orders_count }}, {{ $table->has_unpaid ? 'true' : 'false' }})"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 px-4 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-[1.02]"
                                    id="reset-btn-{{ $table->qr_code }}">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Reset Meja
                            </button>
                        @else
                            <div class="text-center py-4">
                                @if($table->is_locked)
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-user-check text-blue-500 text-lg"></i>
                                    </div>
                                    <p class="text-sm text-blue-600 font-medium">Terisi (Belum Memesan)</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4 italic">Pelanggan sedang memilih menu...</p>
                                    
                                    <!-- Reset Button for Locked but No Order -->
                                    <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', 0, false)"
                                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-1.5 px-3 rounded-lg transition-all duration-200 font-semibold text-xs flex items-center justify-center"
                                            id="reset-btn-{{ $table->qr_code }}">
                                        <i class="fas fa-sync-alt mr-2 text-[10px]"></i>
                                        Reset Meja
                                    </button>
                                @else
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-check text-green-500 text-lg"></i>
                                    </div>
                                    <p class="text-sm text-green-600 font-medium">Tersedia</p>
                                    @if($table->completed_today > 0)
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $table->completed_today }} pesanan selesai hari ini
                                        </p>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chair text-4xl text-gray-300 mb-3"></i>
                    <p class="text-lg">Belum ada meja yang terdaftar</p>
                    <p class="text-sm text-gray-400">Tambahkan QR Code di panel admin untuk mulai mengelola meja</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-orange-600 mr-2"></i>
                Pesanan Terbaru
            </h2>
            <a href="{{ route('cashier.orders') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition text-sm flex items-center">
                Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-orange-600 text-xs"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    <div class="flex items-center space-x-1 mt-0.5">
                                        <span class="text-[10px] bg-orange-100 text-orange-600 px-1.5 rounded uppercase font-bold">Meja: {{ $order->qr_code }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-orange-600">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_method === 'cashier')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Kasir</span>
                            @elseif($order->payment_method === 'e_wallet')
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">E-Wallet</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Transfer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->order_status === 'waiting')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Menunggu</span>
                            @elseif($order->order_status === 'processed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Diproses</span>
                            @elseif($order->order_status === 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Batal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">

                                <a href="{{ route('cashier.order.show', $order) }}" 
                                class="bg-blue-100 text-blue-600 w-10 h-10 flex items-center justify-center border border-blue-200 hover:bg-blue-200 transition"
                                title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                                <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                                        class="bg-green-100 text-green-600 w-10 h-10 flex items-center justify-center border border-green-200 hover:bg-green-200 transition"
                                        title="Proses Pembayaran Tunai">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                @endif
                                
                                @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending')
                                <button onclick="confirmPayment({{ $order->id }})" 
                                        class="bg-yellow-100 text-yellow-600 w-10 h-10 flex items-center justify-center border border-yellow-200 hover:bg-yellow-200 transition"
                                        title="Konfirmasi Pembayaran">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                                
                                <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
                                class="bg-gray-100 text-gray-600 w-10 h-10 flex items-center justify-center border border-gray-200 hover:bg-gray-200 transition"
                                title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Belum ada pesanan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal - Warna disamakan dengan orange theme -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="p-5 border-b bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Proses Pembayaran Tunai
                </h2>
                <button onclick="closePaymentModal()" class="text-white hover:text-orange-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Order Detail -->
            <div id="paymentDetail" class="mb-6 p-5 bg-orange-50 rounded-xl border border-orange-200">
                <!-- Payment details will be loaded here -->
            </div>
            
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="orderId" name="order_id">
                
                <div class="mb-5">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-money-bill text-green-600 mr-1"></i>
                        Jumlah Dibayar
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-medium">Rp</span>
                        <input type="number" id="amountPaid" name="amount_paid" 
                               class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition"
                               placeholder="0"
                               min="0"
                               required>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-undo-alt text-blue-600 mr-1"></i>
                        Kembalian
                    </label>
                    <div class="p-4 bg-gray-100 rounded-xl font-bold text-2xl text-orange-600 text-right" id="changeDisplay">
                        Rp 0
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white py-3 rounded-xl hover:bg-orange-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-check-circle mr-2"></i>Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Function to process cash payment
function processPayment(orderId, totalAmount, orderNumber) {
    // Set data ke modal
    document.getElementById('orderId').value = orderId;
    document.getElementById('paymentDetail').innerHTML = `
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">No. Order:</span>
                <span class="font-mono font-bold text-orange-600">${orderNumber}</span>
            </div>
            <div class="flex justify-between items-center text-lg">
                <span class="text-gray-600">Total:</span>
                <span class="font-bold text-orange-600 text-xl">Rp ${formatPrice(totalAmount)}</span>
            </div>
        </div>
    `;
    
    // Reset form
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    
    // Tampilkan modal dengan animasi
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Focus ke input jumlah
    setTimeout(() => {
        document.getElementById('amountPaid').focus();
    }, 100);
}

// Hitung kembalian otomatis
document.getElementById('amountPaid')?.addEventListener('input', function() {
    const totalText = document.getElementById('paymentDetail').innerHTML;
    const match = totalText.match(/Rp ([\d.]+)/g);
    
    if (match && match[1]) {
        const total = parseFloat(match[1].replace(/\./g, ''));
        const paid = parseFloat(this.value) || 0;
        const change = paid - total;
        
        document.getElementById('changeDisplay').textContent = 'Rp ' + (change > 0 ? formatPrice(change) : '0');
        
        // Ubah warna display berdasarkan nilai
        if (change >= 0) {
            document.getElementById('changeDisplay').classList.add('text-green-600');
            document.getElementById('changeDisplay').classList.remove('text-red-600');
        } else {
            document.getElementById('changeDisplay').classList.add('text-red-600');
            document.getElementById('changeDisplay').classList.remove('text-green-600');
        }
    }
});

// Submit form pembayaran
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('orderId').value;
    const formData = new FormData(this);
    
    // Disable button
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    fetch(`/cashier/orders/${orderId}/process-cash-payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('✅ Pembayaran berhasil! Kembalian: Rp ' + formatPrice(data.change), 'success');
            closePaymentModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Proses';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Terjadi kesalahan. Silakan coba lagi.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Proses';
    });
});

// Function to confirm non-cash payment
function confirmPayment(orderId) {
    if (confirm('Konfirmasi pembayaran untuk pesanan ini?')) {
        fetch(`/cashier/orders/${orderId}/confirm-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ ' + data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        });
    }
}

// Close modal
function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
    document.getElementById('paymentForm').reset();
}

// Show notification
function showNotification(message, type = 'success') {
    // Cek apakah sudah ada toast, jika belum buat
    let toast = document.getElementById('notificationToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'notificationToast';
        toast.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 z-50';
        document.body.appendChild(toast);
    }
    
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    toast.innerHTML = message;
    toast.style.transform = 'translateX(0)';
    toast.style.opacity = '1';
    
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
    }, 3000);
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Close modal when clicking outside
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Handle enter key on amount input
document.getElementById('amountPaid')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    }
});

// ============================================
// 🔥 FUNCTION RESET MEJA
// ============================================
function resetTable(qrCode, mejaName, activeCount, hasUnpaid) {
    let warningHtml = '';
    
    if (hasUnpaid) {
        warningHtml = `
            <div class="bg-yellow-100 border border-yellow-300 p-3 rounded-lg mb-3">
                <p class="text-yellow-800 text-sm font-semibold flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Perhatian: Ada pesanan yang belum dibayar!
                </p>
                <p class="text-yellow-700 text-xs mt-1">Pesanan yang belum dibayar akan <strong>dibatalkan</strong> dan stok akan dikembalikan.</p>
            </div>
        `;
    }

    Swal.fire({
        title: `Reset ${mejaName}?`,
        html: `
            <div class="text-left">
                <p class="mb-3">Meja <strong>${mejaName}</strong> memiliki <strong>${activeCount} pesanan aktif</strong>.</p>
                ${warningHtml}
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Apa yang akan terjadi:</strong>
                    </p>
                    <ul class="text-blue-700 text-xs mt-2 space-y-1 ml-4 list-disc">
                        <li>Pesanan yang <strong>sudah dibayar</strong> → ditandai <span class="text-green-600 font-bold">Selesai</span></li>
                        <li>Pesanan yang <strong>belum dibayar</strong> → ditandai <span class="text-red-600 font-bold">Dibatalkan</span></li>
                        <li>Session customer di meja ini akan di-reset</li>
                        <li>Meja siap untuk pelanggan baru</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Ya, Reset Meja!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Mereset Meja...',
                html: `<p>Sedang memproses reset untuk <strong>${mejaName}</strong></p>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Disable the button
            const btn = document.getElementById(`reset-btn-${qrCode}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mereset...';
            }

            fetch('{{ route("cashier.table.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrCode
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Meja Berhasil Direset!',
                        html: `
                            <div class="text-left">
                                <p class="mb-2">${data.message}</p>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                    <p class="text-green-700 text-sm flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Meja <strong>${mejaName}</strong> sekarang tersedia untuk pelanggan baru
                                    </p>
                                </div>
                            </div>
                        `,
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });

                    // Update card UI
                    const card = document.getElementById(`table-card-${qrCode}`);
                    if (card) {
                        card.classList.add('opacity-50');
                    }

                    setTimeout(() => location.reload(), 3000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    // Re-enable button
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Reset Meja';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mereset meja. Silakan coba lagi.',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Reset Meja';
                }
            });
        }
    });
}
</script>
@endpush
@endsection
@extends('layouts.cashier')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-4 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Daftar Pesanan</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-list mr-2"></i>Kelola semua pesanan pelanggan
                    <i class="fas fa-calendar ml-2 md:ml-4 mr-2"></i>{{ now()->format('d F Y') }}
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-shopping-cart text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Filter Section - Responsive -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <form method="GET" action="{{ route('cashier.orders') }}" class="flex flex-col md:flex-row flex-wrap gap-3 md:gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pesanan</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="No. Order atau Nama Customer..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                </div>
            </div>

            <!-- Filter Status -->
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pesanan</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Pembayaran -->
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                <select name="payment" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>✅ Lunas</option>
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" 
                       name="date" 
                       value="{{ request('date') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
            </div>

            <!-- Buttons -->
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-orange-600 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-orange-700 transition text-sm flex-1 md:flex-none">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cashier.orders') }}" class="bg-gray-500 text-white px-4 md:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition text-sm flex-1 md:flex-none">
                    <i class="fas fa-redo-alt mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards - Mobile Responsive Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Menunggu</p>
                    <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['waiting'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Diproses</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['processed'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-spinner text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Selesai Hari Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-green-600">{{ $stats['completed_today'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500 hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Payment</p>
                    <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $stats['pending_payment'] ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-12 md:h-12 flex items-center justify-center rounded-full">
                    <i class="fas fa-credit-card text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Section - Desktop Table & Mobile Cards -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Desktop Table View - SAMA DENGAN DASHBOARD -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 whitespace-nowrap">
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
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm font-medium">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-orange-600 text-xs"></i>
                                </div>
                                <span>{{ $order->customer_name }}</span>
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
                            @if($order->order_status == 'waiting')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Menunggu</span>
                            @elseif($order->order_status == 'processed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">⚙️ Diproses</span>
                            @elseif($order->order_status == 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✅ Selesai</span>
                            @elseif($order->order_status == 'cancelled')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">❌ Dibatalkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✅ Lunas</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg border border-blue-200 hover:bg-blue-200 transition"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($order->payment_method === 'cashier' && $order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                                <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                                        class="bg-green-100 text-green-600 w-8 h-8 flex items-center justify-center rounded-lg border border-green-200 hover:bg-green-200 transition"
                                        title="Proses Pembayaran Tunai">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                @endif
                                
                                @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                                <button onclick="confirmPayment({{ $order->id }})" 
                                        class="bg-yellow-100 text-yellow-600 w-8 h-8 flex items-center justify-center rounded-lg border border-yellow-200 hover:bg-yellow-200 transition"
                                        title="Konfirmasi Pembayaran">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                                
                                <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
                                   class="bg-gray-100 text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-200 transition"
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
                            <p class="text-lg">Tidak ada pesanan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($orders as $order)
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-orange-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="font-mono font-bold text-orange-600">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-orange-600 text-sm">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs text-gray-600">
                        <i class="fas fa-chair mr-1"></i>Meja {{ $order->qr_code }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->payment_method === 'cashier') bg-green-100 text-green-700
                        @elseif($order->payment_method === 'e_wallet') bg-purple-100 text-purple-700
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ $order->payment_method === 'cashier' ? 'Kasir' : ($order->payment_method === 'e_wallet' ? 'E-Wallet' : 'Transfer') }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->order_status === 'waiting') bg-yellow-100 text-yellow-700
                        @elseif($order->order_status === 'processed') bg-blue-100 text-blue-700
                        @elseif($order->order_status === 'completed') bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        @if($order->order_status === 'waiting') Menunggu
                        @elseif($order->order_status === 'processed') Diproses
                        @elseif($order->order_status === 'completed') Selesai
                        @else Batal @endif
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full 
                        @if($order->payment_status === 'paid') bg-green-100 text-green-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('cashier.order.show', $order) }}" 
                       class="bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-blue-200 transition">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($order->payment_method === 'cashier' && $order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                    <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                            class="bg-green-100 text-green-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-green-200 transition">
                        <i class="fas fa-money-bill-wave mr-1"></i>Bayar
                    </button>
                    @endif
                    @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                    <button onclick="confirmPayment({{ $order->id }})" 
                            class="bg-yellow-100 text-yellow-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-yellow-200 transition">
                        <i class="fas fa-check-circle mr-1"></i>Konfirmasi
                    </button>
                    @endif
                    <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
                       class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs flex items-center hover:bg-gray-200 transition">
                        <i class="fas fa-print mr-1"></i>Struk
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-shopping-cart text-5xl mb-3 text-gray-300"></i>
                <p class="text-lg">Tidak ada pesanan</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(isset($orders) && method_exists($orders, 'links') && $orders->hasPages())
        <div class="px-4 md:px-6 py-4 border-t">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Payment Modal - Mobile Responsive -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
        <div class="p-4 md:p-5 border-b bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-lg md:text-xl font-bold flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Proses Pembayaran
                </h2>
                <button onclick="closePaymentModal()" class="text-white hover:text-orange-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            <div id="paymentDetail" class="mb-4 md:mb-6 p-4 md:p-5 bg-orange-50 rounded-xl border border-orange-200">
                <!-- Payment details will be loaded here -->
            </div>
            
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="orderId" name="order_id">
                
                <div class="mb-4 md:mb-5">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                        <i class="fas fa-money-bill text-green-600 mr-1"></i>
                        Jumlah Dibayar
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 md:left-4 top-2.5 md:top-3 text-gray-500 font-medium text-sm md:text-base">Rp</span>
                        <input type="number" id="amountPaid" name="amount_paid" 
                               class="w-full pl-10 md:pl-12 pr-3 md:pr-4 py-2.5 md:py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition text-sm md:text-base"
                               placeholder="0"
                               min="0"
                               required>
                    </div>
                </div>
                
                <div class="mb-4 md:mb-6">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                        <i class="fas fa-undo-alt text-blue-600 mr-1"></i>
                        Kembalian
                    </label>
                    <div class="p-3 md:p-4 bg-gray-100 rounded-xl font-bold text-xl md:text-2xl text-gray-600 text-right" id="changeDisplay">
                        Rp 0
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 py-2.5 md:py-3 rounded-xl hover:bg-gray-300 transition font-semibold text-sm md:text-base">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white py-2.5 md:py-3 rounded-xl hover:bg-orange-700 transition font-semibold shadow-lg text-sm md:text-base">
                        <i class="fas fa-check-circle mr-2"></i>Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============================================
// FUNCTION PROCESS CASH PAYMENT
// ============================================
function processPayment(orderId, totalAmount, orderNumber) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('paymentDetail').innerHTML = `
        <div class="space-y-3">
            <div class="flex justify-between items-center pb-2 border-b border-orange-200">
                <span class="text-gray-600 font-medium text-sm">No. Order:</span>
                <span class="font-mono font-bold text-orange-600 bg-orange-100 px-3 py-1 rounded-lg text-sm">${orderNumber}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold text-base">Total Bayar:</span>
                <span class="font-bold text-orange-600 text-xl md:text-2xl">Rp ${formatPrice(totalAmount)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    document.getElementById('changeDisplay').classList.add('text-gray-600');
    document.getElementById('changeDisplay').classList.remove('text-green-600', 'text-red-600');
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        document.getElementById('amountPaid').focus();
    }, 100);
}

// ============================================
// FUNCTION CALCULATE CHANGE
// ============================================
document.getElementById('amountPaid')?.addEventListener('input', function() {
    const totalText = document.getElementById('paymentDetail').innerHTML;
    const match = totalText.match(/Rp ([\d.]+)/g);
    
    if (match && match.length >= 2) {
        const totalStr = match[1].replace(/\./g, '');
        const total = parseFloat(totalStr);
        const paid = parseFloat(this.value) || 0;
        const change = paid - total;
        
        const changeDisplay = document.getElementById('changeDisplay');
        if (paid < total) {
            changeDisplay.textContent = 'Rp ' + formatPrice(change * -1) + ' (Kurang)';
            changeDisplay.classList.add('text-red-600');
            changeDisplay.classList.remove('text-green-600', 'text-gray-600');
        } else {
            changeDisplay.textContent = 'Rp ' + formatPrice(change);
            changeDisplay.classList.add(change > 0 ? 'text-green-600' : 'text-gray-600');
            changeDisplay.classList.remove(change > 0 ? 'text-red-600' : 'text-green-600');
        }
    }
});

// ============================================
// FUNCTION SUBMIT PAYMENT FORM
// ============================================
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('orderId').value;
    const amountPaid = document.getElementById('amountPaid').value;
    
    const totalText = document.getElementById('paymentDetail').innerHTML;
    const match = totalText.match(/Rp ([\d.]+)/g);
    
    if (match && match.length >= 2) {
        const totalStr = match[1].replace(/\./g, '');
        const total = parseFloat(totalStr);
        const paid = parseFloat(amountPaid) || 0;
        
        if (paid < total) {
            Swal.fire({
                icon: 'error',
                title: 'Pembayaran Kurang!',
                text: `Jumlah pembayaran kurang Rp ${formatPrice(total - paid)}`,
                confirmButtonColor: '#f97316'
            });
            return;
        }
    }
    
    const formData = new FormData(this);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
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
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                html: `
                    <div class="text-left">
                        <p>${data.message}</p>
                        <p class="font-bold text-green-700 mt-2">Kembalian: Rp ${formatPrice(data.change)}</p>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false
            });
            closePaymentModal();
            setTimeout(() => location.reload(), 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonColor: '#f97316'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses pembayaran',
            confirmButtonColor: '#f97316'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// ============================================
// FUNCTION CONFIRM NON-CASH PAYMENT
// ============================================
function confirmPayment(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: `
            <p class="mb-3">Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?</p>
            <div class="bg-yellow-100 p-3 rounded-lg">
                <p class="text-yellow-800 text-sm">Pesanan akan diproses oleh dapur.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang mengkonfirmasi pembayaran',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/cashier/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#f97316'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan',
                    confirmButtonColor: '#f97316'
                });
            });
        }
    });
}

// ============================================
// FUNCTION CLOSE MODAL
// ============================================
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('paymentForm').reset();
    
    const changeDisplay = document.getElementById('changeDisplay');
    changeDisplay.textContent = 'Rp 0';
    changeDisplay.classList.add('text-gray-600');
    changeDisplay.classList.remove('text-green-600', 'text-red-600');
}

// ============================================
// FUNCTION FORMAT PRICE
// ============================================
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// ============================================
// CLICK OUTSIDE MODAL
// ============================================
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// ============================================
// ENTER KEY ON AMOUNT INPUT
// ============================================
document.getElementById('amountPaid')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    }
});
</script>
@endpush
@endsection
@extends('layouts.cashier')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header dengan Tombol Kembali - Mobile Friendly -->
    <div class="mb-4 md:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <a href="{{ route('cashier.orders') }}" class="flex items-center text-gray-600 hover:text-orange-600 transition text-sm md:text-base">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Pesanan
        </a>
        <a href="{{ route('cashier.receipt', $order) }}" target="_blank" 
           class="bg-blue-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg hover:bg-blue-700 transition flex items-center text-sm md:text-base">
            <i class="fas fa-print mr-2"></i>Cetak Struk
        </a>
    </div>

    <!-- Header Info - Responsive -->
    <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg mb-6 md:mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Detail Pesanan</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-hashtag mr-2"></i>{{ $order->order_number }}
                    <i class="fas fa-user ml-2 md:ml-4 mr-2"></i>{{ $order->customer_name }}
                </p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-orange-100 text-xs md:text-sm">Tanggal Pesan</p>
                <p class="font-semibold text-sm md:text-base">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Content Grid - Responsive -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
        <!-- Left Column - Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 md:p-6 border-b bg-gray-50">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-bag text-orange-600 mr-2"></i>
                        Daftar Item Pesanan
                    </h2>
                </div>
                
                <div class="p-4 md:p-6">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Menu</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Qty</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Harga</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            @php
                                                $productItem = App\Models\Product::find($item->product_id);
                                                $imageUrl = null;
                                                
                                                if ($productItem && $productItem->image) {
                                                    $imagePath = $productItem->image;
                                                    if (!str_contains($imagePath, 'products/')) {
                                                        $imagePath = 'products/' . $imagePath;
                                                    }
                                                    if (Storage::disk('public')->exists($imagePath)) {
                                                        $imageUrl = asset('storage/' . $imagePath);
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" 
                                                    alt="{{ $item->product_name }}"
                                                    class="w-12 h-12 object-cover rounded-lg mr-3"
                                                    onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=Gambar+Tidak+Tersedia';">
                                            @else
                                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-utensils text-orange-600"></i>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <p class="font-semibold text-sm">{{ $item->product_name }}</p>
                                                @if($productItem && $productItem->description)
                                                    <p class="text-xs text-gray-500">{{ Str::limit($productItem->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right font-semibold text-orange-600">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-right font-semibold">Total</td>
                                    <td class="px-4 py-4 text-right font-bold text-orange-600 text-lg">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile Card View untuk Items -->
                    <div class="md:hidden space-y-3">
                        @foreach($order->items as $item)
                        @php
                            $productItem = App\Models\Product::find($item->product_id);
                            $imageUrl = null;
                            
                            if ($productItem && $productItem->image) {
                                $imagePath = $productItem->image;
                                if (!str_contains($imagePath, 'products/')) {
                                    $imagePath = 'products/' . $imagePath;
                                }
                                if (Storage::disk('public')->exists($imagePath)) {
                                    $imageUrl = asset('storage/' . $imagePath);
                                }
                            }
                        @endphp
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-start space-x-3">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                        alt="{{ $item->product_name }}"
                                        class="w-16 h-16 object-cover rounded-lg"
                                        onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=Gambar+Tidak+Tersedia';">
                                @else
                                    <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-utensils text-orange-600 text-2xl"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-sm">{{ $item->product_name }}</p>
                                            @if($productItem && $productItem->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($productItem->description, 40) }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-orange-600 text-sm">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                        <span class="text-xs text-gray-500">Qty: {{ $item->quantity }}</span>
                                        <span class="text-xs text-gray-500">
                                            @ Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Total Mobile -->
                        <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-700">Total</span>
                                <span class="font-bold text-orange-600 text-xl">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Order Info & Actions -->
        <div class="space-y-4 md:space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-3 md:p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm md:text-base">
                        <i class="fas fa-info-circle text-orange-600 mr-2"></i>
                        Informasi Status
                    </h3>
                </div>
                <div class="p-4 md:p-6 space-y-4">
                    <!-- Order Status -->
                    <div>
                        <label class="block text-xs md:text-sm text-gray-500 mb-2">Status Pesanan</label>
                        <select onchange="updateOrderStatus({{ $order->id }}, this.value)" 
                                class="w-full px-3 md:px-4 py-2.5 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm md:text-base disabled:bg-gray-100 disabled:cursor-not-allowed"
                                {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <label class="block text-xs md:text-sm text-gray-500 mb-2">Status Pembayaran</label>
                        @if($order->payment_status === 'paid')
                            <div class="bg-green-100 text-green-800 px-3 md:px-4 py-2.5 md:py-3 rounded-lg flex flex-col md:flex-row md:items-center md:justify-between gap-1 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>Lunas</span>
                                </div>
                                <div class="text-xs md:text-sm">
                                    Rp {{ number_format($order->paid_amount, 0, ',', '.') }}
                                    @if($order->paid_at)
                                        <span class="ml-1">({{ $order->paid_at->format('H:i') }})</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-100 text-yellow-800 px-3 md:px-4 py-2.5 md:py-3 rounded-lg flex items-center text-sm">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Pending</span>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs md:text-sm text-gray-500 mb-2">Metode Pembayaran</label>
                        <div class="bg-gray-100 px-3 md:px-4 py-2.5 md:py-3 rounded-lg flex items-center text-sm md:text-base">
                            @if($order->payment_method === 'cashier')
                                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                <span>Tunai (Bayar di Kasir)</span>
                            @elseif($order->payment_method === 'e_wallet')
                                <i class="fas fa-mobile-alt text-purple-600 mr-2"></i>
                                <span>E-Wallet</span>
                            @else
                                <i class="fas fa-university text-blue-600 mr-2"></i>
                                <span>Transfer Bank</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info Card - DENGAN DETAIL MEJA -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-3 md:p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm md:text-base">
                        <i class="fas fa-user text-orange-600 mr-2"></i>
                        Informasi Pelanggan & Meja
                    </h3>
                </div>
                <div class="p-4 md:p-6 space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                        <span class="text-gray-500 text-sm">Nama Pelanggan</span>
                        <span class="font-semibold text-sm md:text-base">{{ $order->customer_name }}</span>
                    </div>
                    
                    <!-- 🔥 DETAIL MEJA -->
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1 border-t border-gray-100 pt-2">
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-chair text-orange-500 mr-1"></i> Nama Meja
                        </span>
                        <span class="font-semibold text-sm md:text-base text-orange-600">{{ $order->qr_code ?? 'Tidak ada' }}</span>
                    </div>
                    
                    @php
                        // Cari detail meja dari tabel qr_codes
                        $tableDetail = null;
                        if ($order->qr_code) {
                            $tableDetail = App\Models\QrCode::where('qr_code', $order->qr_code)->first();
                        }
                    @endphp
                    
                    @if($tableDetail)
                    <div class="bg-orange-50 rounded-lg p-3 space-y-2">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-tag mr-1"></i> Nama Tempat
                            </span>
                            <span class="font-medium text-sm">{{ $tableDetail->nama_tempat ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-qrcode mr-1"></i> Kode Meja
                            </span>
                            <span class="font-mono text-xs bg-white px-2 py-1 rounded">{{ $tableDetail->qr_code }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-calendar-alt mr-1"></i> Dibuat
                            </span>
                            <span class="text-sm">{{ $tableDetail->created_at ? $tableDetail->created_at->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->customer_phone)
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-phone mr-1"></i> No. Telepon
                        </span>
                        <span class="font-semibold text-sm md:text-base">{{ $order->customer_phone }}</span>
                    </div>
                    @endif
                    
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-utensils mr-1"></i> Tipe Order
                        </span>
                        <span class="font-semibold text-orange-600 text-sm md:text-base">
                            <i class="fas fa-store mr-1"></i> Makan di Tempat
                        </span>
                    </div>
                    
                    @if($order->notes)
                    <div class="mt-3 pt-3 border-t">
                        <span class="text-gray-500 block mb-1 text-sm">
                            <i class="fas fa-sticky-note mr-1"></i> Catatan:
                        </span>
                        <p class="text-gray-700 bg-gray-50 p-3 rounded-lg text-sm">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons - Responsive -->
            <div class="grid grid-cols-1 gap-3">
                @if($order->payment_method === 'cashier' && $order->payment_status === 'pending')
                <button onclick="processPayment({{ $order->id }}, {{ $order->total_amount }}, '{{ $order->order_number }}')" 
                        class="bg-green-600 text-white py-2.5 md:py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Proses Pembayaran Tunai
                </button>
                @endif
                
                @if($order->payment_method !== 'cashier' && $order->payment_status === 'pending')
                <button onclick="confirmPayment({{ $order->id }})" 
                        class="bg-yellow-600 text-white py-2.5 md:py-3 rounded-lg hover:bg-yellow-700 transition flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-check-circle mr-2"></i>
                    Konfirmasi Pembayaran
                </button>
                @endif
                
                @if($order->order_status !== 'cancelled' && $order->order_status !== 'completed')
                <button onclick="cancelOrder({{ $order->id }})" 
                        class="bg-red-600 text-white py-2.5 md:py-3 rounded-lg hover:bg-red-700 transition flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-times-circle mr-2"></i>
                    Batalkan Pesanan
                </button>
                @endif
            </div>
        </div>
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
// FUNCTION UPDATE STATUS PESANAN
// ============================================
function updateOrderStatus(orderId, status) {
    let statusText = '';
    let statusColor = '';
    
    switch(status) {
        case 'waiting': 
            statusText = 'MENUNGGU'; 
            statusColor = '#f59e0b';
            break;
        case 'processed': 
            statusText = 'DIPROSES'; 
            statusColor = '#3b82f6';
            break;
        case 'completed': 
            statusText = 'SELESAI'; 
            statusColor = '#10b981';
            break;
        case 'cancelled': 
            statusText = 'DIBATALKAN'; 
            statusColor = '#ef4444';
            break;
    }
    
    Swal.fire({
        title: 'Ubah Status Pesanan?',
        html: `
            <div class="text-left">
                <p class="mb-2">Anda akan mengubah status pesanan menjadi:</p>
                <div class="flex items-center justify-center p-3 rounded-lg" style="background-color: ${statusColor}20; border: 1px solid ${statusColor}">
                    <span class="font-bold text-lg" style="color: ${statusColor}">${statusText}</span>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: statusColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const select = event.target;
            const originalValue = select.defaultValue;
            select.disabled = true;
            select.style.opacity = '0.5';
            
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang mengubah status pesanan',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/cashier/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    select.defaultValue = status;
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'bg-orange-600 px-6 py-2 rounded-lg'
                        }
                    });
                    select.value = originalValue;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat update status',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'bg-orange-600 px-6 py-2 rounded-lg'
                    }
                });
                select.value = originalValue;
            })
            .finally(() => {
                select.disabled = false;
                select.style.opacity = '1';
            });
        } else {
            event.target.value = event.target.defaultValue;
        }
    });
}

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

// Calculate change
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

// Submit payment form
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
                confirmButtonColor: '#f97316',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2 rounded-lg'
                }
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
                        <p class="mb-2">${data.message}</p>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <p class="font-bold text-green-700">Kembalian: Rp ${formatPrice(data.change)}</p>
                        </div>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
            closePaymentModal();
            setTimeout(() => location.reload(), 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonColor: '#f97316',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2 rounded-lg'
                }
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
            confirmButtonColor: '#f97316',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2 rounded-lg'
            }
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Confirm non-cash payment
function confirmPayment(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
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
                    'Content-Type': 'application/json',
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
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#f97316',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-6 py-2 rounded-lg'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan',
                    confirmButtonColor: '#f97316',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2 rounded-lg'
                    }
                });
            });
        }
    });
}

// Cancel order
function cancelOrder(orderId) {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Pesanan yang dibatalkan tidak dapat dikembalikan dan stok akan dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/cashier/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
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
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#f97316',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-6 py-2 rounded-lg'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan',
                    confirmButtonColor: '#f97316',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2 rounded-lg'
                    }
                });
            });
        }
    });
}

// Close modal
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

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Click outside modal
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Enter key support
document.getElementById('amountPaid')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    }
});
</script>
@endpush
@endsection
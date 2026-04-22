@extends('admin.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-orange-600 mb-2">
                Detail Pesanan #{{ $order->order_number }}
            </h1>
            <p class="text-gray-600">
                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                {{ $order->created_at->format('d F Y H:i') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center" target="_blank">
                <i class="fas fa-file-invoice mr-2"></i> Invoice
            </a>
            <button onclick="exportPDF()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </button>
        </div>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <div>
                <p class="font-bold">Sukses!</p>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <div>
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Pesanan -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-info-circle text-orange-600 mr-2"></i>
                        Informasi Pesanan
                    </h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'waiting' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'processed' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        $statusIcons = [
                            'pending' => 'fa-clock',
                            'waiting' => 'fa-clock',
                            'processing' => 'fa-cog',
                            'processed' => 'fa-cog',
                            'completed' => 'fa-check-circle',
                            'cancelled' => 'fa-times-circle'
                        ];
                        $statusText = [
                            'pending' => 'Pending',
                            'waiting' => 'Menunggu',
                            'processing' => 'Processing',
                            'processed' => 'Diproses',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-800' }}">
                        <i class="fas {{ $statusIcons[$order->order_status] ?? 'fa-info' }} mr-1"></i>
                        {{ $statusText[$order->order_status] ?? ucfirst($order->order_status) }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Pesanan:</span>
                                <span class="font-mono font-semibold">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pesan:</span>
                                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Metode Pembayaran:</span>
                                <span>{{ $order->payment_method ?? 'Cash' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status Pembayaran:</span>
                                @if($order->payment_status == 'paid')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Lunas
                                    </span>
                                @elseif($order->payment_status == 'partial')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-clock mr-1"></i> Sebagian
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-times-circle mr-1"></i> Belum Dibayar
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Pesanan:</span>
                                <span class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Diskon:</span>
                                <span class="text-red-600">- Rp {{ number_format($order->discount ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak (PPN):</span>
                                <span>Rp {{ number_format($order->tax ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 mt-2">
                                <span class="font-bold text-gray-800">Grand Total:</span>
                                <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($order->grand_total ?? $order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Meja -->
                    @if($order->qr_code || $order->table_number)
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center">
                            <i class="fas fa-qrcode text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Meja:</span>
                            <span class="ml-2 font-semibold">{{ $order->qr_code ?? $order->table_number }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detail Produk -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-shopping-cart text-orange-600 mr-2"></i>
                        Detail Produk
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($order->items ?? [] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->name ?? $item->product_name ?? $item->menu_name }}</div>
                                    @if($item->notes)
                                    <small class="text-gray-500 block mt-1">
                                        <i class="fas fa-comment mr-1"></i> {{ $item->notes }}
                                    </small>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-1 bg-gray-100 rounded">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl mb-2"></i>
                                    <p>Tidak ada item dalam pesanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th colspan="3" class="px-6 py-4 text-right font-semibold">Total</th>
                                <th class="px-6 py-4 text-right font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Riwayat Status -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history text-orange-600 mr-2"></i>
                        Riwayat Status
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <!-- Timeline line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        
                        <!-- Timeline items -->
                        <div class="space-y-6">
                            @forelse($order->statusHistories ?? [] as $history)
                            <div class="relative pl-12">
                                <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-check text-orange-600 text-sm"></i>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ $statusText[$history->status] ?? ucfirst($history->status) }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm mb-2">{{ $history->notes ?? 'Tidak ada keterangan' }}</p>
                                    <small class="text-gray-500">
                                        <i class="fas fa-user mr-1"></i> 
                                        Oleh: {{ $history->user->name ?? 'System' }}
                                    </small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-4xl mb-2"></i>
                                <p>Belum ada riwayat status</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (1/3) -->
        <div class="space-y-6">
            <!-- Informasi Pelanggan -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-user text-orange-600 mr-2"></i>
                        Informasi Pelanggan
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-user w-5 text-gray-400 mt-1"></i>
                        <div class="ml-3">
                            <p class="font-medium">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                            <p class="text-sm text-gray-500">Nama Pelanggan</p>
                        </div>
                    </div>
                    @if($order->customer_email)
                    <div class="flex items-start">
                        <i class="fas fa-envelope w-5 text-gray-400 mt-1"></i>
                        <div class="ml-3">
                            <p class="font-medium">{{ $order->customer_email }}</p>
                            <p class="text-sm text-gray-500">Email</p>
                        </div>
                    </div>
                    @endif
                    @if($order->customer_phone)
                    <div class="flex items-start">
                        <i class="fas fa-phone w-5 text-gray-400 mt-1"></i>
                        <div class="ml-3">
                            <p class="font-medium">{{ $order->customer_phone }}</p>
                            <p class="text-sm text-gray-500">Telepon</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-sync-alt text-orange-600 mr-2"></i>
                        Update Status
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" id="updateStatusForm">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status Pesanan</label>
                            <select name="order_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }}>⏳ Menunggu</option>
                                <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                                <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status Pembayaran</label>
                            <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>✅ Lunas</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                            <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" rows="3" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Catatan Order -->
            @if($order->notes)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sticky-note text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Catatan Order</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informasi Pembayaran -->
            @if($order->payment)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-credit-card text-orange-600 mr-2"></i>
                        Informasi Pembayaran
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode:</span>
                        <span>{{ $order->payment->method ?? $order->payment_method ?? 'Cash' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Bayar:</span>
                        <span>{{ isset($order->payment->paid_at) ? date('d/m/Y H:i', strtotime($order->payment->paid_at)) : '-' }}</span>
                    </div>
                    @if($order->payment->amount)
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-600">Jumlah Dibayar:</span>
                        <span class="text-green-600">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function exportPDF() {
    Swal.fire({
        title: 'Export PDF',
        text: 'Mengekspor data pesanan ke PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            window.open('{{ route("admin.orders.export.pdf", $order->id) }}', '_blank');
            setTimeout(() => {
                Swal.close();
            }, 1500);
        }
    });
}

// Konfirmasi update status
document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
    const statusSelect = document.querySelector('select[name="order_status"]');
    const status = statusSelect.options[statusSelect.selectedIndex].text;
    const currentStatus = '{{ $statusText[$order->order_status] ?? $order->order_status }}';
    
    if (status !== currentStatus) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi Update Status',
            html: `Anda akan mengubah status dari <strong>${currentStatus}</strong> menjadi <strong>${status}</strong>. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
});
</script>
@endpush
@endsection
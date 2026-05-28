@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Manajemen Pesanan</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-shopping-cart mr-2"></i>Kelola semua pesanan customer
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-file-invoice-dollar text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-100' }} transition">
                    Semua
                    <span class="ml-1 px-2 py-0.5 rounded-full {{ !request('status') ? 'bg-orange-500' : 'bg-gray-300' }} text-xs">{{ $stats['total_orders'] }}</span>
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'waiting']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'waiting' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-100' }} transition">
                    <i class="fas fa-clock mr-1"></i> Menunggu
                    @if($stats['waiting'] > 0)
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-yellow-500 text-white text-xs">{{ $stats['waiting'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'processed']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'processed' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-100' }} transition">
                    <i class="fas fa-cog mr-1"></i> Diproses
                    @if($stats['processed'] > 0)
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-blue-500 text-white text-xs">{{ $stats['processed'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'completed' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-100' }} transition">
                    <i class="fas fa-check-circle mr-1"></i> Selesai
                    @if($stats['completed'] > 0)
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-green-500 text-white text-xs">{{ $stats['completed'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'cancelled' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-100' }} transition">
                    <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                    @if($stats['cancelled'] > 0)
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-red-500 text-white text-xs">{{ $stats['cancelled'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary - Grid 2x2 untuk tampilan lebih rapi -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Pesanan</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Payment</p>
                    <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['pending_payment'] }}</p>
                </div>
                <div class="bg-yellow-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pesanan Aktif</p>
                    <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $stats['waiting'] + $stats['processed'] }}</p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-spinner text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Revenue</p>
                    <p class="text-lg md:text-xl font-bold text-green-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-5">
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="hidden" name="status" value="{{ request('status') }}">
                
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" placeholder="Cari nomor order atau customer..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                
                <div class="w-48">
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" name="date" value="{{ request('date') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                
                <div class="w-40">
                    <select name="payment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Payment</option>
                        <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>✅ Lunas</option>
                    </select>
                </div>
                
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition flex items-center">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                
                @if(request('search') || request('date') || request('payment'))
                <a href="{{ route('admin.orders.index', ['status' => request('status')]) }}" 
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition flex items-center">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Orders Table - Desain lebih rapi -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="whitespace-nowrap">
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 whitespace-nowrap">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No. Order -->
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-semibold text-gray-800">{{ $order->order_number }}</span>
                        </td>
                        
                        <!-- Customer -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                </div>
                                <span class="text-sm text-gray-700">{{ $order->customer_name ?? 'Guest' }}</span>
                            </div>
                        </td>
                        
                        <!-- Total -->
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        
                        <!-- Metode -->
                        <td class="px-6 py-4">
                            @if($order->payment_method == 'cashier' || $order->payment_method == 'cash')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-money-bill-wave mr-1 text-xs"></i> Kasir
                                </span>
                            @elseif($order->payment_method == 'e_wallet' || $order->payment_method == 'wallet')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-wallet mr-1 text-xs"></i> E-Wallet
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-exchange-alt mr-1 text-xs"></i> Transfer
                                </span>
                            @endif
                        </td>
                        
                        <!-- Status Pesanan -->
                        <td class="px-6 py-4">
                            <select onchange="updateOrderStatus({{ $order->id }}, this.value)" 
                                    class="text-sm border-0 bg-transparent focus:ring-2 focus:ring-orange-500 rounded-lg px-2 py-1 font-medium"
                                    {{ in_array($order->order_status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                                <option value="waiting" {{ $order->order_status == 'waiting' ? 'selected' : '' }} class="text-yellow-600">⏳ Menunggu</option>
                                <option value="processed" {{ $order->order_status == 'processed' ? 'selected' : '' }} class="text-blue-600">⚙️ Diproses</option>
                                <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }} class="text-green-600">✅ Selesai</option>
                                <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }} class="text-red-600">❌ Batal</option>
                            </select>
                        </td>
                        
                        <!-- Status Pembayaran -->
                        <td class="px-6 py-4">
                            @if($order->payment_status == 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Lunas
                                </span>
                            @else
                                @if($order->order_status !== 'cancelled')
                                    <button onclick="confirmPayment({{ $order->id }})"
                                            class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        <i class="fas fa-check mr-1"></i> Konfirmasi
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Batal
                                    </span>
                                @endif
                            @endif
                        </td>
                        
                        <!-- Tanggal -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        
                        <!-- Aksi -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition" 
                                   title="Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                                   class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition" 
                                   title="Invoice">
                                    <i class="fas fa-print text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-lg">Tidak ada pesanan</p>
                                <p class="text-gray-400 text-sm mt-1">Belum ada pesanan yang masuk</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateOrderStatus(orderId, status) {
    let statusText = '';
    switch(status) {
        case 'waiting': statusText = 'Menunggu'; break;
        case 'processed': statusText = 'Diproses'; break;
        case 'completed': statusText = 'Selesai'; break;
        case 'cancelled': statusText = 'Dibatalkan'; break;
    }
    
    Swal.fire({
        title: 'Konfirmasi',
        text: `Ubah status pesanan menjadi ${statusText}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
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
                    Swal.fire('Berhasil!', data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            });
        } else {
            location.reload();
        }
    });
}

function confirmPayment(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Apakah pembayaran sudah diterima?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mengkonfirmasi pembayaran',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
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
                    Swal.fire('Berhasil!', data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection
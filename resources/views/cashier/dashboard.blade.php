@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Dashboard Kasir</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-user text-orange-600"></i>
                {{ auth()->user()->name }}
            </p>
        </div>
        
        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-100 shadow-sm text-[10px] font-black text-slate-900 uppercase tracking-widest">
            <i class="fas fa-calendar text-orange-600"></i>
            {{ now()->format('d M Y') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-10">
        @foreach([
            ['pending_payments', 'Pending', 'bg-amber-50', 'text-amber-600', 'fa-clock'],
            ['waiting_orders', 'Pesanan Baru', 'bg-blue-50', 'text-blue-600', 'fa-shopping-cart'],
            ['processed_orders', 'Diproses', 'bg-purple-50', 'text-purple-600', 'fa-spinner'],
            ['completed_orders', 'Selesai', 'bg-emerald-50', 'text-emerald-600', 'fa-check-circle']
        ] as $stat)
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 {{ $stat[2] }} rounded-xl flex items-center justify-center border {{ str_replace('text-', 'border-', $stat[3]) }}">
                    <i class="fas {{ $stat[4] }} {{ $stat[3] }} text-xs"></i>
                </div>
                <span class="text-2xl font-black text-slate-900">{{ $stats[$stat[0]] ?? 0 }}</span>
            </div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $stat[1] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Pending Payment Alerts (Removed) -->

    <!-- Management Meja -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mb-10">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center">
                <i class="fas fa-chair text-orange-600 mr-3"></i>
                Manajemen Meja
            </h2>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                @foreach($tables as $table)
                <div class="p-4 rounded-[1.5rem] border-2 transition-all duration-300 {{ $table->active_orders_count > 0 ? 'border-orange-100 bg-orange-50/30' : ($table->is_locked ? 'border-blue-100 bg-blue-50/30' : 'border-slate-50 bg-slate-50') }}">
                    <div class="flex flex-col h-full justify-between gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-slate-900 text-sm truncate">{{ $table->meja }}</span>
                            <div class="w-2.5 h-2.5 rounded-full {{ $table->active_orders_count > 0 ? 'bg-orange-500' : 'bg-emerald-400' }}"></div>
                        </div>
                        
                        @if($table->active_orders_count > 0)
                            <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest truncate">
                                Rp {{ number_format($table->total_active_amount, 0, ',', '.') }}
                            </p>
                            <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', {{ $table->active_orders_count }}, {{ $table->has_unpaid ? 'true' : 'false' }})"
                                    class="w-full bg-slate-900 text-white py-2 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 transition">
                                Reset
                            </button>
                        @elseif($table->is_locked)
                            <div class="my-1">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-blue-100 block text-center truncate">
                                    Memilih
                                </span>
                            </div>
                            <button onclick="resetTable('{{ $table->qr_code }}', '{{ $table->meja }}', 0, false)"
                                    class="w-full bg-white border border-slate-200 text-slate-600 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-rose-50 transition">
                                Reset
                            </button>
                        @else
                            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest py-1">Tersedia</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center">
                <i class="fas fa-history text-orange-600 mr-3"></i>
                Transaksi Terbaru
            </h2>
            <a href="{{ route('cashier.transactions.today') }}" class="text-[9px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-400">
                    <tr class="text-left">
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">No. Order</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest text-right">Total</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest">Waktu</th>
                        <th class="px-8 py-4 font-black text-[10px] uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-6 font-black text-slate-900">#{{ $order->order_number }}</td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-900 text-sm tracking-tight">{{ $order->customer_name }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Meja {{ $order->qrCodeRelation->meja ?? $order->qr_code ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right font-black text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest
                                    @if($order->order_status === 'completed') bg-emerald-50 text-emerald-600
                                    @elseif($order->order_status === 'processed') bg-blue-50 text-blue-600
                                    @elseif($order->order_status === 'waiting') bg-amber-50 text-amber-600
                                    @else bg-rose-50 text-rose-600 @endif">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-[10px] font-bold text-slate-400">
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('cashier.order.show', $order) }}" 
                                   class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-widest">Tidak ada pesanan terbaru</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
            Swal.fire({
                title: 'Mereset Meja...',
                html: `<p>Sedang memproses reset untuk <strong>${mejaName}</strong></p>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            });
        }
    });
}
</script>
@endpush
@endsection

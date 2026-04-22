@extends('admin.layouts.app')

@section('title', 'Semua Notifikasi')
@section('page-title', 'Pusat Notifikasi')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-semibold text-gray-800 flex items-center">
            <div class="bg-orange-100 p-2 rounded-lg mr-3">
                <i class="fas fa-bell text-orange-600"></i>
            </div>
            Daftar Notifikasi
        </h3>
        <div class="flex space-x-2">
            <button onclick="markAllAsReadFull()" class="text-sm bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                Tandai Semua Dibaca
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Waktu</th>
                    <th class="px-6 py-4 font-semibold">Tipe</th>
                    <th class="px-6 py-4 font-semibold">Pesan</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($notifications as $notif)
                <tr class="hover:bg-gray-50/80 transition-colors {{ !$notif->is_read ? 'bg-orange-50/30 font-medium' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex flex-col">
                            <span>{{ $notif->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-400">{{ $notif->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(str_contains($notif->message, 'Pesanan baru'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1"></i> Pesanan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-credit-card mr-1"></i> Pembayaran
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $notif->message }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(!$notif->is_read)
                            <span class="inline-flex items-center text-orange-600 bg-orange-100 px-2.5 py-1 rounded-full text-xs animate-pulse">
                                <span class="w-2 h-2 bg-orange-600 rounded-full mr-1.5 "></span>
                                Belum Dibaca
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Dibaca {{ $notif->read_at?->diffForHumans() }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.orders.show', $notif->order_id) }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium flex items-center group">
                                Lihat Pesanan
                                <i class="fas fa-arrow-right ml-1.5 transition-transform group-hover:translate-x-1"></i>
                            </a>
                            @if(!$notif->is_read)
                            <button onclick="markSingleAsRead({{ $notif->id }}, this)" 
                                    class="text-gray-400 hover:text-gray-600 bg-white p-1.5 border border-gray-200 rounded-lg hover:border-gray-300 transition shadow-sm">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl mb-3 block text-gray-300"></i>
                        <p>Belum ada notifikasi yang masi tersedia.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
    <div class="p-6 border-t border-gray-100 bg-gray-50/50">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    function markSingleAsRead(id, btn) {
        fetch(`/api/v1/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(res => {
            if (res.ok) {
                const tr = btn.closest('tr');
                tr.classList.remove('bg-orange-50/30', 'font-medium');
                btn.remove();
                // Refresh small UI elements if needed or just reload
                location.reload();
            }
        });
    }

    function markAllAsReadFull() {
        if (confirm('Tandai semua notifikasi sebagai dibaca?')) {
            fetch('/api/v1/notifications/mark-all-read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => {
                if (res.ok) location.reload();
            });
        }
    }
</script>
@endpush
@endsection

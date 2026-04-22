@extends('admin.layouts.app')

@section('title', 'Detail QR Code')
@section('page-title', 'Detail QR Code')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.qrcodes.index') }}" class="text-gray-600 hover:text-orange-600">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left - QR Image -->
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="bg-gray-50 rounded-lg p-6 inline-block mx-auto">
                @if(isset($qrImage))
                    <img src="{{ $qrImage }}" class="w-48 h-48 mx-auto">
                @else
                    <div class="w-48 h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-5xl text-gray-400"></i>
                    </div>
                @endif
            </div>
            <div class="flex gap-3 mt-6">
                <a href="{{ route('admin.qrcodes.download', $qrcode) }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-center">
                    <i class="fas fa-download"></i> Download
                </a>
                <a href="{{ route('admin.qrcodes.print', $qrcode) }}" target="_blank" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-center">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('admin.qrcodes.edit', $qrcode) }}" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-yellow-700 text-center">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <!-- Right - Info -->
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Informasi QR Code</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Kode QR</p>
                        <p class="font-mono text-sm font-semibold">{{ $qrcode->code }}</p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Meja</p>
                            <p class="font-medium">{{ $qrcode->meja ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span id="statusBadge" class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $qrcode->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $qrcode->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <button onclick="toggleStatus({{ $qrcode->id }})" class="ml-2 text-orange-600 text-sm">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nama Tempat</p>
                        <p class="font-medium">{{ $qrcode->nama_tempat ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dibuat</p>
                        <p class="font-medium">{{ $qrcode->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Kadaluarsa</p>
                        <p class="font-medium">{{ $qrcode->expired_at ? $qrcode->expired_at->format('d/m/Y H:i') : 'Permanen' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Scan</p>
                        <p class="font-medium">{{ $qrcode->scan_count }}x</p>
                        @if($qrcode->last_scanned_at)
                            <p class="text-xs text-gray-500">Terakhir: {{ $qrcode->last_scanned_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    @if($qrcode->notes)
                    <div>
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p class="text-sm">{{ $qrcode->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Statistik Pesanan</h3>
                <div class="grid grid-cols-4 gap-3 text-center">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $orderStats['total'] }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-yellow-600">{{ $orderStats['active'] }}</p>
                        <p class="text-xs text-gray-500">Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $orderStats['completed'] }}</p>
                        <p class="text-xs text-gray-500">Selesai</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($orderStats['revenue'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-xl p-5 border border-red-200">
                <h3 class="font-semibold text-red-800 mb-2">Hapus QR Code</h3>
                <p class="text-sm text-red-600 mb-3">Tindakan ini tidak dapat dibatalkan.</p>
                <button onclick="deleteQR({{ $qrcode->id }})" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">
                    <i class="fas fa-trash-alt"></i> Hapus Permanen
                </button>
                <form id="delete-form-{{ $qrcode->id }}" action="{{ route('admin.qrcodes.destroy', $qrcode->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleStatus(id) {
    const url = `{{ route('admin.qrcodes.toggle-status', ':id') }}`.replace(':id', id);
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('statusBadge');
            badge.textContent = data.status === 'active' ? 'Aktif' : 'Nonaktif';
            badge.className = `inline-block px-2 py-1 rounded-full text-xs font-medium ${data.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
            Swal.fire('Berhasil!', data.message, 'success');
        }
    });
}

function deleteQR(id) {
    Swal.fire({
        title: 'Hapus QR Code?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menghapus QR Code',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection
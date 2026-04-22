@extends('admin.layouts.app')

@section('title', 'Detail QR Code')
@section('page-title', 'Detail QR Code')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.qrcodes.index') }}" class="inline-flex items-center text-gray-600 hover:text-orange-600 transition gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar QR Code
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - QR Display -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden sticky top-24">
                <div class="bg-gradient-to-r from-orange-600 to-orange-500 p-6 text-center">
                    <h3 class="text-white font-semibold text-lg">QR Code</h3>
                </div>
                <div class="p-8 flex justify-center">
                    @if(isset($qrImage))
                        <img src="{{ $qrImage }}" alt="QR Code" class="w-64 h-64">
                    @elseif(isset($qrCode->qr_image))
                        <img src="{{ $qrCode->qr_image }}" alt="QR Code" class="w-64 h-64">
                    @else
                        <div class="w-64 h-64 bg-gray-100 rounded-2xl flex flex-col items-center justify-center">
                            <i class="fas fa-qrcode text-6xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500">QR Code tidak tersedia</p>
                        </div>
                    @endif
                </div>
                <div class="p-6 border-t bg-gray-50">
                    <div class="flex gap-3">
                        {{-- ✅ PERBAIKI: Pastikan ID dikirim dengan benar --}}
                        <a href="{{ route('admin.qrcodes.download', ['qrCode' => $qrCode->id]) }}"
                               class="flex-1 bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition text-center font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i> Download
                        </a>
                        {{-- ✅ PERBAIKI: Pastikan ID dikirim dengan benar --}}
                        <a href="{{ route('admin.qrcodes.print', ['qrCode' => $qrCode->id]) }}" target="_blank"
                           class="flex-1 bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition text-center font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> Informasi QR Code
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Kode QR</p>
                            <div class="bg-gray-50 rounded-xl p-3 font-mono text-sm break-all border">
                                {{ $qrCode->code ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-2 rounded-xl text-sm font-semibold {{ ($qrCode->status ?? '') === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ($qrCode->status ?? '') === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <button onclick="toggleStatus({{ $qrCode->id }})" 
                                        class="text-orange-600 hover:text-orange-700 text-sm flex items-center gap-1">
                                    <i class="fas fa-sync-alt"></i> Ubah Status
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor Meja</p>
                            <p class="font-semibold text-lg">{{ $qrCode->meja ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tipe</p>
                            <p class="font-semibold text-lg">{{ $qrCode->is_permanent ? 'Permanent' : 'Temporer' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Dibuat pada</p>
                            <p class="font-semibold">{{ $qrCode->created_at ? $qrCode->created_at->format('d F Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Kadaluarsa</p>
                            <p class="font-semibold {{ isset($qrCode->expired_at) && $qrCode->expired_at->isPast() ? 'text-red-600' : '' }}">
                                @if($qrCode->expired_at)
                                    {{ $qrCode->expired_at->format('d F Y H:i') }}
                                @else
                                    Tidak ada batas
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-800 to-blue-700 p-6">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-chart-line"></i> Statistik Penggunaan
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-3xl font-bold text-blue-600">{{ $orderStats['total'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Total Pesanan</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-3xl font-bold text-yellow-600">{{ $orderStats['active'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Aktif</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-3xl font-bold text-green-600">{{ $orderStats['completed'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Selesai</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-3xl font-bold text-orange-600">Rp {{ number_format($orderStats['revenue'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">Pendapatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- URL Scan -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-800 to-purple-700 p-6">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-link"></i> URL Scan
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3">
                        <input type="text" 
                               value="{{ route('scan.qr.validate', ['code' => $qrCode->code ?? '']) }}" 
                               id="scanUrl" 
                               class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono text-sm"
                               readonly>
                        <button onclick="copyToClipboard()" 
                                class="bg-orange-600 text-white px-6 py-3 rounded-xl hover:bg-orange-700 transition flex items-center gap-2">
                            <i class="fas fa-copy"></i> Salin
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Customer akan diarahkan ke halaman ini saat memindai QR Code</p>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="border-2 border-red-200 rounded-2xl overflow-hidden bg-red-50">
                <div class="bg-red-100 p-6">
                    <h3 class="text-red-800 font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Menghapus QR Code akan menghapus semua data terkait. Tindakan ini tidak dapat dibatalkan.</p>
                    <button onclick="deleteQR({{ $qrCode->id }})" 
                            class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i> Hapus QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleStatus(id) {
    Swal.fire({
        title: 'Ubah Status QR Code?',
        text: 'QR Code yang nonaktif tidak dapat digunakan untuk scan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            fetch(`/admin/qrcodes/${id}/toggle-status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan jaringan' });
            });
        }
    });
}

function deleteQR(id) {
    Swal.fire({
        title: 'Hapus QR Code?',
        text: 'QR Code yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch(`/admin/qrcodes/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => window.location.href = "{{ route('admin.qrcodes.index') }}", 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan jaringan' });
            });
        }
    });
}

function copyToClipboard() {
    const urlInput = document.getElementById('scanUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(urlInput.value).then(() => {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'URL scan berhasil disalin', timer: 1500, showConfirmButton: false });
    }).catch(() => {
        document.execCommand('copy');
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'URL scan berhasil disalin', timer: 1500, showConfirmButton: false });
    });
}
</script>
@endpush
@endsection
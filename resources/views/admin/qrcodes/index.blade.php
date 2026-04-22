@extends('admin.layouts.app')

@section('title', 'QR Code')
@section('page-title', 'Daftar QR Code')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Total QR</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-qrcode text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Aktif</p>
                    <p class="text-2xl font-bold">{{ $stats['active'] }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Nonaktif</p>
                    <p class="text-2xl font-bold">{{ $stats['inactive'] }}</p>
                </div>
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Permanent</p>
                    <p class="text-2xl font-bold">{{ $stats['permanent'] }}</p>
                </div>
                <i class="fas fa-infinity text-purple-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Total Scan</p>
                    <p class="text-2xl font-bold">{{ $stats['total_scans'] }}</p>
                </div>
                <i class="fas fa-eye text-orange-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Actions -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-4 border-b">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <form method="GET" action="{{ route('admin.qrcodes.index') }}" class="flex flex-wrap gap-2 flex-1">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" placeholder="Cari kode, meja, atau tempat..." 
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:ring-1 focus:ring-orange-200">
                    </div>
                    <div class="w-32">
                        <select name="status" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    @if(request()->has('filter_type') || request()->has('status') || request()->has('search'))
                    <a href="{{ route('admin.qrcodes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        <i class="fas fa-times"></i> Reset
                    </a>
                    @endif
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('admin.qrcodes.export') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('admin.qrcodes.create') }}" 
                       class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Buat QR
                    </a>
                </div>
            </div>
        </div>

        <!-- QR Grid -->
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($qrCodes as $qr)
                <div class="bg-white border rounded-xl hover:shadow-md transition group">
                    <!-- QR Image -->
                    <div class="bg-gray-50 p-4 flex justify-center border-b">
                        @if($qr->qr_image)
                            <img src="{{ $qr->qr_image }}" class="w-24 h-24">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-qrcode text-3xl text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <!-- Kode QR -->
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Kode QR</p>
                        <p class="font-mono text-xs font-semibold text-gray-800 break-all">{{ $qr->code }}</p>
                        
                        <!-- Meja & Status -->
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <div>
                                <p class="text-xs text-gray-500">Meja</p>
                                <p class="font-medium text-gray-800">{{ $qr->meja ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Status</p>
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $qr->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $qr->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- 🔥 INFORMASI MASA BERLAKU -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            @if($qr->is_permanent)
                                {{-- QR PERMANEN --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Masa Berlaku</span>
                                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-infinity mr-1"></i> Permanen
                                    </span>
                                </div>
                            @elseif($qr->expired_at)
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-500">Kadaluarsa</span>
                                    <span class="text-xs font-semibold {{ $qr->expired_at->isPast() ? 'text-red-600' : 'text-orange-600' }}">
                                        <i class="fas fa-calendar-times mr-1"></i>
                                        {{ $qr->expired_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                @if($qr->expired_at->isPast())
                                    <p class="text-xs text-red-500 mt-2 flex items-center justify-end">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Sudah kadaluarsa!
                                    </p>
                                @else
                                    <p class="text-xs text-green-500 mt-2 flex items-center justify-end">
                                        <i class="fas fa-clock mr-1"></i>
                                        Masih berlaku
                                    </p>
                                @endif
                            @else
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-500">Status</span>
                                    <span class="text-xs font-semibold text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Scan Count & Actions -->
                        <div class="flex justify-between items-center mt-3">
                            <div>
                                <p class="text-xs text-gray-500">Total Scan</p>
                                <p class="font-semibold text-gray-800">{{ $qr->scan_count }}x</p>
                            </div>
                            <div class="flex gap-1">
                                <a href="{{ route('admin.qrcodes.show', $qr) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 transition"
                                   title="Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.qrcodes.edit', $qr) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 p-1.5 rounded-lg hover:bg-yellow-50 transition"
                                   title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <a href="{{ route('admin.qrcodes.download', $qr) }}" 
                                   class="text-green-600 hover:text-green-800 p-1.5 rounded-lg hover:bg-green-50 transition"
                                   title="Download">
                                    <i class="fas fa-download text-sm"></i>
                                </a>
                                <button onclick="deleteSingle({{ $qr->id }})" 
                                        class="text-red-600 hover:text-red-800 p-1.5 rounded-lg hover:bg-red-50 transition"
                                        title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                                <form id="delete-form-{{ $qr->id }}" action="{{ route('admin.qrcodes.destroy', $qr->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500">Belum ada QR Code</p>
                    <a href="{{ route('admin.qrcodes.create') }}" class="inline-block mt-3 text-orange-600 hover:text-orange-700">
                        Buat QR sekarang
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($qrCodes->hasPages())
        <div class="p-4 border-t">
            {{ $qrCodes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteSingle(id) {
    Swal.fire({
        title: 'Hapus QR Code?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
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
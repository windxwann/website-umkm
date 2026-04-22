@extends('admin.layouts.app')

@section('title', 'Export QR Code')
@section('page-title', 'Export Data QR Code')

@section('content')
<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-download text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Export Data QR Code</h2>
                <p class="text-gray-500 text-sm">Export data QR code ke berbagai format file</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Total QR Code</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <i class="fas fa-qrcode text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Aktif</p>
                    <p class="text-2xl font-bold">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <i class="fas fa-check-circle text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs">Total Scan</p>
                    <p class="text-2xl font-bold">{{ $stats['total_scans'] ?? 0 }}</p>
                </div>
                <i class="fas fa-eye text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- CSV Export -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-csv text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">CSV</h3>
                <p class="text-gray-500 text-sm mb-4">Format CSV (Comma Separated Values)<br>Kompatibel dengan Excel, Google Sheets</p>
                <button onclick="exportData('csv')" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-download mr-2"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- Excel Export -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-excel text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Excel (XLS)</h3>
                <p class="text-gray-500 text-sm mb-4">Format Microsoft Excel<br>Langsung bisa dibuka di Excel</p>
                <button onclick="exportData('excel')" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-download mr-2"></i> Export Excel
                </button>
            </div>
        </div>

        <!-- PDF Export -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-pdf text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">PDF</h3>
                <p class="text-gray-500 text-sm mb-4">Format PDF<br>Mudah dicetak dan dibagikan</p>
                <button onclick="exportData('pdf')" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-download mr-2"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-filter text-orange-500 mr-2"></i>
                Filter Data
            </h3>
        </div>
        
        <div class="p-4">
            <form id="exportForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Status QR Code</label>
                        <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Dari Tanggal</label>
                        <input type="date" id="date_from" name="date_from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Sampai Tanggal</label>
                        <input type="date" id="date_to" name="date_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-500">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
            <div>
                <p class="text-blue-700 text-sm font-medium">Informasi Export</p>
                <p class="text-blue-600 text-xs mt-1">Data akan diexport sesuai filter yang dipilih. Kosongkan filter untuk export semua data.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function exportData(format) {
    const status = document.getElementById('status').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    let url = '';
    switch(format) {
        case 'csv':
            url = '{{ route("admin.qrcodes.export.csv") }}';
            break;
        case 'excel':
            url = '{{ route("admin.qrcodes.export.excel") }}';
            break;
        case 'pdf':
            url = '{{ route("admin.qrcodes.export.pdf") }}';
            break;
    }
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyiapkan file export',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Redirect to download
    window.location.href = url;
    
    // Close loading after redirect
    setTimeout(() => {
        Swal.close();
    }, 2000);
}

// Reset filters
function resetFilters() {
    document.getElementById('status').value = '';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    
    Swal.fire({
        icon: 'success',
        title: 'Filter Direset!',
        text: 'Semua filter telah direset',
        timer: 1500,
        showConfirmButton: false
    });
}
</script>
@endpush
@endsection
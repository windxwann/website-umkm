@extends('admin.layouts.app')

@section('title', 'Export Data QR')
@section('page-title', 'Export QR')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb & Header - Compact -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.qrcodes.index') }}" 
           class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none">Export Data</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Export Data QR Code ke berbagai format</p>
        </div>
    </div>

    <!-- Stats Bar - Compact -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-8">
        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600"><i data-lucide="qr-code" class="w-4 h-4"></i></div>
            <div>
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total QR</p>
                <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['total'] ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
            <div>
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Aktif</p>
                <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['active'] ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600"><i data-lucide="eye" class="w-4 h-4"></i></div>
            <div>
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total Scan</p>
                <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['total_scans'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Filter Section -->
        <div class="lg:col-span-1 bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-orange-600"></i>
                Filter Data
            </h3>
            <form id="exportForm" class="space-y-4">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Status QR</label>
                    <select id="status" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 cursor-pointer appearance-none">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Dari Tanggal</label>
                    <input type="date" id="date_from" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Sampai Tanggal</label>
                    <input type="date" id="date_to" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                </div>
            </form>
        </div>

        <!-- Export Options -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['format' => 'csv', 'label' => 'CSV', 'color' => 'bg-emerald-600', 'icon' => 'file-text'],
                ['format' => 'excel', 'label' => 'EXCEL', 'color' => 'bg-blue-600', 'icon' => 'table'],
                ['format' => 'pdf', 'label' => 'PDF', 'color' => 'bg-rose-600', 'icon' => 'file-bar-chart']
            ] as $option)
            <button onclick="exportData('{{ $option['format'] }}')"
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-lg transition-all text-center flex flex-col items-center gap-4 group">
                <div class="w-12 h-12 {{ $option['color'] }} text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i data-lucide="{{ $option['icon'] }}" class="w-6 h-6"></i>
                </div>
                <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ $option['label'] }}</span>
            </button>
            @endforeach
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
        case 'csv': url = '{{ route("admin.qrcodes.export.csv") }}'; break;
        case 'excel': url = '{{ route("admin.qrcodes.export.excel") }}'; break;
        case 'pdf': url = '{{ route("admin.qrcodes.export.pdf") }}'; break;
    }
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    if (params.toString()) url += '?' + params.toString();
    
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">PROSES EXPORT</span>',
        text: 'Menyiapkan berkas...',
        allowOutsideClick: false,
        borderRadius: '1.5rem',
        didOpen: () => { Swal.showLoading(); }
    });
    
    window.location.href = url;
    setTimeout(() => Swal.close(), 2000);
}
</script>
@endpush
@endsection

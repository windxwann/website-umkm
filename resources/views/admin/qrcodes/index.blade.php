@extends('admin.layouts.app')

@section('title', 'Manajemen QR Code')
@section('page-title', 'QR Codes')

@section('content')
<!-- Actions -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.qrcodes.create') }}" 
           class="flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat QR
        </a>
        <a href="{{ route('admin.qrcodes.export') }}" 
           class="flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export
        </a>
    </div>

    <form method="GET" action="{{ route('admin.qrcodes.index') }}" class="w-full lg:w-auto flex flex-wrap gap-2">
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Cari kode atau meja..." 
                   value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-3 bg-white border border-slate-100 rounded-2xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <div class="relative w-full sm:w-36">
            <i data-lucide="filter" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <select name="status" class="w-full pl-9 pr-8 py-3 bg-white border border-slate-100 rounded-2xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all appearance-none cursor-pointer">
                <option value="">Semua</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
    </form>
</div>

<!-- QR Grid -->
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($qrCodes as $qr)
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="p-6 bg-slate-50 rounded-t-3xl flex justify-center border-b border-slate-100">
            @if($qr->qr_image)
                <img src="{{ $qr->qr_image }}" class="w-24 h-24 rounded-2xl shadow-sm">
            @else
                <div class="w-24 h-24 bg-slate-200 rounded-2xl flex items-center justify-center text-slate-400">
                    <i data-lucide="qrcode" class="w-10 h-10"></i>
                </div>
            @endif
        </div>
        
        <div class="p-5 flex-grow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Meja</p>
                    <p class="text-sm font-black text-slate-900 tracking-tight">{{ $qr->meja ?? '-' }}</p>
                </div>
                <span class="inline-block px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest 
                    {{ $qr->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    {{ $qr->status }}
                </span>
            </div>
            
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode</p>
            <p class="font-mono text-[10px] font-bold text-slate-600 bg-slate-50 p-2.5 rounded-xl mb-4 break-all">{{ $qr->code }}</p>
            
            <div class="flex items-center justify-between text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-3.5 h-3.5"></i> {{ $qr->scan_count }}x Scan</span>
                @if($qr->is_permanent)
                    <span class="text-purple-600"><i data-lucide="infinity" class="w-3.5 h-3.5"></i></span>
                @elseif($qr->expired_at && $qr->expired_at->isPast())
                    <span class="text-rose-500"><i data-lucide="calendar-x" class="w-3.5 h-3.5"></i> Expired</span>
                @else
                    <span class="text-emerald-500"><i data-lucide="calendar-check" class="w-3.5 h-3.5"></i> Valid</span>
                @endif
            </div>
        </div>

        <div class="p-4 border-t border-slate-50 flex items-center justify-center gap-2">
            <a href="{{ route('admin.qrcodes.edit', $qr) }}" class="p-3 bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm" title="Edit"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></a>
            <a href="{{ route('admin.qrcodes.download', $qr) }}" class="p-3 bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm" title="Download"><i data-lucide="download" class="w-3.5 h-3.5"></i></a>
            <button onclick="deleteSingle({{ $qr->id }})" class="p-3 bg-slate-50 text-slate-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Hapus"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
            <form id="delete-form-{{ $qr->id }}" action="{{ route('admin.qrcodes.destroy', $qr->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center">
        <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Belum Ada QR Code</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteSingle(id) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-lg text-rose-600">HAPUS QR CODE?</span>',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, HAPUS',
        cancelButtonText: 'BATAL',
        customClass: { popup: 'rounded-3xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection

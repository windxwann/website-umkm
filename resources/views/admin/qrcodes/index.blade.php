@extends('admin.layouts.app')

@section('title', 'Manajemen QR Code')
@section('page-title', 'QR Codes')

@section('content')
<!-- Actions & Stats - Compact -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.qrcodes.create') }}" 
           class="flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-[1.02] transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat QR
        </a>
        <a href="{{ route('admin.qrcodes.export') }}" 
           class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export
        </a>
    </div>

    <form method="GET" action="{{ route('admin.qrcodes.index') }}" class="w-full lg:w-auto flex flex-wrap gap-2">
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Cari kode atau meja..." 
                   value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <div class="relative w-full sm:w-36">
            <i data-lucide="filter" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <select name="status" class="w-full pl-9 pr-8 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all appearance-none cursor-pointer">
                <option value="">Semua</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        @if(request('search') || request('status'))
        <a href="{{ route('admin.qrcodes.index') }}" 
           class="flex items-center justify-center p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all shrink-0 shadow-sm"
           title="Hapus Filter">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
        </a>
        @endif
    </form>
</div>

<!-- Stats Bar - Compact -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 mb-8">
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600"><i data-lucide="qr-code" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Aktif</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['active'] }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600"><i data-lucide="x-circle" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Nonaktif</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['inactive'] }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600"><i data-lucide="infinity" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Permanent</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['permanent'] }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600"><i data-lucide="eye" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Scan</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $stats['total_scans'] }}</p>
        </div>
    </div>
</div>

<!-- QR Grid - Compact Design -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
    @forelse($qrCodes as $qr)
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
        <div class="p-4 bg-slate-50 rounded-t-[2rem] flex justify-center border-b border-slate-100">
            @if($qr->qr_image)
                <img src="{{ $qr->qr_image }}" class="w-20 h-20 rounded-xl shadow-sm">
            @else
                <div class="w-20 h-20 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400">
                    <i data-lucide="qrcode" class="w-8 h-8"></i>
                </div>
            @endif
        </div>
        
        <div class="p-5 flex-1">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Meja</p>
                    <p class="text-sm font-black text-slate-900">{{ $qr->meja ?? '-' }}</p>
                </div>
                <span class="inline-block px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest 
                    {{ $qr->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    {{ $qr->status }}
                </span>
            </div>
            
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kode</p>
            <p class="font-mono text-[10px] font-black text-slate-700 bg-slate-50 p-2 rounded-lg mb-4 break-all">{{ $qr->code }}</p>
            
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500">
                <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-3 h-3"></i> {{ $qr->scan_count }}x Scan</span>
                @if($qr->is_permanent)
                    <span class="text-purple-600"><i data-lucide="infinity" class="w-3 h-3"></i></span>
                @elseif($qr->expired_at && $qr->expired_at->isPast())
                    <span class="text-rose-500"><i data-lucide="calendar-x" class="w-3 h-3"></i> Expired</span>
                @else
                    <span class="text-emerald-500"><i data-lucide="calendar-check" class="w-3 h-3"></i> Valid</span>
                @endif
            </div>
        </div>

        <div class="p-4 border-t border-slate-50 flex items-center justify-center gap-2">
            <a href="{{ route('admin.qrcodes.edit', $qr) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Edit"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></a>
            <a href="{{ route('admin.qrcodes.download', $qr) }}" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100" title="Download"><i data-lucide="download" class="w-3.5 h-3.5"></i></a>
            <button onclick="deleteSingle({{ $qr->id }})" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100" title="Hapus"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
            <form id="delete-form-{{ $qr->id }}" action="{{ route('admin.qrcodes.destroy', $qr->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
            <i data-lucide="qrcode" class="w-8 h-8 text-slate-200"></i>
        </div>
        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px]">Belum Ada QR Code</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteSingle(id) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl text-rose-600">HAPUS QR CODE?</span>',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, HAPUS',
        cancelButtonText: 'BATAL',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection

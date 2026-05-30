@extends('admin.layouts.app')

@section('title', 'Buat QR Code')
@section('page-title', 'QR Codes')

@section('content')
<div class="max-w-xl mx-auto">
    <!-- Breadcrumb & Header - Compact -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.qrcodes.index') }}" 
           class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none">Buat QR Baru</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Generate QR untuk Meja</p>
        </div>
    </div>

    <form action="{{ route('admin.qrcodes.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
            
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nomor Meja</label>
                <div class="relative group">
                    <i data-lucide="hash" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="text" name="meja" value="{{ old('meja') }}" placeholder="Contoh: 01"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Lokasi</label>
                <div class="relative group">
                    <i data-lucide="map-pin" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="text" name="nama_tempat" value="{{ old('nama_tempat') }}" placeholder="Contoh: Outdoor / VIP"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all resize-none" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                <i data-lucide="save" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                Simpan QR Code
            </button>
        </div>
    </form>
</div>
@endsection

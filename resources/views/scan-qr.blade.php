@extends('layouts.app')

@section('title', 'Scan QR Meja')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-orange-50 rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-qrcode text-3xl text-orange-600"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                Selamat Datang!
            </h2>
            <p class="mt-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                Scan QR Meja Untuk Memulai Pesanan
            </p>
        </div>

        <!-- Manual Form -->
        <form class="mt-8 space-y-6" action="{{ route('scan.qr.validate') }}" method="POST">
            @csrf
            <div>
                <label for="qr_code" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 text-center">Masukkan Kode Meja</label>
                <input id="qr_code" name="qr_code" type="text" required 
                    class="appearance-none rounded-2xl relative block w-full px-4 py-4 border border-slate-100 placeholder-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm text-center font-black tracking-widest uppercase bg-slate-50" 
                    placeholder="CONTOH: MEJA-01"
                    value="{{ old('qr_code') }}">
            </div>

            @if(session('error'))
            <div class="text-rose-500 text-[10px] font-black uppercase tracking-widest text-center p-3 bg-rose-50 rounded-xl border border-rose-100 flex items-center justify-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-slate-900 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-lg transition-all duration-300">
                    LIHAT MENU & PESAN
                </button>
            </div>
        </form>

        <!-- Help Info -->
        <div class="pt-4 text-center">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                Butuh bantuan? Hubungi Pelayan.
            </p>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.getElementById('qr_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    const qrForm = document.querySelector('form');
    const submitBtn = qrForm.querySelector('button[type="submit"]');

    qrForm.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    });
</script>
@endpush
@endsection

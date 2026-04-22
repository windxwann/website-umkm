@extends('layouts.app')

@section('title', 'Scan QR Meja')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-24 w-24 bg-orange-100 rounded-full flex items-center justify-center mb-6 animate-bounce">
                <i class="fas fa-qrcode text-4xl text-orange-600"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">
                Selamat Datang!
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Silakan scan QR Code yang tertempel di meja Anda menggunakan kamera HP untuk mulai memesan.
            </p>
        </div>

        <!-- Desktop/Laptop Notice -->
        <div class="hidden md:block bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md my-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-laptop text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Anda menggunakan Laptop/Desktop? <br> 
                    </p>
                </div>
            </div>
        </div>

        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500 uppercase tracking-widest text-xs font-bold">Silahkan Masukkan Code Meja</span>
            </div>
        </div>

        <!-- Manual Form -->
        <form class="mt-8 space-y-6" action="{{ route('scan.qr.validate') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm">
                <div>
                    <label for="qr_code" class="sr-only">Kode Meja</label>
                    <input id="qr_code" name="qr_code" type="text" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-4 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm text-center font-bold tracking-widest uppercase" 
                        placeholder="CONTOH: QR-MEJA-001"
                        value="{{ old('qr_code') }}">
                </div>
            </div>

            <!-- No more location fields needed -->

            @if(session('error'))
            <div class="text-red-500 text-sm p-3 bg-red-50 rounded-lg border border-red-100 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-lg transition-all transform hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-utensils text-orange-500 group-hover:text-orange-400"></i>
                    </span>
                    LIHAT MENU & PESAN
                </button>
            </div>
        </form>

        <!-- Help Info -->
        <div class="pt-6 text-center">
            <p class="text-xs text-gray-400">
                Butuh bantuan? Silakan hubungi pelayan kami di area restoran.
            </p>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('qr_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    const qrForm = document.querySelector('form');
    const submitBtn = qrForm.querySelector('button[type="submit"]');

    qrForm.addEventListener('submit', function(e) {
        // Simple submission, no geolocation check
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    });
</script>
@endpush
@endsection
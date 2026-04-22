@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto" 
     x-data="paymentHandler('{{ $order->order_number }}')" 
     x-init="init()">
    
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-2 text-center">Pembayaran</h1>
        <p class="text-gray-600 text-center mb-6">Pesanan #{{ $order->order_number }}</p>
        
        <div class="bg-orange-50 p-4 sm:p-6 rounded-lg mb-6">
            <p class="text-center">
                <span class="text-base sm:text-lg">Total Pembayaran:</span>
                <span class="text-xl sm:text-2xl font-bold text-orange-600 block">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </span>
            </p>
        </div>
        
        @if($order->payment_method === 'e_wallet')
            <!-- E-Wallet Payment -->
            <div class="text-center mb-6">
                <h2 class="font-semibold mb-3">Scan QRIS untuk Membayar</h2>
                <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-inner inline-block border-2 border-gray-100">
                    <!-- QRIS Image -->
                    @if(setting('qris_image'))
                        <img src="{{ asset('storage/'.setting('qris_image')) }}" alt="QRIS" class="w-48 sm:w-64 h-auto mx-auto rounded-lg">
                    @else
                        <div class="w-48 h-48 sm:w-64 sm:h-64 bg-gray-100 flex items-center justify-center rounded-lg">
                            <i class="fas fa-qrcode text-5xl sm:text-6xl text-gray-300"></i>
                        </div>
                        <p class="text-[10px] text-red-500 mt-2">QRIS belum diatur oleh Admin</p>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="font-bold text-gray-800">{{ setting('qris_merchant_name', 'Merchant QRIS') }}</p>
                    @if(setting('qris_nmid'))
                        <p class="text-xs text-gray-500">NMID: {{ setting('qris_nmid') }}</p>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-3">
                    Scan menggunakan aplikasi e-wallet Anda (GoPay, OVO, Dana, dll)
                </p>
            </div>
            
            <button @click="checkPayment($event)" 
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-xl font-bold hover:bg-orange-700 transition shadow-lg transform active:scale-95 mb-3">
                Saya Sudah Membayar
            </button>
            
        @elseif($order->payment_method === 'bank_transfer')
            <!-- Bank Transfer -->
            <div class="space-y-4 mb-6 text-left">
                <h2 class="font-semibold text-gray-700">Silakan Transfer ke:</h2>
                
                <div class="bg-white border-2 border-orange-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <span class="bg-orange-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mr-2">BANK</span>
                                <p class="font-bold text-gray-800">{{ setting('bank_name', 'Belum Diatur') }}</p>
                            </div>
                            <p class="text-lg sm:text-2xl font-black text-orange-600 tracking-wider">{{ setting('bank_account_number', '0000000000') }}</p>
                            <p class="text-xs sm:text-sm text-gray-500">a.n. {{ setting('bank_account_name', '-') }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ setting('bank_account_number', '') }}')" 
                                class="bg-orange-50 text-orange-600 p-3 sm:p-4 rounded-xl hover:bg-orange-100 transition active:scale-90 shrink-0">
                            <i class="fas fa-copy text-lg sm:text-xl"></i>
                        </button>
                    </div>
                </div>

                @if(setting('bank2_name'))
                <div class="bg-white border-2 border-gray-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <span class="bg-gray-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mr-2">BANK</span>
                                <p class="font-bold text-gray-800">{{ setting('bank2_name') }}</p>
                            </div>
                            <p class="text-2xl font-black text-gray-800 tracking-wider">{{ setting('bank2_account_number') }}</p>
                            <p class="text-sm text-gray-500">a.n. {{ setting('bank2_account_name') }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ setting('bank2_account_number', '') }}')" 
                                class="bg-gray-50 text-gray-600 p-4 rounded-xl hover:bg-gray-100 transition active:scale-90">
                            <i class="fas fa-copy text-xl"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>
            
            <button @click="checkPayment($event)"
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-xl font-bold hover:bg-orange-700 transition shadow-lg transform active:scale-95 mb-3">
                Saya Sudah Transfer
            </button>
            
        @elseif($order->payment_method === 'cashier')
            <!-- Cashier Payment -->
            <div class="text-center mb-6">
                <i class="fas fa-cash-register text-6xl text-orange-600 mb-4"></i>
                <h2 class="font-semibold text-lg mb-2">Bayar di Kasir</h2>
                <p class="text-gray-600">
                    Silakan lakukan pembayaran langsung ke kasir.
                    <br>Pesanan Anda akan diproses setelah pembayaran dikonfirmasi.
                </p>
            </div>
            
            <!-- Notification Area -->
            <div x-show="notification" 
                 x-text="notification" 
                 class="bg-green-100 text-green-700 p-4 rounded-xl mb-4 border border-green-200">
            </div>
            
            <div class="text-center text-sm text-gray-500 bg-gray-50 p-4 rounded-xl">
                <p>Nomor Pesanan: <strong>{{ $order->order_number }}</strong></p>
                <p>Sebutkan nomor pesanan saat membayar di kasir</p>
            </div>
            <button @click="checkPayment($event)"
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-xl font-bold hover:bg-orange-700 transition shadow-lg transform active:scale-95 mb-3">
                Saya Sudah Membayar
            </button>
            
        @endif

        <script>
            function paymentHandler(orderNumber) {
                return {
                    notification: '',
                    isNotified: false,
                    init() {
                        this.checkNotifications();
                        setInterval(() => this.checkNotifications(), 5000);
                    },
                    checkNotifications() {
                        fetch(`/api/v1/customer-notifications?order_number=${orderNumber}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    this.notification = data[0].message;
                                    // Mark as read
                                    fetch(`/api/v1/notifications/${data[0].id}/read`, {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    });
                                }
                            });
                    },
                    notifyAdmin() {
                        if (this.isNotified) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Konfirmasi',
                                text: 'Notifikasi sudah dikirim ke kasir. Mohon tunggu sebentar selagi kami memverifikasi pembayaran Anda.',
                                confirmButtonColor: '#ea580c'
                            });
                            return Promise.resolve({ success: true, alreadyNotified: true });
                        }

                        return fetch(`/api/v1/orders/${orderNumber}/confirm-payment-intent`, {
                            method: 'POST',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.success) {
                                this.isNotified = true;
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Konfirmasi Terkirim',
                                    text: 'Kasir telah diberitahu. Kami akan segera memverifikasi pembayaran Anda.',
                                    confirmButtonColor: '#ea580c'
                                });
                            }
                            return result;
                        });
                    },
                    checkPayment(e) {
                        const btn = e.target.closest('button');
                        const originalHtml = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Memverifikasi...';
                        
                        // Beri tahu admin bahwa user mengklaim sudah bayar
                        this.notifyAdmin().then(result => {
                            // Setelah notify admin (atau jika sudah), cek status pembayaran di DB
                            fetch(`/api/v1/orders/${orderNumber}/status`)
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success && result.data.payment_status === 'paid') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Pembayaran Diterima!',
                                            text: 'Terima kasih, pesanan Anda sedang diproses.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            window.location.href = `/order/${orderNumber}/success`;
                                        });
                                    } else {
                                        // Jika belum lunas, tampilkan pesan menunggu konfirmasi
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Menunggu Konfirmasi',
                                            text: 'Pembayaran Anda sedang dalam antrean verifikasi kasir. Mohon tunggu sebentar atau tunjukkan bukti transaksi jika diperlukan.',
                                            confirmButtonColor: '#ea580c'
                                        });
                                        btn.disabled = false;
                                        btn.innerHTML = originalHtml;
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    btn.disabled = false;
                                    btn.innerHTML = originalHtml;
                                });
                        });
                    }
                }
            }
            
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Nomor rekening telah disalin.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }
        </script>
        
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
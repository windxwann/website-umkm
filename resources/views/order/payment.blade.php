@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12" 
     x-data="paymentHandler('{{ $order->order_number }}')" 
     x-init="init()">
    
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 md:p-12">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2 text-center">Pembayaran</h1>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center mb-10">Pesanan #{{ $order->order_number }}</p>
        
        <div class="bg-orange-50 rounded-[2rem] p-8 text-center mb-10 border border-orange-100">
            <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2 block">Total yang harus dibayar</span>
            <span class="text-4xl font-black text-orange-600">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
        </div>
        
        @if($order->payment_method === 'e_wallet')
            <!-- E-Wallet Payment -->
            <div class="text-center mb-10">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Scan QRIS Untuk Membayar</h2>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm inline-block border border-slate-100">
                    <!-- QRIS Image -->
                    @if(setting('qris_image'))
                        <img src="{{ asset('storage/'.setting('qris_image')) }}" alt="QRIS" class="w-56 sm:w-72 h-auto mx-auto rounded-2xl">
                    @else
                        <div class="w-56 h-56 sm:w-72 sm:h-72 bg-slate-50 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200">
                            <i class="fas fa-qrcode text-5xl text-slate-200 mb-4"></i>
                            <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">QRIS belum diatur</p>
                        </div>
                    @endif
                </div>
                <div class="mt-8">
                    <p class="text-lg font-black text-slate-900 tracking-tight">{{ setting('qris_merchant_name', 'Merchant QRIS') }}</p>
                    @if(setting('qris_nmid'))
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">NMID: {{ setting('qris_nmid') }}</p>
                    @endif
                </div>
                <p class="text-xs font-medium text-slate-500 mt-6 max-w-sm mx-auto">
                    Scan menggunakan aplikasi e-wallet Anda (GoPay, OVO, Dana, dll)
                </p>
            </div>
            
            <button @click="checkPayment($event)" 
                    class="w-full bg-slate-900 text-white py-5 px-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10 mb-4">
                SAYA SUDAH MEMBAYAR
            </button>
            
        @elseif($order->payment_method === 'bank_transfer')
            <!-- Bank Transfer -->
            <div class="space-y-6 mb-10 text-left">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest text-center mb-6">Silakan Transfer Ke</h2>
                
                <div class="bg-slate-50 border border-slate-100 rounded-[2rem] p-8 relative overflow-hidden group">
                    <div class="relative z-10 flex justify-between items-center">
                        <div class="space-y-4">
                            <div class="inline-flex items-center bg-orange-600 text-white text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                                {{ setting('bank_name', 'Bank') }}
                            </div>
                            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-wider">{{ setting('bank_account_number', '0000000000') }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">a.n. {{ setting('bank_account_name', '-') }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ setting('bank_account_number', '') }}')" 
                                class="bg-white text-slate-900 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm hover:bg-orange-600 hover:text-white transition-all active:scale-90 shrink-0 border border-slate-100">
                            <i class="fas fa-copy text-xl"></i>
                        </button>
                    </div>
                </div>

                @if(setting('bank2_name'))
                <div class="bg-slate-50 border border-slate-100 rounded-[2rem] p-8 relative overflow-hidden group">
                    <div class="relative z-10 flex justify-between items-center">
                        <div class="space-y-4">
                            <div class="inline-flex items-center bg-slate-400 text-white text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                                {{ setting('bank2_name') }}
                            </div>
                            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-wider">{{ setting('bank2_account_number') }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">a.n. {{ setting('bank2_account_name') }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ setting('bank2_account_number', '') }}')" 
                                class="bg-white text-slate-900 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm hover:bg-orange-600 hover:text-white transition-all active:scale-90 border border-slate-100">
                            <i class="fas fa-copy text-xl"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>
            
            <button @click="checkPayment($event)"
                    class="w-full bg-slate-900 text-white py-5 px-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10 mb-4">
                SAYA SUDAH TRANSFER
            </button>
            
        @elseif($order->payment_method === 'cashier')
            <!-- Cashier Payment -->
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-orange-50 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-cash-register text-3xl text-orange-600"></i>
                </div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Bayar Di Kasir</h2>
                <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-xs mx-auto">
                    Silakan tunjukkan nomor pesanan Anda ke kasir untuk menyelesaikan pembayaran.
                </p>
            </div>
            
            <!-- Notification Area -->
            <div x-show="notification" 
                 x-text="notification" 
                 class="bg-emerald-50 text-emerald-700 p-5 rounded-2xl mb-6 border border-emerald-100 text-xs font-bold text-center uppercase tracking-wide">
            </div>
            
            <div class="text-center mb-10 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Pesanan</p>
                <p class="text-xl font-black text-slate-900">#{{ $order->order_number }}</p>
            </div>

            <button @click="checkPayment($event)"
                    class="w-full bg-slate-900 text-white py-5 px-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10 mb-4">
                SAYA SUDAH MEMBAYAR
            </button>
        @endif

        <div class="text-center pt-4">
            <a href="{{ route('home') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali Ke Beranda
            </a>
        </div>
    </div>
</div>

@push('scripts')
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
                        confirmButtonColor: '#0f172a'
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
                            confirmButtonColor: '#0f172a'
                        });
                    }
                    return result;
                });
            },
            checkPayment(e) {
                const btn = e.target.closest('button');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> VERIFIKASI...';
                
                this.notifyAdmin().then(result => {
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
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Menunggu Konfirmasi',
                                    text: 'Pembayaran Anda sedang dalam antrean verifikasi kasir. Mohon tunggu sebentar.',
                                    confirmButtonColor: '#0f172a'
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
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                icon: 'success',
                title: 'Tersalin'
            });
        });
    }
</script>
@endpush
@endsection

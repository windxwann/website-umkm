@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 md:p-12 text-center">
        <!-- Success Icon -->
        <div class="w-24 h-24 bg-emerald-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 animate-bounce">
            <i class="fas fa-check text-4xl text-emerald-600"></i>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-3">Pesanan Berhasil!</h1>
        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-10">
            Nomor Pesanan: <span class="text-slate-900">#{{ $order->order_number }}</span>
        </p>
        
        <!-- Details Card -->
        <div class="bg-slate-50 rounded-[2rem] border border-slate-100 p-8 text-left mb-10">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Detail Pesanan</h2>
            
            <div class="space-y-4 mb-6">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-700">{{ $item->product_name }} <span class="text-slate-400 font-medium">x{{ $item->quantity }}</span></span>
                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="border-t border-slate-200 border-dashed pt-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total Pembayaran</span>
                    <span class="text-2xl font-black text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="pt-4 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-tight">
                        <i class="fas fa-credit-card text-orange-600 w-4"></i>
                        <span>{{ $order->payment_method === 'e_wallet' ? 'E-Wallet (QRIS)' : ($order->payment_method === 'cashier' ? 'Bayar di Kasir' : 'Transfer Bank') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-tight {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        <i class="fas {{ $order->payment_status === 'paid' ? 'fa-check-circle' : 'fa-clock' }} w-4"></i>
                        <span>{{ $order->payment_status === 'paid' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col gap-4">
            @if($order->payment_status !== 'paid')
                <a href="{{ route('order.payment', $order->order_number) }}" 
                   class="w-full bg-slate-900 text-white py-4 px-8 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10">
                    Selesaikan Pembayaran
                </a>
            @endif
            
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('menu') }}" class="bg-white border border-slate-200 text-slate-600 py-4 px-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all">
                    <i class="fas fa-utensils mr-2"></i> Pesan Lagi
                </a>
                <a href="{{ route('home') }}" class="bg-white border border-slate-200 text-slate-600 py-4 px-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pastikan cart kosong di halaman sukses
    localStorage.removeItem('restaurant_cart');
    sessionStorage.setItem('order_success', 'true');
</script>
@endpush
@endsection

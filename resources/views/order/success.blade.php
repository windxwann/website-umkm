@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-4xl text-green-600"></i>
        </div>
        
        <h1 class="text-3xl font-bold mb-2">Pesanan Berhasil!</h1>
        <p class="text-gray-600 mb-6">Nomor Pesanan Anda: <strong>{{ $order->order_number }}</strong></p>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
            <h2 class="font-semibold mb-3">Detail Pesanan</h2>
            
            <div class="space-y-2 mb-4">
                @foreach($order->items as $item)
                <div class="flex justify-between">
                    <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="border-t pt-3 space-y-2">
                <!-- Packaging fee removed -->
                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span class="text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Delivery address removed -->
            
            <div class="mt-4 p-3 bg-blue-50 rounded">
                <p class="text-sm">
                    <span class="font-semibold">Metode Pembayaran:</span> 
                    {{ $order->payment_method === 'e_wallet' ? 'E-Wallet (QRIS)' : ($order->payment_method === 'cashier' ? 'Bayar di Kasir' : 'Transfer Bank') }}
                </p>
                <p class="text-sm mt-1">
                    <span class="font-semibold">Status:</span> 
                    {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran' }}
                </p>
            </div>
        </div>
        
        @if($order->payment_status !== 'paid')
            <a href="{{ route('order.payment', $order->order_number) }}" 
               class="inline-block bg-orange-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-orange-700 transition mb-3">
                Lanjutkan Pembayaran
            </a>
        @endif
        
        <div class="space-x-4">
            <a href="{{ route('menu') }}" class="text-orange-600 hover:text-orange-700">
                <i class="fas fa-utensils mr-1"></i> Pesan Lagi
            </a>
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-700">
                <i class="fas fa-home mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
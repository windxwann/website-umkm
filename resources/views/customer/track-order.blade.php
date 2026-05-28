@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-orange-600 mb-2">Lacak Pesanan</h1>
        <p class="text-gray-600">Lihat status pesanan Anda</p>
    </div>

    <!-- Order Info Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-6 py-4">
            <div class="flex justify-between items-center text-white">
                <div>
                    <p class="text-sm opacity-90">Nomor Pesanan</p>
                    <p class="text-xl font-bold">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Tanggal</p>
                    <p class="font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Customer Info -->
            <div class="mb-6 pb-4 border-b">
                <h3 class="font-semibold text-gray-800 mb-2">Informasi Pelanggan</h3>
                <p class="text-gray-600">{{ $order->customer_name }}</p>
                @if($order->customer_phone)
                    <p class="text-gray-500 text-sm">{{ $order->customer_phone }}</p>
                @endif
                <p class="text-gray-500 text-sm mt-1">
                    <i class="fas {{ $order->order_type === 'offline' ? 'fa-store' : 'fa-truck' }} mr-1"></i>
                    {{ $order->order_type === 'offline' ? 'Makan di Tempat' : 'Bawa Pulang' }}
                </p>
            </div>

            <!-- Status Timeline -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status Pesanan</h3>
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <!-- Status Waiting -->
                    <div class="relative flex items-start mb-6">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border-2 {{ $order->order_status == 'waiting' ? 'border-orange-600' : 'border-green-500' }} flex items-center justify-center z-10">
                            @if($order->order_status == 'waiting')
                                <div class="w-3 h-3 bg-orange-600 rounded-full animate-pulse"></div>
                            @else
                                <i class="fas fa-check text-green-500 text-sm"></i>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-semibold {{ $order->order_status == 'waiting' ? 'text-orange-600' : 'text-gray-700' }}">
                                Pesanan Diterima
                            </h4>
                            <p class="text-sm text-gray-500">Pesanan telah diterima dan menunggu diproses</p>
                            @if($order->created_at)
                                <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status Processed -->
                    <div class="relative flex items-start mb-6">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border-2 {{ $order->order_status == 'processed' || $order->order_status == 'completed' ? 'border-green-500' : 'border-gray-300' }} flex items-center justify-center z-10">
                            @if($order->order_status == 'processed' || $order->order_status == 'completed')
                                <i class="fas fa-check text-green-500 text-sm"></i>
                            @elseif($order->order_status == 'waiting')
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-semibold {{ $order->order_status == 'processed' || $order->order_status == 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                                Sedang Dimasak
                            </h4>
                            <p class="text-sm {{ $order->order_status == 'processed' || $order->order_status == 'completed' ? 'text-gray-600' : 'text-gray-400' }}">
                                Pesanan sedang diproses di dapur
                            </p>
                            @if($order->updated_at && ($order->order_status == 'processed' || $order->order_status == 'completed'))
                                <p class="text-xs text-gray-400 mt-1">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status Completed -->
                    <div class="relative flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border-2 {{ $order->order_status == 'completed' ? 'border-green-500' : 'border-gray-300' }} flex items-center justify-center z-10">
                            @if($order->order_status == 'completed')
                                <i class="fas fa-check text-green-500 text-sm"></i>
                            @else
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-semibold {{ $order->order_status == 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                                Pesanan Selesai
                            </h4>
                            <p class="text-sm {{ $order->order_status == 'completed' ? 'text-gray-600' : 'text-gray-400' }}">
                                Pesanan telah selesai dan siap diambil
                            </p>
                            @if($order->updated_at && $order->order_status == 'completed')
                                <p class="text-xs text-gray-400 mt-1">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Item Pesanan</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b last:border-0">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-orange-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center pt-3 mt-2 border-t">
                    <span class="font-semibold text-gray-800">Total</span>
                    <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status Pembayaran</span>
                    @if($order->payment_status === 'paid')
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Lunas</span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
                    @endif
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="text-sm font-medium">
                        @if($order->payment_method === 'cashier')
                            Bayar di Kasir
                        @elseif($order->payment_method === 'e_wallet')
                            E-Wallet
                        @else
                            Transfer Bank
                        @endif
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex gap-3">
                @if($order->payment_status !== 'paid')
                <a href="{{ route('order.payment', $order->order_number) }}" 
                   class="flex-1 bg-orange-600 text-white text-center py-2 rounded-lg hover:bg-orange-700 transition">
                    <i class="fas fa-credit-card mr-2"></i>Bayar Sekarang
                </a>
                @endif
                <a href="{{ route('menu') }}" 
                   class="flex-1 bg-gray-500 text-white text-center py-2 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-utensils mr-2"></i>Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
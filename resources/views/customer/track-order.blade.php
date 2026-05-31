@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Lacak Pesanan</h1>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-orange-600"></i>
            Pantau status hidangan Anda secara real-time
        </p>
    </div>

    <!-- Order Info Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Pesanan</p>
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter">#{{ $order->order_number }}</h2>
            </div>
            <div class="md:text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Pesanan</p>
                <p class="text-sm font-bold text-slate-700">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        
        <div class="p-8">
            <!-- Status Timeline -->
            <div class="mb-12">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">Status Progres</h3>
                <div class="space-y-8 relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-slate-100"></div>
                    
                    @php
                        $statuses = [
                            ['key' => 'waiting', 'label' => 'Pesanan Diterima', 'desc' => 'Dapur telah menerima pesanan Anda.', 'icon' => 'fa-receipt'],
                            ['key' => 'processed', 'label' => 'Sedang Dimasak', 'desc' => 'Koki kami sedang menyiapkan hidangan Anda.', 'icon' => 'fa-fire'],
                            ['key' => 'completed', 'label' => 'Siap Disajikan', 'desc' => 'Hidangan Anda sudah siap dan sedang diantar.', 'icon' => 'fa-utensils'],
                        ];
                        $currentStatusIndex = 0;
                        foreach($statuses as $index => $s) {
                            if($order->order_status == $s['key']) $currentStatusIndex = $index;
                        }
                        // Special handling for completed
                        if($order->order_status == 'completed') $currentStatusIndex = 2;
                        elseif($order->order_status == 'processed') $currentStatusIndex = 1;
                    @endphp

                    @foreach($statuses as $index => $s)
                    @php
                        $isActive = $index <= $currentStatusIndex;
                        $isCurrent = $order->order_status == $s['key'];
                    @endphp
                    <div class="relative flex items-start group">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl border-2 flex items-center justify-center z-10 transition-all duration-500 {{ $isActive ? 'bg-orange-600 border-orange-600 shadow-lg shadow-orange-600/20' : 'bg-white border-slate-100' }}">
                            <i class="fas {{ $s['icon'] }} text-xs {{ $isActive ? 'text-white' : 'text-slate-300' }}"></i>
                        </div>
                        <div class="ml-6 flex-1">
                            <h4 class="text-sm font-black uppercase tracking-tight {{ $isActive ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ $s['label'] }}
                                @if($isCurrent)
                                    <span class="ml-2 inline-flex h-2 w-2 rounded-full bg-orange-500 animate-ping"></span>
                                @endif
                            </h4>
                            <p class="text-xs font-medium mt-1 {{ $isActive ? 'text-slate-500' : 'text-slate-300' }}">{{ $s['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Items Summary -->
            <div class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Rincian Menu</h3>
                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-700">{{ $item->product_name }} <span class="text-slate-400 font-medium">x{{ $item->quantity }}</span></span>
                        <span class="text-sm font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-slate-200 border-dashed pt-6 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total Bayar</span>
                    <span class="text-xl font-black text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="p-4 bg-white rounded-2xl border border-slate-100">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Metode Bayar</p>
                    <p class="text-[10px] font-black text-slate-900 uppercase">{{ $order->payment_method === 'cashier' ? 'Kasir' : ($order->payment_method === 'e_wallet' ? 'QRIS' : 'Transfer') }}</p>
                </div>
                <div class="p-4 bg-white rounded-2xl border border-slate-100">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Status Bayar</p>
                    <p class="text-[10px] font-black {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }} uppercase">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Pending' }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 flex flex-col md:flex-row gap-4">
                @if($order->payment_status !== 'paid')
                <a href="{{ route('order.payment', $order->order_number) }}" 
                   class="flex-1 bg-slate-900 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10">
                    <i class="fas fa-credit-card mr-2 text-orange-500"></i> Bayar Sekarang
                </a>
                @endif
                <a href="{{ route('menu') }}" 
                   class="flex-1 bg-white border border-slate-200 text-slate-600 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all">
                    <i class="fas fa-utensils mr-2"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>

    <!-- Help Info -->
    <div class="text-center">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Pesanan akan diproses otomatis oleh sistem dapur kami.</p>
    </div>
</div>
@endsection

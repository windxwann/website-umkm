<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @media print {
            @page { margin: 0; }
            body { padding: 0.5cm; background: white !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #e2e8f0 !important; }
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
        }

        .invoice-card {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        @media print {
            .invoice-card {
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body class="text-slate-800">

    <!-- Action Bar -->
    <div class="max-w-[800px] mx-auto mt-8 px-4 no-print flex justify-between items-center">
        <a href="{{ url()->previous() }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-bold text-xs uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 px-6 py-2.5 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-105 transition-all">
            <i data-lucide="printer" class="w-4 h-4"></i>
            Cetak Struk
        </button>
    </div>

    <div class="invoice-card print-border">
        <!-- Header Section -->
        <div class="p-8 sm:p-12 border-b border-slate-50 bg-slate-50/30">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-orange-600 flex items-center justify-center p-2">
                            <i data-lucide="utensils" class="text-white w-full h-full"></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tighter uppercase">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h2>
                    </div>
                    <div class="space-y-1 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                        <p>{{ setting('address', 'Jl. Raya Contoh No. 123, Indonesia') }}</p>
                        <p>WA: {{ setting('phone', '0812-3456-7890') }}</p>
                        <p>{{ setting('email', 'halo@dapoerjiemas.com') }}</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">STRUK</h1>
                    <p class="text-xs font-black text-orange-600 tracking-[0.2em] uppercase mb-4">#{{ $order->order_number }}</p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        {{ $order->created_at->format('d M Y - H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Bar -->
        <div class="px-8 sm:px-12 py-6 bg-white border-b border-slate-50 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pelanggan</p>
                <p class="text-xs font-black text-slate-900 uppercase">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Meja / Lokasi</p>
                <p class="text-xs font-black text-slate-900 uppercase">#{{ $order->qr_code ?? $order->table_number ?? 'Take Away' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Tipe Order</p>
                <p class="text-xs font-black text-slate-900 uppercase">{{ str_replace('_', ' ', $order->order_type) }}</p>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pembayaran</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status == 'paid' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    <p class="text-xs font-black text-slate-900 uppercase">{{ $order->payment_status }} ({{ $order->payment_method }})</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-8 sm:p-12">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-900">
                        <th class="py-4 text-left text-[10px] font-black text-slate-900 uppercase tracking-widest">Item Menu</th>
                        <th class="py-4 text-center text-[10px] font-black text-slate-900 uppercase tracking-widest">Qty</th>
                        <th class="py-4 text-right text-[10px] font-black text-slate-900 uppercase tracking-widest">Harga</th>
                        <th class="py-4 text-right text-[10px] font-black text-slate-900 uppercase tracking-widest">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="py-5">
                            <p class="text-sm font-black text-slate-900 tracking-tight leading-none mb-1">{{ $item->product_name }}</p>
                            @if($item->notes)
                                <p class="text-[9px] font-bold text-slate-400 uppercase italic leading-none">"{{ $item->notes }}"</p>
                            @endif
                        </td>
                        <td class="py-5 text-center text-sm font-black text-slate-900">{{ $item->quantity }}</td>
                        <td class="py-5 text-right text-sm font-medium text-slate-500">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-5 text-right text-sm font-black text-slate-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="mt-8 flex justify-end">
                <div class="w-full sm:w-72 space-y-3">
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <span>Subtotal</span>
                        <span class="text-slate-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->packaging_fee > 0)
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <span>Biaya Packing</span>
                        <span class="text-slate-900">Rp{{ number_format($order->packaging_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if(isset($order->discount) && $order->discount > 0)
                    <div class="flex justify-between text-[10px] font-bold text-rose-500 uppercase tracking-widest">
                        <span>Potongan Harga</span>
                        <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="pt-4 border-t-2 border-slate-900 flex justify-between items-end">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total Bayar</span>
                        <span class="text-2xl font-black text-orange-600 leading-none tracking-tighter">Rp{{ number_format($order->total_amount + ($order->packaging_fee ?? 0) - ($order->discount ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="px-8 sm:px-12 py-10 bg-slate-50/50 border-t border-slate-50 text-center">
            <p class="text-xs font-black text-slate-900 uppercase tracking-widest mb-2">Terima Kasih Atas Kunjungannya!</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-relaxed">
                Struk ini adalah bukti pembayaran yang sah.<br>
                Silakan datang kembali di {{ setting('restaurant_name', 'Dapoer Jiemas') }}.
            </p>
            
            <div class="mt-10 pt-10 border-t border-slate-100 flex justify-between items-center text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">
                <div class="flex flex-col items-center">
                    <p class="mb-12">PELANGGAN</p>
                    <p>( {{ substr($order->customer_name, 0, 15) }} )</p>
                </div>
                <div class="w-16 h-16 opacity-10">
                    <i data-lucide="utensils" class="w-full h-full text-slate-900"></i>
                </div>
                <div class="flex flex-col items-center">
                    <p class="mb-12">KASIR</p>
                    <p>( {{ strtoupper(auth()->user()->name) }} )</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center pb-10 text-[9px] font-black text-slate-300 uppercase tracking-[0.4em] no-print">
        &copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Jiemas') }} Digital System
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

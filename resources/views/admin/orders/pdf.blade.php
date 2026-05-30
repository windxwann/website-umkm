<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #334155;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
        }
        .header {
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -1px;
        }
        .company-name {
            font-size: 14px;
            font-weight: 900;
            color: #ea580c;
            margin-bottom: 5px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .status-waiting { background: #fef3c7; color: #92400e; }
        .status-processed { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #f8fafc;
            color: #64748b;
            padding: 10px;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals-table td {
            padding: 5px 0;
        }
        .grand-total {
            font-size: 16px;
            font-weight: 900;
            color: #ea580c;
            border-top: 2px solid #f1f5f9;
            padding-top: 10px !important;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table style="border: none;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="invoice-title">INVOICE</div>
                        <div style="font-weight: bold; margin-top: 5px;">#{{ $order->order_number }}</div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge status-{{ $order->order_status }}">
                                {{ $order->order_status }}
                            </span>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        <div class="company-name">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</div>
                        <div style="color: #64748b; line-height: 1.5;">
                            {{ setting('address', 'Jl. Raya Contoh No. 123') }}<br>
                            Telp: {{ setting('phone', '0812-3456-7890') }}<br>
                            Email: {{ setting('email', 'halo@dapoerjiemas.com') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <table style="margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div style="font-weight: 900; text-transform: uppercase; font-size: 9px; color: #94a3b8; margin-bottom: 5px;">Tagihan Untuk:</div>
                    <div style="font-weight: bold; font-size: 12px; color: #0f172a;">{{ $order->customer_name }}</div>
                    <div style="margin-top: 5px; color: #64748b;">
                        {{ $order->customer_phone ?? '-' }}<br>
                        Meja: #{{ $order->qr_code ?? $order->table_number ?? 'Take Away' }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <div style="font-weight: 900; text-transform: uppercase; font-size: 9px; color: #94a3b8; margin-bottom: 5px;">Rincian Pesanan:</div>
                    <div style="color: #64748b;">
                        Tanggal: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong><br>
                        Metode: <strong>{{ strtoupper($order->payment_method) }}</strong><br>
                        Status: <strong>{{ strtoupper($order->payment_status) }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Menu</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 20%;" class="text-right">Harga</th>
                    <th style="width: 20%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $item->product_name }}</div>
                        @if($item->notes)
                            <div style="font-size: 8px; color: #94a3b8; font-style: italic; margin-top: 2px;">"{{ $item->notes }}"</div>
                        @endif
                    </td>
                    <td class="text-center" style="font-weight: bold;">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <table style="width: 250px; margin-left: auto;" class="totals-table">
                <tr>
                    <td style="color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 9px;">Subtotal</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                @if(isset($order->packaging_fee) && $order->packaging_fee > 0)
                <tr>
                    <td style="color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 9px;">Biaya Packing</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->packaging_fee, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if(isset($order->discount) && $order->discount > 0)
                <tr>
                    <td style="color: #ef4444; font-weight: bold; text-transform: uppercase; font-size: 9px;">Potongan</td>
                    <td class="text-right" style="font-weight: bold; color: #ef4444;">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="grand-total uppercase tracking-widest" style="font-size: 10px;">Total Bayar</td>
                    <td class="text-right grand-total">Rp {{ number_format($order->grand_total ?? ($order->total_amount + ($order->packaging_fee ?? 0) - ($order->discount ?? 0)), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($order->notes)
        <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #e2e8f0;">
            <div style="font-weight: 900; text-transform: uppercase; font-size: 9px; color: #94a3b8; margin-bottom: 5px;">Catatan Order:</div>
            <div style="color: #475569; font-style: italic;">{{ $order->notes }}</div>
        </div>
        @endif

        <div class="footer">
            <p style="font-weight: 900; color: #0f172a; margin-bottom: 5px;">TERIMA KASIH TELAH BERBELANJA!</p>
            <p>Invoice ini dihasilkan secara otomatis dan merupakan bukti pembayaran yang sah.</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Jiemas') }} Digital System</p>
        </div>
    </div>
</body>
</html>

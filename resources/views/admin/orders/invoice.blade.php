<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .invoice-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-title {
            color: #4e73df;
            font-size: 2.5rem;
            font-weight: bold;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .table th {
            background-color: #f8f9fc;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
            .print-btn {
                display: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn btn-primary print-btn no-print">
        <i class="fas fa-print"></i> Print / Save PDF
    </button>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="invoice-title">INVOICE</h1>
                    <p class="text-muted">No. {{ $order->order_number }}</p>
                </div>
                <div class="col-6 company-info">
                    <h3>{{ config('app.name') }}</h3>
                    <p>
                        {{ config('app.address', 'Jl. Contoh No. 123') }}<br>
                        Telp: {{ config('app.phone', '(021) 1234567') }}<br>
                        Email: {{ config('app.email', 'info@example.com') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h5>Bill To:</h5>
                <p>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->customer_email }}<br>
                    {{ $order->customer_phone }}<br>
                    {{ $order->shipping_address ?? '-' }}
                </p>
            </div>
            <div class="col-6 text-end">
                <h5>Invoice Details:</h5>
                <p>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Status:</strong> 
                    @php
                        $statusClass = match($order->status) {
                            'pending' => 'status-pending',
                            'processing' => 'status-processing',
                            'completed' => 'status-completed',
                            'cancelled' => 'status-cancelled',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span><br>
                    <strong>Metode Pembayaran:</strong> {{ $order->payment_method ?? '-' }}
                </p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                    <td class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td colspan="4" class="text-end"><strong>Diskon</strong></td>
                    <td class="text-end">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($order->shipping_cost > 0)
                <tr>
                    <td colspan="4" class="text-end"><strong>Ongkos Kirim</strong></td>
                    <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" class="text-end"><strong>Grand Total</strong></td>
                    <td class="text-end"><strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        @if($order->notes)
        <div class="mt-4">
            <h6>Catatan:</h6>
            <p class="text-muted">{{ $order->notes }}</p>
        </div>
        @endif

        <div class="row mt-5">
            <div class="col-6">
                <p class="text-muted">Penerima,</p>
                <br><br>
                <p>(____________________)</p>
            </div>
            <div class="col-6 text-end">
                <p class="text-muted">Hormat kami,</p>
                <br><br>
                <p>(____________________)</p>
            </div>
        </div>

        <div class="text-center mt-4 text-muted small no-print">
            <p>Terima kasih telah berbelanja di {{ config('app.name') }}</p>
            <p>Invoice ini adalah bukti pembayaran yang sah</p>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
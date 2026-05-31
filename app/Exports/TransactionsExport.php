<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Nama Pelanggan',
            'Meja',
            'Total',
            'Metode Bayar',
            'Status Order',
            'Status Pembayaran',
            'Waktu'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->customer_name,
            $order->qrCodeRelation->meja ?? $order->qr_code,
            $order->total_amount,
            strtoupper($order->payment_method),
            strtoupper($order->order_status),
            strtoupper($order->payment_status),
            $order->created_at->format('d/m/Y H:i'),
        ];
    }
}

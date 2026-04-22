<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $message;
    public $type;
    public $time;

    public function __construct(Order $order, $type = 'cashier')
    {
        $this->order = $order;
        $this->type = $type;
        $this->time = now()->format('H:i');
        
        $methodText = [
            'cashier' => 'Kasir',
            'e_wallet' => 'QRIS',
            'bank_transfer' => 'Transfer Bank'
        ];
        
        $method = $methodText[$order->payment_method] ?? $order->payment_method;
        $this->message = "Pelanggan memilih pembayaran {$method} untuk pesanan #{$order->order_number}";
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'payment-notification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->customer_name,
            'payment_method' => $this->order->payment_method,
            'total_amount' => $this->order->total_amount,
            'message' => $this->message,
            'type' => 'payment',
            'time' => $this->time,
            'url' => route('admin.orders.show', $this->order)
        ];
    }
}
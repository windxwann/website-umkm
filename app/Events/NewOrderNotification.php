<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $message;
    public $time;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->message = "Pesanan baru #{$order->order_number} dari {$order->customer_name}";
        $this->time = now()->format('H:i');
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'new-order';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->customer_name,
            'total_amount' => $this->order->total_amount,
            'message' => $this->message,
            'time' => $this->time,
            'url' => route('admin.orders.show', $this->order)
        ];
    }
}
<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('customer.' . $this->order->session_id);
    }

    public function broadcastWith()
    {
        return [
            'order_number' => $this->order->order_number,
            'status' => 'completed',
            'message' => 'Pesanan Anda telah selesai'
        ];
    }

    public function broadcastAs()
    {
        return 'order.completed';
    }
}
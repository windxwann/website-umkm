<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerSessionReset implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionId;
    public $message;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->message = 'Pesanan Anda telah selesai. Terima kasih!';
    }

    public function broadcastOn()
    {
        return new Channel('customer.' . $this->sessionId);
    }

    public function broadcastAs()
    {
        return 'session.reset';
    }
    
    public function broadcastWith()
    {
        return [
            'session_id' => $this->sessionId,
            'message' => $this->message,
            'time' => now()->toDateTimeString()
        ];
    }
}
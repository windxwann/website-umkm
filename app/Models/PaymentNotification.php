<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentNotification extends Model
{
    protected $fillable = [
        'order_id', 'type', 'message', 'is_read', 'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // 🔥 KONSTANTA TYPE
    const TYPE_CASHIER = 'cashier';
    const TYPE_CUSTOMER = 'customer';

    const TYPES = [
        self::TYPE_CASHIER => 'Kasir',
        self::TYPE_CUSTOMER => 'Pelanggan',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function isForCashier(): bool
    {
        return $this->type === self::TYPE_CASHIER;
    }

    public function isForCustomer(): bool
    {
        return $this->type === self::TYPE_CUSTOMER;
    }

}
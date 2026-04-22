<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $order_number
 * @property string|null $qr_code
 * @property string|null $session_id
 * @property string $customer_name
 * @property string|null $customer_phone
 * @property string $order_type
 * @property string $payment_method
 * @property string $payment_status
 * @property string $order_status
 * @property float $total_amount
 * @property float $paid_amount
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $payment_due_at
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\PaymentNotification|null $paymentNotification
 * @property-read \App\Models\QrCode|null $qrCodeRelation
 */
class Order extends Model
{
    /**
     * NAMA TABEL: Menggunakan 'orders' (bukan orders_backup)
     */
    protected $table = 'orders';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;
    
    protected $keyType = 'int';
    
    public $timestamps = true;

    protected $fillable = [
        'order_number',
        'qr_code',
        'session_id',
        'customer_name',
        'customer_phone',
        'order_type',
        'payment_method',
        'payment_status',
        'order_status',
        'is_archived_for_table',
        'total_amount',
        'paid_amount',
        'notes',
        'delivery_address',
        'packaging_fee',
        'payment_due_at',
        'paid_at',
        'completed_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'packaging_fee' => 'decimal:2',
        'is_archived_for_table' => 'boolean',
        'payment_due_at' => 'datetime',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'payment_status' => 'pending',
        'order_status' => 'waiting',
        'paid_amount' => 0
    ];

    /**
     * KONSTANTA STATUS ORDER
     * CATATAN: Sesuaikan dengan constraint di database
     * Database saat ini hanya memiliki: waiting, processed, completed, cancelled
     * READY belum ada di database, jadi jangan digunakan dulu
     */
    const STATUS_WAITING = 'waiting';
    const STATUS_PROCESSED = 'processed';
    // const STATUS_READY = 'ready'; // DIKOMENTAR karena belum ada di database
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * KONSTANTA STATUS PEMBAYARAN
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';

    /**
     * DAFTAR STATUS YANG VALID (Sesuai database)
     */
    const ORDER_STATUSES = [
        self::STATUS_WAITING => 'Menunggu',
        self::STATUS_PROCESSED => 'Diproses',
        // self::STATUS_READY => 'Siap', // DIKOMENTAR
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan'
    ];

    const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_PAID => 'Lunas',
        self::PAYMENT_FAILED => 'Gagal'
    ];

    /**
     * Relasi ke OrderItem
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Relasi ke PaymentNotification
     */
    public function paymentNotification(): HasOne
    {
        return $this->hasOne(PaymentNotification::class, 'order_id', 'id');
    }

    /**
     * Relasi ke QrCode
     */
    public function qrCodeRelation(): BelongsTo
    {
        return $this->belongsTo(QrCode::class, 'qr_code', 'code');
    }

    /**
     * Generate nomor order unik
     */
    public static function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'ORD-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk order yang masih aktif (belum selesai)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('order_status', [
            self::STATUS_WAITING, 
            self::STATUS_PROCESSED,
            // self::STATUS_READY // DIKOMENTAR
        ]);
    }

    /**
     * Scope untuk order yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('order_status', self::STATUS_COMPLETED);
    }

    /**
     * Scope untuk order yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    /**
     * Scope untuk order yang siap diantar/diambil
     * CATATAN: Sementara pakai 'processed' karena 'ready' belum ada
     */
    public function scopeReady($query)
    {
        // Gunakan 'processed' sebagai pengganti 'ready' sementara
        return $query->where('order_status', self::STATUS_PROCESSED);
    }

    /**
     * Scope untuk order yang sedang diproses
     */
    public function scopeProcessed($query)
    {
        return $query->where('order_status', self::STATUS_PROCESSED);
    }

    /**
     * Scope untuk order yang menunggu
     */
    public function scopeWaiting($query)
    {
        return $query->where('order_status', self::STATUS_WAITING);
    }

    /**
     * Cek apakah order masih aktif (belum selesai)
     */
    public function isActive(): bool
    {
        return in_array($this->order_status, [
            self::STATUS_WAITING,
            self::STATUS_PROCESSED,
            // self::STATUS_READY // DIKOMENTAR
        ]);
    }

    /**
     * Cek apakah order sudah siap diantar
     * CATATAN: Sementara pakai 'processed' karena 'ready' belum ada
     */
    public function isReady(): bool
    {
        // Sementara return false atau cek 'processed'
        return $this->order_status === self::STATUS_PROCESSED;
    }

    /**
     * Cek apakah order sedang diproses
     */
    public function isProcessed(): bool
    {
        return $this->order_status === self::STATUS_PROCESSED;
    }

    /**
     * Cek apakah order sedang menunggu
     */
    public function isWaiting(): bool
    {
        return $this->order_status === self::STATUS_WAITING;
    }

    /**
     * Cek apakah order sudah selesai
     */
    public function isCompleted(): bool
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }

    /**
     * Cek apakah order dibatalkan
     */
    public function isCancelled(): bool
    {
        return $this->order_status === self::STATUS_CANCELLED;
    }

    /**
     * Cek apakah pembayaran sudah lunas
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Cek apakah pembayaran pending
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }

    /**
     * Hitung kembalian (jika bayar tunai)
     */
    public function getChangeAttribute(): float
    {
        if ($this->payment_method === 'cashier' && $this->paid_amount > $this->total_amount) {
            return $this->paid_amount - $this->total_amount;
        }
        return 0;
    }

    /**
     * Get status badge class untuk tampilan
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->order_status) {
            self::STATUS_WAITING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSED => 'bg-blue-100 text-blue-800',
            // self::STATUS_READY => 'bg-purple-100 text-purple-800', // DIKOMENTAR
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status text dalam bahasa Indonesia
     */
    public function getStatusTextAttribute(): string
    {
        return self::ORDER_STATUSES[$this->order_status] ?? $this->order_status;
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentBadgeClass(): string
    {
        return $this->payment_status === self::PAYMENT_PAID 
            ? 'bg-green-100 text-green-800' 
            : 'bg-yellow-100 text-yellow-800';
    }

    /**
     * Get payment status text
     */
    public function getPaymentStatusTextAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Format total amount ke rupiah
     */
    public function getTotalAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Format paid amount ke rupiah
     */
    public function getPaidAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    /**
     * Get waktu order dalam format yang mudah dibaca
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Cek apakah bisa diubah ke status tertentu
     * Disesuaikan dengan status yang tersedia di database
     */
    public function canTransitionTo(string $newStatus): bool
    {
        // Transisi tanpa 'ready'
        $transitions = [
            self::STATUS_WAITING => [self::STATUS_PROCESSED, self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_PROCESSED => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_COMPLETED => [],
            self::STATUS_CANCELLED => []
        ];

        return in_array($newStatus, $transitions[$this->order_status] ?? []);
    }

    /**
     * Boot method untuk event model
     */
    protected static function boot()
    {
        parent::boot();

        // Event saat membuat order baru
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });

        // Event saat order diupdate
        static::updating(function ($order) {
            // Log perubahan status
            if ($order->isDirty('order_status')) {
                $oldStatus = $order->getOriginal('order_status');
                $newStatus = $order->order_status;
                
                \Log::info("Order status changed: {$order->order_number} from {$oldStatus} to {$newStatus}", [
                    'order_id' => $order->id,
                    'session_id' => $order->session_id
                ]);

                // Validasi transisi status
                if (!$order->canTransitionTo($newStatus)) {
                    \Log::warning("Invalid status transition attempted", [
                        'order_id' => $order->id,
                        'from' => $oldStatus,
                        'to' => $newStatus
                    ]);
                }
            }
        });
    }
} 
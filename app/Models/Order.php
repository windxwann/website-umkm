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
 * @property int|null $table_id
 * @property \Illuminate\Support\Carbon|null $payment_due_at
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\PaymentNotification|null $paymentNotification
 * @property-read \App\Models\QrCode|null $qrCodeRelation
 * @property-read \App\Models\Table|null $table
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
        'table_id', // Tambahkan table_id
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
     * Cek apakah order bisa berpindah ke status baru
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $currentStatus = $this->order_status;
        
        // Jika status sama, izinkan (no-op)
        if ($currentStatus === $newStatus) {
            return true;
        }

        // Status akhir tidak bisa diubah lagi
        if (in_array($currentStatus, [self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            return false;
        }

        // Logika transisi
        switch ($newStatus) {
            case self::STATUS_PROCESSED:
                return $currentStatus === self::STATUS_WAITING;
                
            case 'ready':
                return $currentStatus === self::STATUS_PROCESSED;

            case self::STATUS_COMPLETED:
                // Bisa selesai dari waiting, processed, atau ready
                return in_array($currentStatus, [self::STATUS_WAITING, self::STATUS_PROCESSED, 'ready']);
                
            case self::STATUS_CANCELLED:
                // Bisa dibatalkan jika belum selesai
                return in_array($currentStatus, [self::STATUS_WAITING, self::STATUS_PROCESSED, 'ready']);
                
            default:
                return false;
        }
    }

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
     * Relasi ke Table
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
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
     * Accessor untuk mendapatkan nomor meja dengan format yang lebih baik
     */
    public function getTableNumberAttribute()
    {
        if (!$this->table_id) {
            return null;
        }
        return $this->table->table_number ?? null;
    }

    /**
     * Accessor untuk mendapatkan lokasi meja (indoor/outdoor)
     */
    public function getTableLocationAttribute()
    {
        if (!$this->table_id) {
            return null;
        }
        return $this->table->location ?? null;
    }

    /**
     * Accessor untuk mendapatkan display meja yang lengkap
     */
    public function getTableDisplayAttribute(): object
    {
        if (!$this->table_id || !$this->table) {
            return (object) [
                'name' => '-',
                'location' => null,
                'location_name' => '-',
                'icon' => 'fa-chair',
                'badge_class' => 'bg-gray-100 text-gray-600',
                'full_display' => '-'
            ];
        }

        $location = $this->table->location ?? 'indoor';
        $locationName = $location === 'indoor' ? 'Indoor' : 'Outdoor';
        $icon = $location === 'indoor' ? 'fa-building' : 'fa-tree';
        $badgeClass = $location === 'indoor' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
        
        $fullDisplay = $this->table->table_number . ' (' . $locationName . ')';

        return (object) [
            'name' => $this->table->table_number,
            'location' => $location,
            'location_name' => $locationName,
            'icon' => $icon,
            'badge_class' => $badgeClass,
            'full_display' => $fullDisplay
        ];
    }

    /**
     * Cek apakah order dine-in (di meja)
     */
    public function isDineIn(): bool
    {
        return !is_null($this->table_id);
    }

    /**
     * Scope untuk order dine-in
     */
    public function scopeDineIn($query)
    {
        return $query->whereNotNull('table_id');
    }

    /**
     * Scope berdasarkan lokasi meja
     */
    public function scopeByTableLocation($query, $location)
    {
        return $query->whereHas('table', function($q) use ($location) {
            $q->where('location', $location);
        });
    }

    /**
     * Scope untuk order indoor
     */
    public function scopeIndoor($query)
    {
        return $this->scopeByTableLocation($query, 'indoor');
    }

    /**
     * Scope untuk order outdoor
     */
    public function scopeOutdoor($query)
    {
        return $this->scopeByTableLocation($query, 'outdoor');
    }

    /**
     * Get deskripsi lengkap lokasi
     */
    public function getLocationDescriptionAttribute(): string
    {
        if ($this->table_id && $this->table) {
            $location = $this->table->location ?? 'indoor';
            $locationName = $location === 'indoor' ? 'Indoor' : 'Outdoor';
            return $this->table->table_number . ' - ' . $locationName;
        }
        
        if ($this->qr_code) {
            // Coba ambil dari relasi QrCode
            if ($this->qrCodeRelation) {
                return $this->qrCodeRelation->meja ?? $this->qr_code;
            }
            return $this->qr_code;
        }

        return '-';
    }

    /**
     * Get icon untuk lokasi
     */
    public function getLocationIconAttribute(): string
    {
        if (!$this->table_id || !$this->table) {
            return 'fa-chair';
        }
        
        $location = $this->table->location ?? 'indoor';
        return $location === 'indoor' ? 'fa-building' : 'fa-tree';
    }

    /**
     * Get badge class untuk lokasi
     */
    public function getLocationBadgeClassAttribute(): string
    {
        if (!$this->table_id || !$this->table) {
            return 'bg-gray-100 text-gray-600';
        }
        
        $location = $this->table->location ?? 'indoor';
        return $location === 'indoor' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
    }

    /**
     * Scope untuk order berdasarkan rentang tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk order berdasarkan metode pembayaran
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope untuk order berdasarkan status pembayaran
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope untuk order berdasarkan status order
     */
    public function scopeByOrderStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    /**
     * Hitung total item dalam order
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get daftar produk yang dipesan
     */
    public function getProductListAttribute(): string
    {
        $products = $this->items->map(function($item) {
            return $item->product->name . ' (' . $item->quantity . 'x)';
        });
        
        return $products->implode(', ');
    }

    /**
     * Scope untuk mencari order berdasarkan nomor order atau nama customer
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('order_number', 'like', '%' . $search . '%')
                     ->orWhere('customer_name', 'like', '%' . $search . '%');
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
            
            // Set default order_type
            if (empty($order->order_type)) {
                $order->order_type = 'dine_in';
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
            
            // Log perubahan pembayaran
            if ($order->isDirty('payment_status')) {
                $oldStatus = $order->getOriginal('payment_status');
                $newStatus = $order->payment_status;
                
                \Log::info("Payment status changed: {$order->order_number} from {$oldStatus} to {$newStatus}", [
                    'order_id' => $order->id
                ]);
            }
        });
        
        // Event setelah order dibuat
        static::created(function ($order) {
            \Log::info("New order created: {$order->order_number}", [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'order_type' => $order->order_type,
                'table_id' => $order->table_id
            ]);
        });
    }
}
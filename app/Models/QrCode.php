<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'code', 'meja', 'nama_tempat', 'status', 'expired_at', 
        'is_permanent', 'scan_count', 'last_scanned_at', 'notes',
        'current_session_id', 'session_expires_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'is_permanent' => 'boolean',
        'session_expires_at' => 'datetime'
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->expired_at && $this->expired_at->isPast()) {
            return false;
        }
        return true;
    }

    public function incrementScanCount()
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'qr_code', 'code');
    }
}
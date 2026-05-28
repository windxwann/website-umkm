<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'table_number',
        'location', // indoor, outdoor
        'capacity',
        'is_active',
        'qr_code'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer'
    ];

    /**
     * Relasi ke orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id', 'id');
    }

    /**
     * Scope untuk meja yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan lokasi
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope untuk meja indoor
     */
    public function scopeIndoor($query)
    {
        return $query->where('location', 'indoor');
    }

    /**
     * Scope untuk meja outdoor
     */
    public function scopeOutdoor($query)
    {
        return $query->where('location', 'outdoor');
    }

    /**
     * Get display text
     */
    public function getDisplayAttribute(): string
    {
        $location = $this->location === 'indoor' ? 'Indoor' : 'Outdoor';
        return $this->table_number . ' (' . $location . ')';
    }

    /**
     * Get badge class untuk lokasi
     */
    public function getLocationBadgeClassAttribute(): string
    {
        return $this->location === 'indoor' 
            ? 'bg-green-100 text-green-700' 
            : 'bg-blue-100 text-blue-700';
    }

    /**
     * Get icon untuk lokasi
     */
    public function getLocationIconAttribute(): string
    {
        return $this->location === 'indoor' ? 'fa-building' : 'fa-tree';
    }
}
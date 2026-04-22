<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value'];
    public $timestamps = false;
    
    /**
     * Remove JSON cast as we are handling types manually in get/set
     * to avoid double encoding or string escaping issues.
     */
    protected $casts = [];
    
    /**
     * Get setting value
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = $setting->value;
        
        // Handle boolean strings
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        
        // Handle numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }
        
        return $value;
    }
    
    /**
     * Set setting value
     */
    public static function set($key, $value)
    {
        // Convert boolean to string for storage
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    /**
     * Get all settings as array
     */
    public static function getAll()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
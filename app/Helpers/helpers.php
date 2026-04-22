<?php

if (!function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                \App\Models\Setting::set($k, $v);
                Cache::forget('setting.' . $k); // Hapus cache spesifik pencarian per key
            }
            Cache::forget('app_settings');
            return true;
        }

        return Cache::remember('setting.' . $key, 3600, function () use ($key, $default) {
            return \App\Models\Setting::get($key, $default);
        });
    }
}
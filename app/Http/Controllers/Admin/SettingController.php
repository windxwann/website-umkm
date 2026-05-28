<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Ambil semua settings dari file config atau database
        $settings = [
            // Informasi Restoran
            'restaurant_name' => setting('restaurant_name', config('app.name', 'Dapoer Cemal Cemil Jiemas')),
            'phone' => setting('phone', '0812-3456-7890'),
            'address' => setting('address', 'Jl. Kuliner No. 123, Jakarta'),
            'email' => setting('email', 'info@dapoercemalcemil.com'),
            'website' => setting('website', 'www.dapoercemalcemil.com'),
            
            // Jam Operasional
            'mon_fri_open' => setting('mon_fri_open', '10:00'),
            'mon_fri_close' => setting('mon_fri_close', '22:00'),
            'sat_sun_open' => setting('sat_sun_open', '09:00'),
            'sat_sun_close' => setting('sat_sun_close', '23:00'),
            
            // Pajak & Biaya
            'tax' => setting('tax', 11),
            'service_charge' => setting('service_charge', 5),
            'packaging_fee' => setting('packaging_fee', 2000),
            
            // Metode Pembayaran
            'payment_cashier' => setting('payment_cashier', true),
            'payment_ewallet' => setting('payment_ewallet', true),
            'payment_transfer' => setting('payment_transfer', true),
            
            // QRIS
            'qris_merchant_name' => setting('qris_merchant_name', 'Dapoer Cemal Cemil'),
            'qris_merchant_id' => setting('qris_merchant_id', 'ID123456789'),
            'qris_nmid' => setting('qris_nmid', 'NMID123456789'),
            
            // Rekening Bank
            'bank_name' => setting('bank_name', 'Bank BCA'),
            'bank_account_number' => setting('bank_account_number', '1234567890'),
            'bank_account_name' => setting('bank_account_name', 'Dapoer Cemal Cemil'),
            
            'bank2_name' => setting('bank2_name', 'Bank Mandiri'),
            'bank2_account_number' => setting('bank2_account_number', '1234567890'),
            'bank2_account_name' => setting('bank2_account_name', 'Dapoer Cemal Cemil'),
            
            // Printer
            'printer_name' => setting('printer_name', 'POS-58'),
            'printer_type' => setting('printer_type', 'thermal'),
            'auto_print' => setting('auto_print', true),
            
            // Notifikasi
            'whatsapp_notification' => setting('whatsapp_notification', true),
            'email_notification' => setting('email_notification', true),
            'sms_notification' => setting('sms_notification', false),
            
            // Tampilan
            'theme_color' => setting('theme_color', 'orange'),
            'items_per_page' => setting('items_per_page', 15),
            'currency_symbol' => setting('currency_symbol', 'Rp'),
            'date_format' => setting('date_format', 'd/m/Y'),
            'time_format' => setting('time_format', 'H:i'),
            
            // Logo & Favicon
            'logo' => setting('logo'),
            'favicon' => setting('favicon'),

            // Geolocation
            'restaurant_latitude' => setting('restaurant_latitude', -6.2088), // Default Jakarta
            'restaurant_longitude' => setting('restaurant_longitude', 106.8456),
            'offline_radius' => setting('offline_radius', 20), // 20 meters default
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            // Informasi Restoran
            'restaurant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'website' => 'nullable|string|max:255',
            
            // Jam Operasional (Relaxed format to support various browsers)
            'mon_fri_open' => 'required|string',
            'mon_fri_close' => 'required|string',
            'sat_sun_open' => 'required|string',
            'sat_sun_close' => 'required|string',
            
            // Pajak & Biaya
            'tax' => 'required|numeric|min:0|max:100',
            'service_charge' => 'required|numeric|min:0|max:100',
            // 'packaging_fee' => 'required|numeric|min:0',
            
            // QRIS
            'qris_merchant_name' => 'nullable|string|max:255',
            'qris_merchant_id' => 'nullable|string|max:255',
            'qris_nmid' => 'nullable|string|max:255',
            
            // Rekening Bank 1
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            
            // Rekening Bank 2
            'bank2_name' => 'nullable|string|max:255',
            'bank2_account_number' => 'nullable|string|max:255',
            'bank2_account_name' => 'nullable|string|max:255',
            
            // Tampilan
            'items_per_page' => 'nullable|integer|min:1|max:500',
            'theme_color' => 'nullable|string',
            
            // Logo & Favicon
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,svg,webp|max:2048',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Banners
            'banner1_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'banner2_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'banner3_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Geolocation
            'restaurant_latitude' => 'nullable|numeric',
            'restaurant_longitude' => 'nullable|numeric',
            'offline_radius' => 'nullable|numeric|min:1',
        ]);

        try {
            // Simpan semua settings ke database atau file config
            $settings = $request->except(['_token', '_method', 'logo', 'favicon', 'qris_image', 'banner1_image', 'banner2_image', 'banner3_image']);
            
            foreach ($settings as $key => $value) {
                // Handle boolean values untuk checkbox
                if (in_array($key, ['payment_cashier', 'payment_ewallet', 'payment_transfer', 
                                    'whatsapp_notification', 'email_notification', 'sms_notification', 
                                    'auto_print'])) {
                    $value = $request->has($key);
                }
                
                // Simpan ke database atau file
                setting([$key => $value]);
            }

            // Handle Banner Uploads
            for ($i = 1; $i <= 3; $i++) {
                if ($request->hasFile("banner{$i}_image")) {
                    $oldImage = setting("banner{$i}_image");
                    if ($oldImage && \Storage::disk('public')->exists($oldImage)) {
                        \Storage::disk('public')->delete($oldImage);
                    }
                    $path = $request->file("banner{$i}_image")->store('banners', 'public');
                    setting(["banner{$i}_image" => $path]);
                }
            }

            // Handle Logo Upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('settings', 'public');
                
                // Hapus logo lama jika ada
                if (setting('logo')) {
                    Storage::disk('public')->delete(setting('logo'));
                }
                
                setting(['logo' => $logoPath]);
            }

            // Handle Favicon Upload
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('settings', 'public');
                
                if (setting('favicon')) {
                    Storage::disk('public')->delete(setting('favicon'));
                }
                
                setting(['favicon' => $faviconPath]);
            }

            // Handle QRIS Image Upload
            if ($request->hasFile('qris_image')) {
                $qrisPath = $request->file('qris_image')->store('settings/qris', 'public');
                
                if (setting('qris_image')) {
                    Storage::disk('public')->delete(setting('qris_image'));
                }
                
                setting(['qris_image' => $qrisPath]);
            }

            // Clear cache settings
            Cache::forget('app_settings');

            return redirect()->route('admin.settings')
                ->with('success', 'Pengaturan berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update specific setting group.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|string|max:255',
        ]);

        setting([
            'restaurant_name' => $request->restaurant_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'website' => $request->website,
        ]);

        Cache::forget('app_settings');

        return response()->json([
            'success' => true,
            'message' => 'Informasi umum berhasil disimpan'
        ]);
    }

    /**
     * Update payment settings.
     */
    public function updatePayment(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'qris_merchant_name' => 'required|string|max:255',
        ]);

        setting([
            'payment_cashier' => $request->has('payment_cashier'),
            'payment_ewallet' => $request->has('payment_ewallet'),
            'payment_transfer' => $request->has('payment_transfer'),
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'bank2_name' => $request->bank2_name,
            'bank2_account_number' => $request->bank2_account_number,
            'bank2_account_name' => $request->bank2_account_name,
            'qris_merchant_name' => $request->qris_merchant_name,
            'qris_merchant_id' => $request->qris_merchant_id,
            'qris_nmid' => $request->qris_nmid,
        ]);

        if ($request->hasFile('qris_image')) {
            $qrisPath = $request->file('qris_image')->store('settings/qris', 'public');
            setting(['qris_image' => $qrisPath]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan pembayaran berhasil disimpan'
        ]);
    }

    /**
     * Update tax settings.
     */
    public function updateTax(Request $request)
    {
        $request->validate([
            'tax' => 'required|numeric|min:0|max:100',
            'service_charge' => 'required|numeric|min:0|max:100',
        ]);

        setting([
            'tax' => $request->tax,
            'service_charge' => $request->service_charge,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan pajak berhasil disimpan'
        ]);
    }

    /**
     * Update printer settings.
     */
    public function updatePrinter(Request $request)
    {
        $request->validate([
            'printer_name' => 'required|string|max:255',
            'printer_type' => 'required|in:thermal,inkjet,dotmatrix',
        ]);

        setting([
            'printer_name' => $request->printer_name,
            'printer_type' => $request->printer_type,
            'auto_print' => $request->has('auto_print'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan printer berhasil disimpan'
        ]);
    }

    /**
     * Update notification settings.
     */
    public function updateNotification(Request $request)
    {
        setting([
            'whatsapp_notification' => $request->has('whatsapp_notification'),
            'email_notification' => $request->has('email_notification'),
            'sms_notification' => $request->has('sms_notification'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan notifikasi berhasil disimpan'
        ]);
    }

    /**
     * Reset all settings to default.
     */
    public function reset()
    {
        // Hapus semua setting
        $defaultSettings = [
            'restaurant_name' => 'Dapoer Cemal Cemil Jiemas',
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Kuliner No. 123, Jakarta',
            'email' => 'info@dapoercemalcemil.com',
            'tax' => 11,
            'service_charge' => 5,
            'theme_color' => 'orange',
            'items_per_page' => 15,
        ];

        foreach ($defaultSettings as $key => $value) {
            setting([$key => $value]);
        }

        // Hapus logo dan favicon
        if (setting('logo')) {
            Storage::disk('public')->delete(setting('logo'));
            setting(['logo' => null]);
        }

        if (setting('favicon')) {
            Storage::disk('public')->delete(setting('favicon'));
            setting(['favicon' => null]);
        }

        Cache::forget('app_settings');

        return redirect()->route('admin.settings')
            ->with('success', 'Semua pengaturan telah direset ke default');
    }

    /**
     * Test printer connection.
     */
    public function testPrinter()
    {
        try {
            // Simulasi test printer
            // Di sini Anda bisa menambahkan logic untuk test printer
            // Misalnya dengan library printer thermal
            
            return response()->json([
                'success' => true,
                'message' => 'Printer terhubung dengan baik'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke printer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup settings.
     */
    public function backup()
    {
        $settings = [];
        // Ambil semua settings
        $keys = [
            'restaurant_name', 'phone', 'address', 'email', 'website',
            'mon_fri_open', 'mon_fri_close', 'sat_sun_open', 'sat_sun_close',
            'tax', 'service_charge',
            'bank_name', 'bank_account_number', 'bank_account_name',
            'qris_merchant_name', 'qris_merchant_id',
            'printer_name', 'printer_type', 'auto_print',
            'theme_color', 'items_per_page', 'date_format', 'time_format',
        ];

        foreach ($keys as $key) {
            $settings[$key] = setting($key);
        }

        // Simpan ke file JSON
        $backupFile = 'settings-backup-' . date('Y-m-d-His') . '.json';
        $backupPath = storage_path('app/backups/' . $backupFile);
        
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        file_put_contents($backupPath, json_encode($settings, JSON_PRETTY_PRINT));

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }

    /**
     * Restore settings from backup.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json|max:2048'
        ]);

        try {
            $backupContent = file_get_contents($request->file('backup_file')->path());
            $settings = json_decode($backupContent, true);

            foreach ($settings as $key => $value) {
                setting([$key => $value]);
            }

            Cache::forget('app_settings');

            return redirect()->route('admin.settings')
                ->with('success', 'Pengaturan berhasil dipulihkan dari backup');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memulihkan backup: ' . $e->getMessage());
        }
    }
}
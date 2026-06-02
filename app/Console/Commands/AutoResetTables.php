<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\CustomerSessionReset;

class AutoResetTables extends Command
{
    protected $signature = 'tables:auto-reset';
    protected $description = 'Reset meja otomatis yang tidak aktif lebih dari X jam';

    public function handle()
    {
        //  AUTO RESET DINONAKTIFKAN UNTUK TEST: Set ke 9999 jam
        $inactiveHours = 9999;
        $cutoffDate = Carbon::now()->subHours($inactiveHours);

        // Cari QR yang ada sesi aktif dengan lockForUpdate untuk mencegah race condition
        $qrCodes = QrCode::whereNotNull('current_session_id')->lockForUpdate()->get();

        $resetCount = 0;

        foreach ($qrCodes as $qr) {
            // Cek pesanan aktif di meja ini
            $hasActiveOrders = Order::where('qr_code', $qr->code)
                ->whereIn('order_status', ['waiting', 'processed'])
                ->exists();

            // Jika tidak ada order aktif, dan sudah lama tidak ada aktivitas (misal kita anggap 
            // session_expires_at sebagai acuan aktivitas, atau tambahkan kolom last_activity_at)
            // Di sini kita gunakan session_expires_at jika ada, atau sekadar safety check.
            // Sebagai pengaman tambahan, kita cek apakah semua order sudah diarsipkan.
            
            if (!$hasActiveOrders) {
                // Reset meja
                try {
                    DB::beginTransaction();
                    
                    // Cari order yang masih punya session_id untuk di-broadcast logout
                    $ordersWithSession = Order::where('qr_code', $qr->code)
                        ->whereNotNull('session_id')
                        ->get();

                    foreach ($ordersWithSession as $order) {
                        try {
                            event(new CustomerSessionReset($order->session_id));
                        } catch (\Exception $e) {
                            Log::warning("Gagal broadcast reset session untuk order {$order->order_number}: " . $e->getMessage());
                        }
                    }

                    // Reset session di QrCode
                    $qr->update([
                        'current_session_id' => null,
                        'session_expires_at' => null
                    ]);
                    
                    Log::info("Meja {$qr->meja} di-reset otomatis karena tidak ada aktivitas.");
                    
                    DB::commit();
                    $resetCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Gagal reset otomatis meja {$qr->meja}: " . $e->getMessage());
                }
            }
        }

        $this->info("Berhasil mereset {$resetCount} meja secara otomatis.");
        return Command::SUCCESS;
    }
}

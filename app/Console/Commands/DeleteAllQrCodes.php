<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrCode;

class DeleteAllQrCodes extends Command
{
    protected $signature = 'qrcode:delete-all';
    protected $description = 'Delete all QR codes from database';

    public function handle()
    {
        $count = QrCode::count();
        
        if ($this->confirm("Are you sure you want to delete all $count QR codes?")) {
            QrCode::truncate();
            $this->info("✅ Successfully deleted all $count QR codes!");
        } else {
            $this->info("❌ Operation cancelled.");
        }
    }
}
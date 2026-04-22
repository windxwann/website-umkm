<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupTableArchives extends Command
{
    protected $signature = 'orders:cleanup-table-archives {days=30}';
    protected $description = 'Hapus data order yang sudah diarsipkan lebih dari X hari (untuk database)';

    public function handle()
    {
        $days = $this->argument('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $deleted = Order::where('is_archived_for_table', true)
            ->where('completed_at', '<', $cutoffDate)
            ->delete();
        
        $this->info("Deleted {$deleted} archived orders older than {$days} days");
        
        return Command::SUCCESS;
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            // Tambahkan kolom yang hilang
            if (!Schema::hasColumn('qr_codes', 'nama_tempat')) {
                $table->string('nama_tempat')->nullable()->after('meja');
            }
            
            if (!Schema::hasColumn('qr_codes', 'notes')) {
                $table->text('notes')->nullable()->after('is_permanent');
            }
            
            if (!Schema::hasColumn('qr_codes', 'scan_count')) {
                $table->integer('scan_count')->default(0)->after('notes');
            }
            
            if (!Schema::hasColumn('qr_codes', 'last_scanned_at')) {
                $table->timestamp('last_scanned_at')->nullable()->after('scan_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn(['nama_tempat', 'notes', 'scan_count', 'last_scanned_at']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambah
            if (!Schema::hasColumn('orders', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('order_number');
            }
            
            if (!Schema::hasColumn('orders', 'session_id')) {
                $table->string('session_id')->nullable()->after('qr_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'session_id']);
        });
    }
};
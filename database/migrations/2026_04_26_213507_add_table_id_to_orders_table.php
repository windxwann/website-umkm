<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cek dulu apakah kolom table_id sudah ada
            if (!Schema::hasColumn('orders', 'table_id')) {
                $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'table_id')) {
                $table->dropForeign(['table_id']);
                $table->dropColumn('table_id');
            }
        });
    }
};
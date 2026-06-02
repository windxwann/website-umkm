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
        Schema::table('categories', function (Blueprint $table) {
            //  CEK DULU APAKAH KOLOM SUDAH ADA
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            //  CEK APAKAH KOLOM ADA SEBELUM DIHAPUS
            if (Schema::hasColumn('categories', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
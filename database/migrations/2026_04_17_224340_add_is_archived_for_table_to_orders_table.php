<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsArchivedForTableToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_archived_for_table')->default(false)
                  ->after('order_status')
                  ->comment('Menandakan apakah order sudah diarsipkan untuk tampilan meja');
            $table->timestamp('completed_at')->nullable()
                  ->after('paid_at')
                  ->comment('Waktu pesanan selesai (pembeli pergi)');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_archived_for_table', 'completed_at']);
        });
    }
}
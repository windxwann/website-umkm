<?php
// database/migrations/xxxx_update_payment_notifications_type_enum.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak bisa modify constraint langsung, jadi harus buat tabel baru
        Schema::create('payment_notifications_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders_backup');
            $table->enum('type', ['cashier', 'customer']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Copy data
        DB::table('payment_notifications_new')->insert(
            DB::table('payment_notifications')->get()->map(fn($item) => (array) $item)->toArray()
        );

        // Ganti tabel
        Schema::drop('payment_notifications');
        Schema::rename('payment_notifications_new', 'payment_notifications');
    }

    public function down(): void
    {
        // Kembalikan ke semula
        Schema::create('payment_notifications_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders_backup');
            $table->string('type', ['cashier', 'customer']); // Kembali ke semula
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Copy data
        DB::table('payment_notifications_old')->insert(
            DB::table('payment_notifications')->get()->map(fn($item) => (array) $item)->toArray()
        );

        Schema::drop('payment_notifications');
        Schema::rename('payment_notifications_old', 'payment_notifications');
    }
};
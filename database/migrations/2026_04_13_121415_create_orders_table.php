<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->integer('table_number')->nullable();
            $table->string('session_id')->nullable();
            $table->string('qr_code')->nullable();
            $table->decimal('total_amount', 12, 0);
            $table->string('payment_method'); // cashier, e_wallet, bank_transfer
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->decimal('paid_amount', 12, 0)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('order_status')->default('waiting'); // waiting, processed, ready, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('order_number');
            $table->index('order_status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('session_id');
            $table->index('qr_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
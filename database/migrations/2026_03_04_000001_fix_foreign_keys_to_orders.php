<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // GUNAKAN TRANSACTION AGAR AMAN
        DB::transaction(function () {
            
            // CEK DULU apakah orders_backup masih ada
            if (Schema::hasTable('orders_backup')) {
                // Pindahkan data dari orders_backup ke orders (jika orders kosong)
                $ordersCount = DB::table('orders')->count();
                $backupCount = DB::table('orders_backup')->count();
                
                if ($ordersCount == 0 && $backupCount > 0) {
                    // Copy data dari backup ke orders
                    $backupOrders = DB::table('orders_backup')->get();
                    foreach ($backupOrders as $order) {
                        DB::table('orders')->insert((array) $order);
                    }
                    echo "Data orders dipulihkan dari backup\n";
                }
            }
            
            // 1. Backup data order_items dengan tabel temporary
            if (Schema::hasTable('order_items')) {
                Schema::dropIfExists('order_items_backup');
                DB::statement('CREATE TABLE order_items_backup AS SELECT * FROM order_items');
                
                // 2. Drop tabel order_items
                Schema::dropIfExists('order_items');
                
                // 3. Buat tabel order_items baru dengan foreign key ke orders
                Schema::create('order_items', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                    $table->foreignId('product_id')->constrained('products');
                    $table->string('product_name');
                    $table->decimal('price', 10, 2);
                    $table->integer('quantity');
                    $table->decimal('subtotal', 10, 2);
                    $table->timestamps();
                });
                
                // 4. Masukkan kembali data tanpa memaksa ID
                $backupItems = DB::table('order_items_backup')->get();
                foreach ($backupItems as $item) {
                    DB::table('order_items')->insert([
                        'order_id' => $item->order_id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]);
                }
                
                Schema::dropIfExists('order_items_backup');
            }
            
            // 5. Fix payment_notifications
            if (Schema::hasTable('payment_notifications')) {
                Schema::dropIfExists('payment_notifications_backup');
                DB::statement('CREATE TABLE payment_notifications_backup AS SELECT * FROM payment_notifications');
                
                Schema::dropIfExists('payment_notifications');
                
                Schema::create('payment_notifications', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                    $table->enum('type', ['cashier', 'customer']);
                    $table->text('message');
                    $table->boolean('is_read')->default(false);
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
                
                $backupNotifs = DB::table('payment_notifications_backup')->get();
                foreach ($backupNotifs as $notif) {
                    DB::table('payment_notifications')->insert([
                        'order_id' => $notif->order_id,
                        'type' => $notif->type,
                        'message' => $notif->message,
                        'is_read' => $notif->is_read,
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at,
                        'updated_at' => $notif->updated_at,
                    ]);
                }
                
                Schema::dropIfExists('payment_notifications_backup');
            }
        });
    }

    public function down(): void
    {
        // Rollback ke kondisi sebelumnya
        DB::transaction(function () {
            if (Schema::hasTable('order_items_backup')) {
                Schema::dropIfExists('order_items');
                DB::statement('CREATE TABLE order_items AS SELECT * FROM order_items_backup');
                Schema::dropIfExists('order_items_backup');
            }
            
            if (Schema::hasTable('payment_notifications_backup')) {
                Schema::dropIfExists('payment_notifications');
                DB::statement('CREATE TABLE payment_notifications AS SELECT * FROM payment_notifications_backup');
                Schema::dropIfExists('payment_notifications_backup');
            }
        });
    }
};
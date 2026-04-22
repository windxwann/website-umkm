<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusConstraint extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Matikan foreign key checks untuk SQLite
        DB::statement('PRAGMA foreign_keys=off');
        
        // Cek apakah kolom customer_email ada, jika tidak jangan gunakan
        $columns = DB::select("PRAGMA table_info(orders)");
        $hasCustomerEmail = false;
        foreach ($columns as $column) {
            if ($column->name === 'customer_email') {
                $hasCustomerEmail = true;
                break;
            }
        }
        
        // Buat tabel baru dengan constraint yang benar (tanpa customer_email)
        if ($hasCustomerEmail) {
            DB::statement('
                CREATE TABLE "orders_new" (
                    "id" integer primary key autoincrement,
                    "order_number" varchar not null,
                    "customer_name" varchar not null,
                    "customer_phone" varchar,
                    "customer_email" varchar,
                    "table_number" integer,
                    "session_id" varchar,
                    "qr_code" varchar,
                    "total_amount" decimal not null,
                    "payment_method" varchar not null,
                    "payment_status" varchar not null default "pending",
                    "paid_amount" decimal,
                    "paid_at" datetime,
                    "order_status" varchar not null default "waiting" CHECK(order_status IN ("waiting", "processed", "ready", "completed", "cancelled")),
                    "notes" text,
                    "created_at" datetime,
                    "updated_at" datetime
                )
            ');
            
            DB::statement('
                INSERT INTO orders_new SELECT * FROM orders
            ');
        } else {
            DB::statement('
                CREATE TABLE "orders_new" (
                    "id" integer primary key autoincrement,
                    "order_number" varchar not null,
                    "customer_name" varchar not null,
                    "customer_phone" varchar,
                    "table_number" integer,
                    "session_id" varchar,
                    "qr_code" varchar,
                    "total_amount" decimal not null,
                    "payment_method" varchar not null,
                    "payment_status" varchar not null default "pending",
                    "paid_amount" decimal,
                    "paid_at" datetime,
                    "order_status" varchar not null default "waiting" CHECK(order_status IN ("waiting", "processed", "ready", "completed", "cancelled")),
                    "notes" text,
                    "created_at" datetime,
                    "updated_at" datetime
                )
            ');
            
            // Copy data tanpa kolom customer_email
            DB::statement('
                INSERT INTO orders_new (
                    id, order_number, customer_name, customer_phone,
                    table_number, session_id, qr_code, total_amount, payment_method,
                    payment_status, paid_amount, paid_at, order_status, notes,
                    created_at, updated_at
                )
                SELECT 
                    id, order_number, customer_name, customer_phone,
                    table_number, session_id, qr_code, total_amount, payment_method,
                    payment_status, paid_amount, paid_at, order_status, notes,
                    created_at, updated_at
                FROM orders
            ');
        }
        
        // Drop tabel lama
        DB::statement('DROP TABLE orders');
        
        // Rename tabel baru
        DB::statement('ALTER TABLE orders_new RENAME TO orders');
        
        // Nyalakan kembali foreign key checks
        DB::statement('PRAGMA foreign_keys=on');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys=off');
        
        // Cek struktur tabel saat ini
        $columns = DB::select("PRAGMA table_info(orders)");
        $hasCustomerEmail = false;
        foreach ($columns as $column) {
            if ($column->name === 'customer_email') {
                $hasCustomerEmail = true;
                break;
            }
        }
        
        if ($hasCustomerEmail) {
            DB::statement('
                CREATE TABLE "orders_old" (
                    "id" integer primary key autoincrement,
                    "order_number" varchar not null,
                    "customer_name" varchar not null,
                    "customer_phone" varchar,
                    "customer_email" varchar,
                    "table_number" integer,
                    "session_id" varchar,
                    "qr_code" varchar,
                    "total_amount" decimal not null,
                    "payment_method" varchar not null,
                    "payment_status" varchar not null default "pending",
                    "paid_amount" decimal,
                    "paid_at" datetime,
                    "order_status" varchar not null default "waiting" CHECK(order_status IN ("waiting", "processed", "completed", "cancelled")),
                    "notes" text,
                    "created_at" datetime,
                    "updated_at" datetime
                )
            ');
        } else {
            DB::statement('
                CREATE TABLE "orders_old" (
                    "id" integer primary key autoincrement,
                    "order_number" varchar not null,
                    "customer_name" varchar not null,
                    "customer_phone" varchar,
                    "table_number" integer,
                    "session_id" varchar,
                    "qr_code" varchar,
                    "total_amount" decimal not null,
                    "payment_method" varchar not null,
                    "payment_status" varchar not null default "pending",
                    "paid_amount" decimal,
                    "paid_at" datetime,
                    "order_status" varchar not null default "waiting" CHECK(order_status IN ("waiting", "processed", "completed", "cancelled")),
                    "notes" text,
                    "created_at" datetime,
                    "updated_at" datetime
                )
            ');
        }
        
        DB::statement('INSERT INTO orders_old SELECT * FROM orders');
        DB::statement('DROP TABLE orders');
        DB::statement('ALTER TABLE orders_old RENAME TO orders');
        
        DB::statement('PRAGMA foreign_keys=on');
    }
}
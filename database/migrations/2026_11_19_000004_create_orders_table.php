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
            $table->foreignId('canteen_id')->constrained('canteens')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_type')->nullable();
            $table->text('detail_menu_snapshot');
            $table->unsignedInteger('total_price');
            $table->enum('status', ['Diterima', 'Disiapkan', 'Siap diambil', 'Selesai', 'Dibatalkan'])->default('Diterima');
            $table->enum('payment_method', ['Tunai', 'QRIS']);
            $table->enum('payment_status', ['Belum dibayar', 'Lunas'])->default('Belum dibayar');
            $table->string('access_token')->unique();
            $table->string('checkout_key')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['canteen_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

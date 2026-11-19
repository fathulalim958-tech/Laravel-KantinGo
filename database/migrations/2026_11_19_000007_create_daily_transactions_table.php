<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canteen_id')->constrained('canteens')->onDelete('cascade');
            $table->date('date');
            $table->enum('payment_method', ['Tunai', 'QRIS']);
            $table->unsignedBigInteger('total_revenue')->default(0);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->timestamps();

            $table->unique(['canteen_id', 'date', 'payment_method'], 'canteen_date_method_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_transactions');
    }
};

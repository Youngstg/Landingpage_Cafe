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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            //Info Pelanggan
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('table_number');
            $table->text('notes')->nullable();

            //Info Keuangan
            $table->decimal('subtotal',10,2);
            $table->decimal('total',10,2);

            //Status Pesanan
            $table->enum('status',['pending','confirmed','completed'])
                  ->default('pending');

            //status pembayaran
            $table->string('payment_method')->nullable();
            $table->enum('payment_status',['pending','paid','failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

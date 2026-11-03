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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel orders
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // Foreign Key ke tabel laundry_items
            $table->foreignId('laundry_item_id')
                ->constrained('laundry_items')
                ->onDelete('cascade');

            $table->integer('quantity');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};

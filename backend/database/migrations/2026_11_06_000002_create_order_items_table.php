<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Isi pesanan. Relasi: orders 1 - N order_items, products 1 - N order_items.
     * Nama dan harga disalin saat checkout; kalau produk dihapus, product_id menjadi NULL
     * tetapi riwayat pesanan tetap utuh.
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();

            $table->string('name', 150);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('qty');
            $table->string('img')->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};

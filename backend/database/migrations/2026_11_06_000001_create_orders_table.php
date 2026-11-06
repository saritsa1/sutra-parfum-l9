<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pesanan (fitur tambahan: modul Pesanan).
     * Data pembeli, ongkir, metode bayar, dan total disimpan sebagai "snapshot"
     * supaya riwayat pesanan tidak berubah walau produk atau tarif diubah di kemudian hari.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 20)->unique();

            // Data penerima
            $table->string('customer_name', 100);
            $table->string('phone', 20);
            $table->text('address');

            // Pengiriman
            $table->string('shipping_name', 50);
            $table->string('shipping_est', 30)->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);

            // Pembayaran
            $table->string('payment_id', 30);
            $table->string('payment_name', 80);
            $table->string('payment_type', 20); // va / ewallet / cod
            $table->string('va_number', 30)->nullable();

            // Rincian harga (dihitung server, bukan dari browser)
            $table->decimal('subtotal', 12, 2);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('payment_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->string('status', 30)->default('Menunggu Pembayaran')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

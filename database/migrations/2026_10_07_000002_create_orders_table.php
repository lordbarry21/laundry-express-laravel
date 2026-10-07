<?php

// Mengimpor class Migration dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk konfigurasi kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk mengelola tabel di database
use Illuminate\Support\Facades\Schema;

// Mengembalikan kelas migrasi anonim
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel orders (Header Transaksi)
     */
    public function up(): void
    {
        // Membuat tabel orders sesuai ketentuan modul
        Schema::create('orders', function (Blueprint $table) {
            // Kolom id sebagai primary key bernilai integer auto-increment
            $table->id();
            // Kolom customer_name bertipe varchar untuk mencatat nama lengkap pelanggan
            $table->string('customer_name', 100);
            // Kolom whatsapp_number bertipe varchar untuk nomor kontak WhatsApp
            $table->string('whatsapp_number', 20);
            // Kolom total_price bertipe integer untuk akumulasi biaya keseluruhan pesanan
            $table->integer('total_price')->default(0);
            // Kolom status bertipe enum dengan nilai: Antrean, Dicuci, Selesai (default: Antrean)
            $table->enum('status', ['Antrean', 'Dicuci', 'Selesai'])->default('Antrean');
            // Kolom timestamps untuk otomatis mencatat waktu created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel orders
     */
    public function down(): void
    {
        // Menghapus tabel orders jika migrasi di-rollback
        Schema::dropIfExists('orders');
    }
};

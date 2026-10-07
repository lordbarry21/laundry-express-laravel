<?php

// Mengimpor class Migration dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk konfigurasi skema tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk operasi skema database
use Illuminate\Support\Facades\Schema;

// Mengembalikan kelas migrasi anonim
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel order_details (Detail Transaksi)
     */
    public function up(): void
    {
        // Membuat tabel order_details sesuai ketentuan modul
        Schema::create('order_details', function (Blueprint $table) {
            // Kolom id sebagai primary key baris detail transaksi
            $table->id();
            // Kolom order_id sebagai Foreign Key yang berelasi ke tabel orders dan hapus otomatis saat order dihapus
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            // Kolom service_id sebagai Foreign Key yang berelasi ke tabel services
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            // Kolom qty_or_weight bertipe integer untuk jumlah berat (Kg) atau unit pakaian (Pcs)
            $table->integer('qty_or_weight');
            // Kolom subtotal bertipe integer untuk biaya subtotal per item layanan
            $table->integer('subtotal');
            // Kolom timestamps untuk mencatat created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel order_details
     */
    public function down(): void
    {
        // Menghapus tabel order_details jika migrasi di-rollback
        Schema::dropIfExists('order_details');
    }
};

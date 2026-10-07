<?php

// Mengimpor class Migration dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk merancang struktur tabel database
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk memanipulasi skema basis data
use Illuminate\Support\Facades\Schema;

// Mengembalikan kelas migrasi anonim
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel services (Master Layanan)
     */
    public function up(): void
    {
        // Membuat tabel services sesuai ketentuan modul
        Schema::create('services', function (Blueprint $table) {
            // Kolom id sebagai primary key auto-increment
            $table->id();
            // Kolom service_name bertipe varchar(100) untuk nama paket layanan
            $table->string('service_name', 100);
            // Kolom category bertipe enum dengan pilihan: Kiloan, Satuan, Express
            $table->enum('category', ['Kiloan', 'Satuan', 'Express']);
            // Kolom price_per_unit bertipe integer untuk tarif harga per satuan
            $table->integer('price_per_unit');
            // Kolom unit_type bertipe enum dengan pilihan: Kg, Pcs
            $table->enum('unit_type', ['Kg', 'Pcs']);
            // Kolom description bertipe text untuk penjelasan layanan (bisa kosong)
            $table->text('description')->nullable();
            // Kolom timestamps untuk mencatat created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel services
     */
    public function down(): void
    {
        // Menghapus tabel services dari basis data
        Schema::dropIfExists('services');
    }
};

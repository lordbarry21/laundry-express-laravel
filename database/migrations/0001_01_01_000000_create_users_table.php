<?php

// Mengimpor class Migration dari framework Laravel untuk membuat struktur tabel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk mendefinisikan kolom-kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk mengelola skema database
use Illuminate\Support\Facades\Schema;

// Mengembalikan instance kelas anonim migration
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel users, password_reset_tokens, dan sessions
     */
    public function up(): void
    {
        // Membuat tabel users untuk autentikasi admin dan pengguna sistem
        Schema::create('users', function (Blueprint $table) {
            // Kolom id sebagai primary key bernilai integer auto-increment
            $table->id();
            // Kolom nama lengkap user/admin
            $table->string('name');
            // Kolom username unik untuk login admin (gaya praktis developer junior)
            $table->string('username')->unique();
            // Kolom alamat email user yang bersifat unik
            $table->string('email')->unique();
            // Kolom waktu verifikasi email, boleh null jika belum diverifikasi
            $table->timestamp('email_verified_at')->nullable();
            // Kolom kata sandi yang telah di-hash
            $table->string('password');
            // Kolom remember token untuk fitur ingat saya saat login
            $table->rememberToken();
            // Kolom created_at dan updated_at otomatis dari Laravel
            $table->timestamps();
        });

        // Membuat tabel token untuk reset password pengguna
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Kolom email sebagai primary key
            $table->string('email')->primary();
            // Kolom string untuk menyimpan token acak
            $table->string('token');
            // Waktu kapan token dibuat
            $table->timestamp('created_at')->nullable();
        });

        // Membuat tabel sessions untuk menyimpan sesi login berbasis database
        Schema::create('sessions', function (Blueprint $table) {
            // ID sesi unik sebagai primary key
            $table->string('id')->primary();
            // ID pengguna yang memiliki sesi tersebut jika sudah login
            $table->foreignId('user_id')->nullable()->index();
            // Alamat IP perangkat pengguna
            $table->string('ip_address', 45)->nullable();
            // Identitas browser pengguna
            $table->text('user_agent')->nullable();
            // Data payload sesi yang disimpan
            $table->longText('payload');
            // Waktu aktivitas terakhir pengguna dalam format integer timestamp
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel yang telah dibuat
     */
    public function down(): void
    {
        // Menghapus tabel users jika ada
        Schema::dropIfExists('users');
        // Menghapus tabel password_reset_tokens jika ada
        Schema::dropIfExists('password_reset_tokens');
        // Menghapus tabel sessions jika ada
        Schema::dropIfExists('sessions');
    }
};

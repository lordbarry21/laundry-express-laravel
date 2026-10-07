<?php

// Menentukan namespace lokasi class User berada

namespace App\Models;

// Mengimpor trait HasFactory untuk kemudahan pembuatan data palsu / seeder
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor class Authenticatable dasar untuk fitur login bawaan Laravel
use Illuminate\Foundation\Auth\User as Authenticatable;
// Mengimpor trait Notifiable untuk sistem notifikasi Laravel
use Illuminate\Notifications\Notifiable;

// Mendefinisikan class Model User
class User extends Authenticatable
{
    // Menggunakan trait HasFactory dan Notifiable
    use HasFactory, Notifiable;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
     */
    protected $fillable = [
        // Nama lengkap user / admin
        'name',
        // Username untuk keperluan login admin
        'username',
        // Email user
        'email',
        // Password yang telah di-hash
        'password',
    ];

    /**
     * Kolom-kolom yang disembunyikan saat data model diubah ke bentuk array atau JSON
     */
    protected $hidden = [
        // Menyembunyikan password demi keamanan data
        'password',
        // Menyembunyikan remember token
        'remember_token',
    ];

    /**
     * Menentukan casting tipe data untuk kolom tertentu
     */
    protected function casts(): array
    {
        return [
            // Memastikan email_verified_at diperlakukan sebagai objek waktu Carbon datetime
            'email_verified_at' => 'datetime',
            // Memastikan password di-hash otomatis saat diisi
            'password' => 'hashed',
        ];
    }
}

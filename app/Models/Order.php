<?php

// Menentukan namespace lokasi class Order

namespace App\Models;

// Mengimpor class Model dari Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor trait HasFactory
use Illuminate\Database\Eloquent\Model;
// Mengimpor class relasi HasMany
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mendefinisikan Model Order yang merepresentasikan tabel orders (Header Transaksi)
class Order extends Model
{
    // Mengaktifkan fitur factory
    use HasFactory;

    // Menentukan nama tabel database yang digunakan
    protected $table = 'orders';

    /**
     * Menentukan kolom tabel orders yang dapat diisi secara massal
     */
    protected $fillable = [
        // Nama lengkap pelanggan
        'customer_name',
        // Nomor WhatsApp aktif pelanggan
        'whatsapp_number',
        // Akumulasi total harga transaksi
        'total_price',
        // Status progres pengerjaan: Antrean, Dicuci, Selesai
        'status',
    ];

    /**
     * Mendefinisikan relasi One-to-Many: 1 Order memiliki banyak rincian di tabel order_details
     */
    public function orderDetails(): HasMany
    {
        // Menghubungkan model Order ke model OrderDetail menggunakan foreign key order_id
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}

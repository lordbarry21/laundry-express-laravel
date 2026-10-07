<?php

// Menentukan namespace lokasi class OrderDetail

namespace App\Models;

// Mengimpor class Model dari Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor trait HasFactory
use Illuminate\Database\Eloquent\Model;
// Mengimpor class relasi BelongsTo
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Mendefinisikan Model OrderDetail yang merepresentasikan tabel order_details (Detail Transaksi)
class OrderDetail extends Model
{
    // Mengaktifkan fitur factory
    use HasFactory;

    // Menentukan nama tabel yang digunakan
    protected $table = 'order_details';

    /**
     * Menentukan kolom tabel order_details yang dapat diisi secara massal
     */
    protected $fillable = [
        // ID order yang menjadi induk transaksi
        'order_id',
        // ID layanan yang dipesan
        'service_id',
        // Jumlah perkiraan berat (Kg) atau unit pakaian (Pcs)
        'qty_or_weight',
        // Subtotal biaya untuk baris layanan ini
        'subtotal',
    ];

    /**
     * Mendefinisikan relasi Many-to-One: 1 baris detail milik 1 Order utama
     */
    public function order(): BelongsTo
    {
        // Menghubungkan detail transaksi kembali ke header transaksi (Order)
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Mendefinisikan relasi Many-to-One: 1 baris detail merujuk ke 1 Master Layanan (Service)
     */
    public function service(): BelongsTo
    {
        // Menghubungkan detail transaksi ke data master paket layanan (Service)
        return $this->belongsTo(Service::class, 'service_id');
    }
}

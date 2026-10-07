<?php

// Menentukan namespace lokasi class Service

namespace App\Models;

// Mengimpor class Model dari Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor trait HasFactory
use Illuminate\Database\Eloquent\Model;
// Mengimpor class relasi HasMany
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mendefinisikan Model Service yang merepresentasikan tabel services (Master Layanan)
class Service extends Model
{
    // Mengaktifkan factory untuk model ini
    use HasFactory;

    // Menentukan nama tabel yang dihubungkan ke model ini
    protected $table = 'services';

    /**
     * Menentukan kolom-kolom tabel services yang dapat diisi melalui form (Mass Assignment)
     */
    protected $fillable = [
        // Nama paket layanan (contoh: Cuci Komplit Reguler)
        'service_name',
        // Kategori layanan: Kiloan, Satuan, atau Express
        'category',
        // Tarif harga per unit dalam rupiah
        'price_per_unit',
        // Jenis satuan ukuran: Kg atau Pcs
        'unit_type',
        // Penjelasan deskripsi paket layanan
        'description',
    ];

    /**
     * Mendefinisikan relasi One-to-Many: 1 Layanan bisa dipesan di banyak detail transaksi
     */
    public function orderDetails(): HasMany
    {
        // Menghubungkan model Service dengan model OrderDetail melalui foreign key service_id
        return $this->hasMany(OrderDetail::class, 'service_id');
    }
}

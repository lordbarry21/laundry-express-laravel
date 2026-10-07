<?php

// Menentukan namespace lokasi seeder

namespace Database\Seeders;

// Mengimpor class Seeder dari framework Laravel
use App\Models\Order;
// Mengimpor Model User untuk membuat akun admin
use App\Models\OrderDetail;
// Mengimpor Model Service untuk membuat data awal master layanan
use App\Models\Service;
// Mengimpor Model Order untuk membuat contoh transaksi pesanan
use App\Models\User;
// Mengimpor Model OrderDetail untuk membuat rincian contoh transaksi
use Illuminate\Database\Seeder;
// Mengimpor Facade Hash untuk mengenkripsi kata sandi
use Illuminate\Support\Facades\Hash;

// Mendefinisikan class DatabaseSeeder utama
class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan proses seeder untuk memasukkan data awal ke basis data
     */
    public function run(): void
    {
        // 1. Membuat akun login Administrator (username: admin, password: admin123)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Laundry Express',
                'email' => 'admin@laundry.com',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Daftar paket layanan awal sesuai skenario modul (Kiloan, Satuan, Express)
        $layananAwal = [
            [
                'service_name' => 'Cuci Komplit Reguler (Cuci + Setrika + Lipat)',
                'category' => 'Kiloan',
                'price_per_unit' => 7000,
                'unit_type' => 'Kg',
                'description' => 'Layanan cuci bersih wangi dan disetrika rapi, selesai dalam 2-3 hari kerja.',
            ],
            [
                'service_name' => 'Cuci Kering Lipat Cepat',
                'category' => 'Kiloan',
                'price_per_unit' => 5000,
                'unit_type' => 'Kg',
                'description' => 'Layanan cuci bersih dan dikeringkan rapi tanpa disetrika, selesai 1-2 hari.',
            ],
            [
                'service_name' => 'Cuci Kilat Express (3 Jam Selesai)',
                'category' => 'Express',
                'price_per_unit' => 15000,
                'unit_type' => 'Kg',
                'description' => 'Prioritas cuci kilat khusus untuk kebutuhan mendesak, selesai dalam hitungan 3 jam.',
            ],
            [
                'service_name' => 'Bed Cover Besar / King Size',
                'category' => 'Satuan',
                'price_per_unit' => 25000,
                'unit_type' => 'Pcs',
                'description' => 'Pembersihan mendalam untuk bed cover ukuran besar agar bebas debu dan tungau.',
            ],
            [
                'service_name' => 'Jas / Blazer Dry Cleaning',
                'category' => 'Satuan',
                'price_per_unit' => 35000,
                'unit_type' => 'Pcs',
                'description' => 'Perawatan khusus pakaian berbahan halus/jas formal menggunakan metode dry clean.',
            ],
            [
                'service_name' => 'Selimut Tebal Bulu Halus',
                'category' => 'Satuan',
                'price_per_unit' => 20000,
                'unit_type' => 'Pcs',
                'description' => 'Pencucian selimut tebal dengan pelembut premium menjaga kelembutan kain.',
            ],
        ];

        // Melakukan perulangan untuk memasukkan seluruh master layanan ke tabel services
        foreach ($layananAwal as $srv) {
            Service::firstOrCreate(
                ['service_name' => $srv['service_name']],
                $srv
            );
        }

        // 3. Menambahkan contoh transaksi pesanan awal jika tabel masih kosong
        if (Order::count() === 0) {
            // Membuat pesanan contoh 1
            $order1 = Order::create([
                'customer_name' => 'Siti Rahmawati',
                'whatsapp_number' => '081234567890',
                'total_price' => 49000,
                'status' => 'Dicuci',
            ]);

            // Menambahkan detail pesanan 1
            OrderDetail::create([
                'order_id' => $order1->id,
                'service_id' => 1,
                'qty_or_weight' => 7,
                'subtotal' => 49000,
            ]);

            // Membuat pesanan contoh 2
            $order2 = Order::create([
                'customer_name' => 'Budi Santoso',
                'whatsapp_number' => '089876543210',
                'total_price' => 30000,
                'status' => 'Antrean',
            ]);

            // Menambahkan detail pesanan 2
            OrderDetail::create([
                'order_id' => $order2->id,
                'service_id' => 3,
                'qty_or_weight' => 2,
                'subtotal' => 30000,
            ]);
        }
    }
}

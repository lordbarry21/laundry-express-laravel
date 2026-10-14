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
        // 1. Akun Admin Default untuk Login
        User::updateOrCreate(
            ['email' => 'admin@laundry.com'],
            [
                'name'     => 'Admin Laundry Express',
                'username' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Data Master Layanan
        $this->call(ServiceSeeder::class);

        // 3. Contoh Transaksi Awal untuk Bahan Belajar & Pengujian
        if (Order::count() === 0) {
            $service1 = Service::where('category', 'Kiloan')->first();
            $service2 = Service::where('category', 'Express')->first();

            if ($service1) {
                $order1 = Order::create([
                    'customer_name'   => 'Siti Rahmawati',
                    'whatsapp_number' => '081234567890',
                    'total_price'     => $service1->price_per_unit * 5,
                    'status'          => 'Dicuci',
                ]);

                OrderDetail::create([
                    'order_id'      => $order1->id,
                    'service_id'    => $service1->id,
                    'qty_or_weight' => 5,
                    'subtotal'      => $service1->price_per_unit * 5,
                ]);
            }

            if ($service2) {
                $order2 = Order::create([
                    'customer_name'   => 'Budi Santoso',
                    'whatsapp_number' => '089876543210',
                    'total_price'     => $service2->price_per_unit * 3,
                    'status'          => 'Antrean',
                ]);

                OrderDetail::create([
                    'order_id'      => $order2->id,
                    'service_id'    => $service2->id,
                    'qty_or_weight' => 3,
                    'subtotal'      => $service2->price_per_unit * 3,
                ]);
            }
        }
    }
}

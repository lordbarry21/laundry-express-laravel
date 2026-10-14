<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'service_name'   => 'Cuci Kering Setrika Reguler',
                'category'       => 'Kiloan',
                'price_per_unit' => 7000,
                'unit_type'      => 'Kg',
                'description'    => 'Layanan cuci bersih, kering, dan setrika rapi wangi tahan lama estimasi pengerjaan 2 hari.',
            ],
            [
                'service_name'   => 'Cuci Kilat Express 4 Jam',
                'category'       => 'Express',
                'price_per_unit' => 12000,
                'unit_type'      => 'Kg',
                'description'    => 'Layanan cuci dan setrika kilat selesai dalam 4-6 jam, pakaian siap pakai seketika.',
            ],
            [
                'service_name'   => 'Dry Cleaning Jas / Blazer',
                'category'       => 'Satuan',
                'price_per_unit' => 25000,
                'unit_type'      => 'Pcs',
                'description'    => 'Pembersihan profesional untuk setelan jas, blazer, atau pakaian formal tanpa merusak bahan.',
            ],
            [
                'service_name'   => 'Cuci Bed Cover Jumbo',
                'category'       => 'Satuan',
                'price_per_unit' => 20000,
                'unit_type'      => 'Pcs',
                'description'    => 'Pencucian khusus selimut tebal dan bed cover ukuran besar agar bersih, bebas tungau, dan harum.',
            ],
            [
                'service_name'   => 'Setrika Uap Express',
                'category'       => 'Express',
                'price_per_unit' => 8000,
                'unit_type'      => 'Kg',
                'description'    => 'Jasa setrika uap licin dan rapi untuk pakaian kering yang butuh cepat rapi siap pakai.',
            ],
        ];

        foreach ($services as $item) {
            Service::create($item);
        }
    }
}

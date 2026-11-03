<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaundryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('laundry_items')->insert([
            [
                'name' => 'Cuci Baju Reguler',
                'category' => 'Cuci',
                'price' => 25000,
                'description' => 'Layanan cuci baju reguler.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuci Baju Ekspres',
                'category' => 'Cuci',
                'price' => 28000,
                'description' => 'Layanan cuci baju ekspres.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Setrika Baju Reguler',
                'category' => 'Setrika',
                'price' => 5000,
                'description' => 'Layanan setrika baju reguler.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Setrika Baju Ekspres',
                'category' => 'Setrika',
                'price' => 15000,
                'description' => 'Layanan setrika baju ekspres.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Cuci dan Setrika',
                'category' => 'Paket',
                'price' => 12000,
                'description' => 'Paket layanan cuci dan setrika baju.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

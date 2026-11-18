<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'Admin Laundry',
            'password' => Hash::make('password123'),
        ]);

        if (! $admin->is_admin) {
            $admin->is_admin = true;
            $admin->save();
        }

        if (! Service::exists()) {
            $this->call(ServiceSeeder::class);
        }
    }
}

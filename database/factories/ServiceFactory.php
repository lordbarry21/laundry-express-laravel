<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_name' => fake()->words(3, true),
            'category' => 'Kiloan',
            'unit_type' => 'Kg',
            'price_per_unit' => 25000,
            'description' => 'Layanan cuci baju reguler.',
        ];
    }
}

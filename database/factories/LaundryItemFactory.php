<?php

namespace Database\Factories;

use App\Models\LaundryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaundryItem>
 */
class LaundryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'category' => 'Cuci',
            'price' => 25000,
            'description' => 'Layanan cuci baju reguler.',
            'image' => null,
        ];
    }
}

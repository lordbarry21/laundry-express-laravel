<?php

namespace Database\Factories;

use App\Models\LaundryItem;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'laundry_item_id' => LaundryItem::factory(),
            'quantity' => 1,
            'price' => 25000,
        ];
    }
}

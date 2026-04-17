<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        return [
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'total_amount' => fake()->randomFloat(2, 50, 10000),
            'status' => OrderStatusEnum::PENDING,
            'source' => 'site',
        ];
    }
}
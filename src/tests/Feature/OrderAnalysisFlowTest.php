<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAnalysisFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_and_analyzed(): void
    {
        $user = User::factory()->create();

        $customer = Customer::factory()->create([
            'email' => 'cliente_test@email.com',
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'total_amount' => '6500',
                'source' => 'site',
            ]);

        $response->assertCreated();

        $orderId = $response->json('id') ?? $response->json('data.id');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
        ]);
    }
}
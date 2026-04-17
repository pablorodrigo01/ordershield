<?php

namespace Tests\Feature;

use App\Enums\OrderStatusEnum;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManualActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_approve_order_under_review(): void
    {
        $user = User::factory()->create();

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'status' => OrderStatusEnum::UNDER_REVIEW,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/orders/{$order->id}/approve", [
                'reason' => 'Pedido validado manualmente',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'approved',
        ]);
    }

    public function test_cannot_approve_order_not_under_review(): void
    {
        $user = User::factory()->create();

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'status' => OrderStatusEnum::APPROVED,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/orders/{$order->id}/approve", [
                'reason' => 'Tentativa inválida',
            ]);

        $response->assertStatus(422);
    }
}
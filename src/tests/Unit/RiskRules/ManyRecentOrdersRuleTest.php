<?php

namespace Tests\Unit\RiskRules;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Services\RiskEngine\Rules\ManyRecentOrdersRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManyRecentOrdersRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_result_when_customer_has_many_recent_orders(): void
    {
        Carbon::setTestNow('2026-04-17 12:00:00');

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(10),
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(20),
        ]);

        $currentOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        $rule = new ManyRecentOrdersRule();

        $result = $rule->evaluate($currentOrder);

        $this->assertNotNull($result);
        $this->assertSame(20, $result->score);
        $this->assertSame('Múltiplos pedidos em curto intervalo', $result->reason);
        $this->assertSame('many_recent_orders', $result->code);

        Carbon::setTestNow();
    }

    public function test_returns_null_when_customer_does_not_have_many_recent_orders(): void
    {
        Carbon::setTestNow('2026-04-17 12:00:00');

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(10),
        ]);

        $currentOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        $rule = new ManyRecentOrdersRule();

        $result = $rule->evaluate($currentOrder);

        $this->assertNull($result);

        Carbon::setTestNow();
    }

    public function test_ignores_old_orders_outside_the_time_window(): void
    {
        Carbon::setTestNow('2026-04-17 12:00:00');

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subHours(3),
        ]);

        $currentOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        $rule = new ManyRecentOrdersRule();

        $result = $rule->evaluate($currentOrder);

        $this->assertNull($result);

        Carbon::setTestNow();
    }
}
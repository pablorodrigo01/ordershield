<?php

namespace Tests\Unit\RiskRules;

use App\Models\Customer;
use App\Models\Order;
use App\Services\RiskEngine\Rules\RecentCustomerRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentCustomerRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_result_for_recent_customer(): void
    {
        Carbon::setTestNow('2026-04-17 12:00:00');

        $customer = Customer::factory()->create([
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $order = new Order();
        $order->setRelation('customer', $customer);

        $rule = new RecentCustomerRule();

        $result = $rule->evaluate($order);

        $this->assertNotNull($result);
        $this->assertSame(20, $result->score);
        $this->assertSame('recent_customer', $result->code);

        Carbon::setTestNow();
    }

    public function test_returns_null_for_old_customer(): void
    {
        Carbon::setTestNow('2026-04-17 12:00:00');

        $customer = Customer::factory()->create([
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $order = new Order();
        $order->setRelation('customer', $customer);

        $rule = new RecentCustomerRule();

        $result = $rule->evaluate($order);

        $this->assertNull($result);

        Carbon::setTestNow();
    }

    public function test_returns_null_when_order_has_no_customer(): void
    {
        $order = new Order();

        $rule = new RecentCustomerRule();

        $result = $rule->evaluate($order);

        $this->assertNull($result);
    }
}
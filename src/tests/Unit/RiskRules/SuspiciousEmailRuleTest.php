<?php

namespace Tests\Unit\RiskRules;

use App\Models\Customer;
use App\Models\Order;
use App\Services\RiskEngine\Rules\SuspiciousEmailRule;
use PHPUnit\Framework\TestCase;

class SuspiciousEmailRuleTest extends TestCase
{
    public function test_returns_result_when_amount_is_above_threshold(): void
    {
        $customer = new Customer([
            'email' => 'cliente_test@email.com',
        ]);

        $order = new Order();
        $order->setRelation('customer', $customer);

        $rule = new SuspiciousEmailRule();

        $result = $rule->evaluate($order);

        $this->assertNotNull($result);
        $this->assertSame(15, $result->score);
        $this->assertSame('suspicious_email', $result->code);
    }

    public function test_returns_null_for_normal_email(): void
    {
        $customer = new Customer([
            'email' => 'cliente@email.com'
        ]);

        $order = new Order();
        $order->setRelation('customer', $customer);

        $rule = new SuspiciousEmailRule();

        $result = $rule->evaluate($order);

        $this->assertNull($result);
    }
}

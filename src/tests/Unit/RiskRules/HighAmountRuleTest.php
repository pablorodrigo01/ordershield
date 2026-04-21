<?php

namespace Tests\Unit\RiskRules;

use App\Models\Order;
use App\Services\RiskEngine\Rules\HighAmountRule;
use PHPUnit\Framework\TestCase;

class HighAmountRuleTest extends TestCase
{
    public function test_returns_result_when_amount_is_above_threshold(): void
    {
        $order = new Order([
            'total_amount' => 6500,
        ]);

        $rule = new HighAmountRule();

        $result = $rule->evaluate($order);

        $this->assertNotNull($result);
        $this->assertSame(30, $result->score);
        $this->assertSame('high_amount', $result->code);
    }

    public function test_returns_null_when_amount_is_not_above_threshold(): void
    {
        $order = new Order([
            'total_amount' => 1000,
        ]);

        $rule = new HighAmountRule();

        $result = $rule->evaluate($order);

        $this->assertNull($result);
    }
}

<?php

namespace App\Services\RiskEngine\Rules;

use App\DTOs\RiskRuleResult;
use App\Models\Order;
use App\Services\RiskEngine\Contracts\RiskRuleInterface;
use Carbon\Carbon;

class RecentCustomerRule implements RiskRuleInterface
{
    public function evaluate(Order $order): ?RiskRuleResult
    {
        if (! $order->customer) {
            return null;
        }

        if (! $order->customer->created_at?->greaterThan(Carbon::now()->subDays(7))) {
            return null;
        }

        return new RiskRuleResult(
            score: 20,
            reason: 'Cliente criado recentemente',
            code: 'recent_customer',
        );
    }
}
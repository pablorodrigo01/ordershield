<?php

namespace App\Services\RiskEngine\Rules;

use App\DTOs\RiskRuleResult;
use App\Models\Order;
use App\Services\RiskEngine\Contracts\RiskRuleInterface;

class HighAmountRule implements RiskRuleInterface
{
    public function evaluate(Order $order): ?RiskRuleResult
    {
        if ((float) $order->total_amount <= 5000) {
            return null;
        }

        return new RiskRuleResult(
            score: 30,
            reason: 'Pedido acima de 5000',
            code: 'high_amount',
        );
    }
}
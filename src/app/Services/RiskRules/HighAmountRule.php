<?php

namespace App\Services\RiskRules;

use App\Models\Order;
use App\Services\RiskRules\Contracts\RiskRuleInterface;

class HighAmountRule implements RiskRuleInterface
{
    public function handle(Order $order): ?array
    {
        if ((float) $order->total_amount <= 5000) {
            return null;
        }

        return [
            'score' => 30,
            'reason' => 'Pedido acima de 5000',
        ];
    }
}
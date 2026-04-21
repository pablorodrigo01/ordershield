<?php

namespace App\Services\RiskEngine\Rules;

use App\DTOs\RiskRuleResult;
use App\Models\Order;
use App\Services\RiskEngine\Contracts\RiskRuleInterface;
use Carbon\Carbon;

class ManyRecentOrdersRule implements RiskRuleInterface
{
    public function evaluate(Order $order): ?RiskRuleResult
    {
        $recentOrdersCount = Order::query()
            ->where('customer_id', $order->customer_id)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentOrdersCount < 3) {
            return null;
        }

        return new RiskRuleResult(
            score: 20,
            reason: 'Múltiplos pedidos em curto intervalo',
            code: 'many_recent_orders',
        );
    }
}
<?php

namespace App\Services\RiskRules;

use App\Models\Order;
use App\Services\RiskRules\Contracts\RiskRuleInterface;
use Carbon\Carbon;

class RecentCustomerRule implements RiskRuleInterface
{
    public function handle(Order $order): ?array
    {
        if (!$order->customer()) {
            return null;
        }

        if (!$order->customer->created_at->greaterThan(Carbon::now()->subDays(7))) {
            return null;
        }

        return [
            'score' => 20,
            'reason' => 'Cliente criado recentemente'
        ];
    }
}
<?php

namespace App\Services\RiskRules\Contracts;

use App\Models\Order;

interface RiskRuleInterface
{
    public function handle(Order $order): ?array;
}
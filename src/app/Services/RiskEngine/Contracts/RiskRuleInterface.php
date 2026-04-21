<?php

namespace App\Services\RiskEngine\Contracts;

use App\DTOs\RiskRuleResult;
use App\Models\Order;

interface RiskRuleInterface
{
    public function evaluate(Order $order): ?RiskRuleResult;
}
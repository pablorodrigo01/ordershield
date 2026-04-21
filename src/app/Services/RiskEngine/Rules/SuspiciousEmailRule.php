<?php

namespace App\Services\RiskEngine\Rules;

use App\DTOs\RiskRuleResult;
use App\Models\Order;
use App\Services\RiskEngine\Contracts\RiskRuleInterface;

class SuspiciousEmailRule implements RiskRuleInterface
{
    public function evaluate(Order $order): ?RiskRuleResult
    {
        $email = mb_strtolower($order->customer?->email ?? '');

        if (!$email) {
            return null;
        }

        $patterns = ['test', 'fake', 'spam', 'temp'];

        foreach ($patterns as $pattern) {
            if (str_contains($email, $pattern)) {
                return new RiskRuleResult(
                    score: 15,
                    reason: 'E-mail suspeito',
                    code: 'suspicious_email',
                );
            }
        }

        return null;
    }
}